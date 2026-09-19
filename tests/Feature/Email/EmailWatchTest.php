<?php

namespace Tests\Feature\Email;

use App\Jobs\ProcessInboundEmail;
use App\Jobs\SyncGmailMessages;
use App\Models\Incident;
use App\Models\Integration;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Email\EmailClassifier;
use App\Services\Email\GmailClient;
use App\Services\VendorChangeDetectionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailWatchTest extends TestCase
{
    // DatabaseMigrations (not RefreshDatabase): tenant provisioning runs DDL
    // which cannot live inside a transaction, same as production.
    use DatabaseMigrations;

    /**
     * @return array{tenant: Tenant, user: User}
     */
    private function makeTenantOwner(string $prefix): array
    {
        $tenant = Tenant::create([
            'company' => "{$prefix} Inc",
            'email' => "{$prefix}@test.example",
        ]);

        $user = User::create([
            'name' => "{$prefix} Owner",
            'email' => "{$prefix}@test.example",
            'role' => 'owner',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->getKey(),
        ]);

        return ['tenant' => $tenant, 'user' => $user];
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    private function configureGmail(): void
    {
        config()->set('services.gmail', [
            'client_id' => 'gmail-client-id',
            'client_secret' => 'gmail-client-secret',
            'redirect' => 'http://localhost:9000/app/integrations/gmail/callback',
            'scopes' => 'https://www.googleapis.com/auth/gmail.readonly',
        ]);
    }

    private function gmailBody(string $text): string
    {
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }

    public function test_flagged_email_opens_incident_for_matched_vendor(): void
    {
        $owner = $this->makeTenantOwner('mailco');
        $this->configureGmail();

        $integration = Integration::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'gmail',
            'external_account_id' => 'owner@mailco.test',
            'access_token' => 'at-gmail',
            'status' => 'connected',
        ]);

        Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Paper Supplies Co',
        ]);

        Http::fake([
            'gmail.googleapis.com/gmail/v1/users/me/messages?*' => Http::response([
                'messages' => [['id' => 'm1'], ['id' => 'm2']],
            ]),
            'gmail.googleapis.com/gmail/v1/users/me/messages/m1*' => Http::response([
                'id' => 'm1',
                'snippet' => 'our bank details changed',
                'payload' => [
                    'mimeType' => 'text/plain',
                    'headers' => [
                        ['name' => 'Subject', 'value' => 'New bank details for Paper Supplies Co'],
                        ['name' => 'From', 'value' => 'billing@fraud.test'],
                    ],
                    'body' => ['data' => $this->gmailBody('Hi, our bank details have changed. Please update our account urgently.')],
                ],
            ]),
            'gmail.googleapis.com/gmail/v1/users/me/messages/m2*' => Http::response([
                'id' => 'm2',
                'snippet' => 'lunch tomorrow?',
                'payload' => [
                    'mimeType' => 'text/plain',
                    'headers' => [
                        ['name' => 'Subject', 'value' => 'Lunch tomorrow?'],
                        ['name' => 'From', 'value' => 'friend@test.example'],
                    ],
                    'body' => ['data' => $this->gmailBody('Want to grab lunch tomorrow?')],
                ],
            ]),
        ]);

        // QUEUE_CONNECTION=sync in tests: dispatches run inline.
        (new SyncGmailMessages)->handle(app(GmailClient::class));

        $incident = Incident::withoutTenancy()
            ->where('tenant_id', $owner['tenant']->getKey())
            ->first();

        $this->assertNotNull($incident);
        $this->assertSame('open', $incident->status);
        $this->assertSame('email', $incident->changeLog->source);
        $this->assertSame('email_message', $incident->changeLog->field_changed);

        // Exactly one incident: the benign message is ignored.
        $this->assertSame(1, Incident::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->count());

        $this->assertDatabaseHas('integration_sync_logs', [
            'integration_id' => $integration->getKey(),
            'event_type' => 'email.flagged',
        ]);

        // Re-running the poll is idempotent.
        (new SyncGmailMessages)->handle(app(GmailClient::class));

        $this->assertSame(1, Incident::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->count());

        $this->cleanup($owner['tenant']);
    }

    public function test_unmatched_email_is_logged_without_incident(): void
    {
        $owner = $this->makeTenantOwner('nomatch');

        tenancy()->initialize($owner['tenant']);

        (new ProcessInboundEmail(
            tenantId: $owner['tenant']->getKey(),
            vendorId: null,
            subject: 'New bank details',
            body: 'Our bank details changed, please update urgently.',
            from: 'stranger@test.example',
            ref: 'gmail:x:zzz',
            integrationId: null,
        ))->handle(
            app(EmailClassifier::class),
            app(VendorChangeDetectionService::class)
        );

        $this->assertSame(0, Incident::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->count());

        tenancy()->end();

        $this->cleanup($owner['tenant']);
    }

    public function test_new_vendor_with_fraud_note_is_auto_flagged(): void
    {
        $owner = $this->makeTenantOwner('newvend');

        $response = $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'),
            [
                'name' => 'First Timer LLC',
                'invoice_note' => 'Please use this account, not our usual one, here are our new bank details.',
            ]
        );

        $vendor = Vendor::withoutTenancy()->where('name', 'First Timer LLC')->firstOrFail();

        $response->assertRedirect(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]));
        $this->assertGreaterThanOrEqual(50, $vendor->risk_score);

        $this->assertSame(
            1,
            Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->count()
        );

        $this->cleanup($owner['tenant']);
    }

    public function test_new_vendor_with_benign_note_is_not_flagged(): void
    {
        $owner = $this->makeTenantOwner('benignvend');

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'),
            [
                'name' => 'Normal Co',
                'invoice_note' => 'Net 30 payment terms. Please find the invoice attached.',
            ]
        )->assertRedirect();

        $this->assertSame(0, Incident::withoutTenancy()->count());

        $this->cleanup($owner['tenant']);
    }

    public function test_gmail_connect_and_callback(): void
    {
        $owner = $this->makeTenantOwner('gmailco');
        $this->configureGmail();

        $this->actingAs($owner['user'])
            ->get(route('tenant.integrations.connect', 'gmail'))
            ->assertRedirect();

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'access_token' => 'at-g',
                'refresh_token' => 'rt-g',
                'expires_in' => 3600,
            ]),
            'gmail.googleapis.com/gmail/v1/users/me/profile' => Http::response([
                'emailAddress' => 'owner@gmailco.test',
            ]),
            'gmail.googleapis.com/gmail/v1/users/me/messages*' => Http::response(['messages' => []]),
        ]);

        $this->actingAs($owner['user'])
            ->withSession(['gmail_oauth_state' => ['state' => 'state-g', 'tenant_id' => $owner['tenant']->getKey()]])
            ->get(route('tenant.integrations.callback', [
                'provider' => 'gmail',
                'code' => 'auth-code',
                'state' => 'state-g',
            ]))
            ->assertRedirect(route('tenant.integrations.index'));

        $this->assertDatabaseHas('integrations', [
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'gmail',
            'external_account_id' => 'owner@gmailco.test',
        ]);

        $this->cleanup($owner['tenant']);
    }
}
