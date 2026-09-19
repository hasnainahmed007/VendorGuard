<?php

namespace Tests\Feature\Integrations;

use App\Jobs\SyncQuickBooksVendors;
use App\Models\Incident;
use App\Models\Integration;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\QuickBooks\QuickBooksClient;
use App\Services\VendorChangeDetectionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuickBooksTest extends TestCase
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

    private function configureQuickBooks(): void
    {
        config()->set('services.quickbooks', [
            'client_id' => 'qb-client-id',
            'client_secret' => 'qb-client-secret',
            'redirect' => 'http://localhost:9000/app/integrations/quickbooks/callback',
            'sandbox' => true,
            'minor_version' => '75',
            'scopes' => 'com.intuit.quickbooks.accounting',
        ]);
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    public function test_connect_redirects_to_intuit_with_state(): void
    {
        $owner = $this->makeTenantOwner('qbco');
        $this->configureQuickBooks();

        $response = $this->actingAs($owner['user'])
            ->get(route('tenant.integrations.connect', 'quickbooks'));

        $response->assertRedirect();
        $target = $response->headers->get('Location');

        $this->assertStringStartsWith('https://appcenter.intuit.com/connect/oauth2', $target);
        $this->assertStringContainsString('client_id=qb-client-id', $target);
        $this->assertNotNull(session('qb_oauth_state'));

        $this->cleanup($owner['tenant']);
    }

    public function test_connect_refuses_when_credentials_missing(): void
    {
        $owner = $this->makeTenantOwner('nocreds');

        config()->set('services.quickbooks.client_id', null);

        $this->actingAs($owner['user'])
            ->get(route('tenant.integrations.connect', 'quickbooks'))
            ->assertRedirect(route('tenant.integrations.index'));

        $this->cleanup($owner['tenant']);
    }

    public function test_callback_creates_integration_and_syncs_vendors(): void
    {
        $owner = $this->makeTenantOwner('syncCo');
        $this->configureQuickBooks();

        Http::fake([
            'oauth.platform.intuit.com/*' => Http::response([
                'access_token' => 'at-123',
                'refresh_token' => 'rt-123',
                'expires_in' => 3600,
            ]),
            'sandbox-quickbooks.api.intuit.com/*' => Http::response([
                'QueryResponse' => ['Vendor' => [
                    ['Id' => '10', 'DisplayName' => 'Synced Supplier'],
                ]],
            ]),
        ]);

        // Seed the OAuth state the connect step would have stored.
        $this->actingAs($owner['user'])
            ->withSession(['qb_oauth_state' => ['state' => 'state-abc', 'tenant_id' => $owner['tenant']->getKey()]])
            ->get(route('tenant.integrations.callback', [
                'provider' => 'quickbooks',
                'code' => 'auth-code',
                'realmId' => 'realm-1',
                'state' => 'state-abc',
            ]))
            ->assertRedirect(route('tenant.vendors.index'));

        $integration = Integration::withoutTenancy()
            ->where('tenant_id', $owner['tenant']->getKey())
            ->where('provider', 'quickbooks')
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
        (new SyncQuickBooksVendors)->handle(
            app(QuickBooksClient::class),
            app(VendorChangeDetectionService::class)
        );

        $vendor = Vendor::withoutTenancy()
            ->where('tenant_id', $owner['tenant']->getKey())
            ->where('provider', 'quickbooks')
            ->where('external_id', '10')
            ->firstOrFail();

        $this->assertSame('Synced Supplier', $vendor->name);
        $this->assertSame(0, Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->count());
        $this->assertDatabaseHas('integration_sync_logs', [
            'integration_id' => $integration->getKey(),
            'event_type' => 'vendor.pull',
            'status' => 'ok',
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_sync_opens_incident_when_bank_details_change(): void
    {
        $owner = $this->makeTenantOwner('chgco');
        $this->configureQuickBooks();

        $integration = Integration::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'quickbooks',
            'external_account_id' => 'realm-9',
            'access_token' => 'at-live',
            'refresh_token' => 'rt-live',
            'expires_at' => now()->addHour(),
            'status' => 'connected',
        ]);

        Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'quickbooks',
            'external_id' => '77',
            'name' => 'Known Vendor',
            'current_bank_last4' => '1111',
        ]);

        Http::fake([
            'sandbox-quickbooks.api.intuit.com/*' => Http::response([
                'QueryResponse' => ['Vendor' => [
                    [
                        'Id' => '77',
                        'DisplayName' => 'Known Vendor',
                        'BankAccountNumber' => '9999888877776666',
                    ],
                ]],
            ]),
        ]);

        (new SyncQuickBooksVendors)->handle(
            app(QuickBooksClient::class),
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
        (new SyncQuickBooksVendors)->handle(
            app(QuickBooksClient::class),
            app(VendorChangeDetectionService::class)
        );

        $this->assertSame(1, Incident::withoutTenancy()->where('vendor_id', $incident->vendor_id)->count());

        $this->cleanup($owner['tenant']);
    }

    public function test_sync_refreshes_expired_tokens(): void
    {
        $owner = $this->makeTenantOwner('refco');
        $this->configureQuickBooks();

        Integration::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'provider' => 'quickbooks',
            'external_account_id' => 'realm-3',
            'access_token' => 'at-old',
            'refresh_token' => 'rt-old',
            'expires_at' => now()->subHour(),
            'status' => 'connected',
        ]);

        Http::fake([
            'oauth.platform.intuit.com/*' => Http::response([
                'access_token' => 'at-new',
                'refresh_token' => 'rt-new',
                'expires_in' => 3600,
            ]),
            'sandbox-quickbooks.api.intuit.com/*' => Http::response([
                'QueryResponse' => ['Vendor' => []],
            ]),
        ]);

        (new SyncQuickBooksVendors)->handle(
            app(QuickBooksClient::class),
            app(VendorChangeDetectionService::class)
        );

        $this->assertSame(
            'at-new',
            Integration::withoutTenancy()->where('tenant_id', $owner['tenant']->getKey())->value('access_token')
        );

        $this->cleanup($owner['tenant']);
    }

    public function test_accounting_org_limit_is_enforced(): void
    {
        $owner = $this->makeTenantOwner('limco');
        $this->configureQuickBooks();

        foreach (['realm-a', 'realm-b'] as $realm) {
            Integration::create([
                'tenant_id' => $owner['tenant']->getKey(),
                'provider' => 'quickbooks',
                'external_account_id' => $realm,
                'access_token' => 'at-x',
                'status' => 'connected',
            ]);
        }

        Http::fake([
            'oauth.platform.intuit.com/*' => Http::response(['access_token' => 'at-y']),
        ]);

        $this->actingAs($owner['user'])
            ->withSession(['qb_oauth_state' => ['state' => 'state-ok', 'tenant_id' => $owner['tenant']->getKey()]])
            ->get(route('tenant.integrations.callback', [
                'provider' => 'quickbooks',
                'code' => 'auth-code',
                'realmId' => 'realm-c',
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
