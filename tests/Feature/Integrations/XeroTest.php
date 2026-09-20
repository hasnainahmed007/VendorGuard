<?php

namespace Tests\Feature\Integrations;

use App\Jobs\SyncXeroContacts;
use App\Models\Incident;
use App\Models\Integration;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\VendorChangeDetectionService;
use App\Services\Xero\XeroClient;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XeroTest extends TestCase
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

    private function configureXero(): void
    {
        config()->set('services.xero', [
            'client_id' => 'xero-client-id',
            'client_secret' => 'xero-client-secret',
            'redirect' => 'http://localhost:9000/app/integrations/xero/callback',
            'scopes' => 'openid profile email accounting.contacts.read offline_access',
        ]);
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    public function test_connect_redirects_to_xero_with_state(): void
    {
        $owner = $this->makeTenantOwner('xeroco');
        $this->configureXero();

        $response = $this->actingAs($owner['user'])
            ->get(route('tenant.integrations.connect', 'xero'));

        $response->assertRedirect();
        $target = $response->headers->get('Location');

        $this->assertStringStartsWith('https://login.xero.com/identity/connect/authorize', $target);
        $this->assertStringContainsString('client_id=xero-client-id', $target);
        $this->assertNotNull(session('xero_oauth_state'));

        $this->cleanup($owner['tenant']);
    }

    public function test_connect_refuses_when_credentials_missing(): void
    {
        $owner = $this->makeTenantOwner('nocreds');

        config()->set('services.xero.client_id', null);

        $this->actingAs($owner['user'])
            ->get(route('tenant.integrations.connect', 'xero'))
            ->assertRedirect(route('tenant.integrations.index'));

        $this->cleanup($owner['tenant']);
    }

    public function test_callback_connects_every_authorized_organisation(): void
    {
        $owner = $this->makeTenantOwner('syncCo');
        $this->configureXero();

        Http::fake([
            'identity.xero.com/*' => Http::response([
                'access_token' => 'at-123',
                'refresh_token' => 'rt-123',
                'expires_in' => 1800,
            ]),
            'api.xero.com/connections' => Http::response([
                ['id' => 'c1', 'tenantId' => 'org-1', 'tenantType' => 'ORGANISATION'],
                ['id' => 'c2', 'tenantId' => 'org-2', 'tenantType' => 'ORGANISATION'],
            ]),
            'api.xero.com/api.xro/2.0/Contacts*' => Http::response([
                'Contacts' => [
                    ['ContactID' => '10', 'Name' => 'Synced Supplier'],
                ],
            ]),
        ]);

        // Seed the OAuth state the connect step would have stored.
        $this->actingAs($owner['user'])
            ->withSession(['xero_oauth_state' => ['state' => 'state-abc', 'tenant_id' => $owner['tenant']->getKey()]])
            ->get(route('tenant.integrations.callback', [
                'provider' => 'xero',
                'code' => 'auth-code',
                'state' => 'state-abc',
            ]))
            ->assertRedirect(route('tenant.vendors.index'));

        // One login authorizing two orgs creates two integration rows.
        $this->assertSame(
            2,
            Integration::withoutTenancy()
                ->where('tenant_id', $owner['tenant']->getKey())
                ->where('provider', 'xero')
                ->count()
        );

        $integration = Integration::withoutTenancy()
            ->where('tenant_id', $owner['tenant']->getKey())
            ->where('provider', 'xero')
            ->where('external_account_id', 'org-1')
            ->firstOrFail();

        // Tokens are encrypted at rest, transparently decrypted in app.
        $this->assertSame('at-123', $integration->access_token);
        $this->assertNotSame(
            'at-123',
            DB::table('integrations')
                ->where('id', $integration->getKey())
                ->value('access_token')
        );

        // First sync establishes the baseline silently: vendor, no incident.
        (new SyncXeroContacts)->handle(
            app(XeroClient::class),
            app(VendorChangeDetectionService::class)
        );

        $vendor = Vendor::withoutTenancy()
            ->where('tenant_id', $owner['tenant']->getKey())
            ->where('provider', 'xero')
            ->where('external_id', '10')
            ->firstOrFail();

        $this->assertSame('Synced Supplier', $vendor->name);
        $this->assertSame(0, Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->count());
        $this->assertDatabaseHas('integration_sync_logs', [
            'integration_id' => $integration->getKey(),
            'event_type' => 'contact.pull',
            'status' => 'ok',
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_sync_opens_incident_when_bank_details_change(): void
    {
        $owner = $this->makeTenantOwner('chgco');
        $this->configureXero();

        Integration::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'xero',
            'external_account_id' => 'org-9',
            'access_token' => 'at-live',
            'refresh_token' => 'rt-live',
            'expires_at' => now()->addMinutes(20),
            'status' => 'connected',
        ]);

        Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'xero',
            'external_id' => '77',
            'name' => 'Known Vendor',
            'current_bank_last4' => '1111',
        ]);

        Http::fake([
            'api.xero.com/api.xro/2.0/Contacts*' => Http::response([
                'Contacts' => [
                    [
                        'ContactID' => '77',
                        'Name' => 'Known Vendor',
                        'BankAccountDetails' => '9999888877776666',
                    ],
                ],
            ]),
        ]);

        (new SyncXeroContacts)->handle(
            app(XeroClient::class),
            app(VendorChangeDetectionService::class)
        );

        $incident = Incident::withoutTenancy()
            ->where('vendor_id', Vendor::withoutTenancy()
                ->where('tenant_id', $owner['tenant']->getKey())
                ->where('external_id', '77')
                ->value('id'))
            ->firstOrFail();

        $this->assertSame('open', $incident->status);
        $this->assertSame('accounting', $incident->changeLog->source);

        // A second identical poll is idempotent: no second incident.
        (new SyncXeroContacts)->handle(
            app(XeroClient::class),
            app(VendorChangeDetectionService::class)
        );

        $this->assertSame(1, Incident::withoutTenancy()->where('vendor_id', $incident->vendor_id)->count());

        $this->cleanup($owner['tenant']);
    }

    public function test_sync_refreshes_expired_tokens(): void
    {
        $owner = $this->makeTenantOwner('refco');
        $this->configureXero();

        Integration::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'xero',
            'external_account_id' => 'org-3',
            'access_token' => 'at-old',
            'refresh_token' => 'rt-old',
            'expires_at' => now()->subHour(),
            'status' => 'connected',
        ]);

        Http::fake([
            'identity.xero.com/*' => Http::response([
                'access_token' => 'at-new',
                'refresh_token' => 'rt-new',
                'expires_in' => 1800,
            ]),
            'api.xero.com/api.xro/2.0/Contacts*' => Http::response([
                'Contacts' => [],
            ]),
        ]);

        (new SyncXeroContacts)->handle(
            app(XeroClient::class),
            app(VendorChangeDetectionService::class)
        );

        $this->assertSame(
            'at-new',
            Integration::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->value('access_token')
        );

        $this->cleanup($owner['tenant']);
    }

    public function test_accounting_org_limit_stops_at_two(): void
    {
        $owner = $this->makeTenantOwner('limco');
        $this->configureXero();

        foreach (['org-a', 'org-b'] as $org) {
            Integration::create([
                'tenant_id' => $owner['tenant']->getKey(),
                'provider' => 'xero',
                'external_account_id' => $org,
                'access_token' => 'at-x',
                'status' => 'connected',
            ]);
        }

        Http::fake([
            'identity.xero.com/*' => Http::response(['access_token' => 'at-y']),
            'api.xero.com/connections' => Http::response([
                ['id' => 'c3', 'tenantId' => 'org-c', 'tenantType' => 'ORGANISATION'],
            ]),
        ]);

        $this->actingAs($owner['user'])
            ->withSession(['xero_oauth_state' => ['state' => 'state-ok', 'tenant_id' => $owner['tenant']->getKey()]])
            ->get(route('tenant.integrations.callback', [
                'provider' => 'xero',
                'code' => 'auth-code',
                'state' => 'state-ok',
            ]))
            ->assertRedirect(route('tenant.integrations.index'));

        $this->assertSame(
            2,
            Integration::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->count()
        );

        $this->cleanup($owner['tenant']);
    }
}
