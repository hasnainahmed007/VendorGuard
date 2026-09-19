<?php

namespace Tests\Feature\Tenant;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\VendorChangeDetectionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantDashboardTest extends TestCase
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

    public function test_incidents_queue_renders_with_callback_number(): void
    {
        $owner = $this->makeTenantOwner('dashco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Dashboard Payee',
            'verified_phone' => '+15550002222',
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($owner['user']);
        $detection = app(VendorChangeDetectionService::class);
        tenancy()->initialize($owner['tenant']);
        $incident = $detection->recordChange($vendor, [
            'field_changed' => 'bank_account',
            'old_value_hash' => 'last4:1111',
            'new_value_hash' => 'last4:2222',
            'source' => 'manual',
        ]);

        $response = $this->actingAs($owner['user'])->get(route('tenant.incidents.index'));

        $response->assertOk()
            ->assertSee('Dashboard Payee')
            ->assertSee('+15550002222')
            ->assertSee('#'.$incident->id, false);

        $detail = $this->actingAs($owner['user'])->get(route('tenant.incidents.show', ['incident' => $incident->getKey()]));

        $detail->assertOk()
            ->assertSee('Trusted callback number')
            ->assertSee('+15550002222')
            ->assertSee('tel:+15550002222', false);

        $this->cleanup($owner['tenant']);
    }

    public function test_vendor_directory_and_wizard_flow(): void
    {
        $owner = $this->makeTenantOwner('wizweb');

        $this->actingAs($owner['user'])->get(route('tenant.vendors.index'))
            ->assertOk()
            ->assertSee('Vendor directory');

        $this->actingAs($owner['user'])->post(route('tenant.vendors.store'), [
            'name' => 'Web Wizard Co',
            'verified_phone' => '+14155550132',
        ])->assertRedirect(route('tenant.vendors.verify', [
            'vendor' => Vendor::withoutTenancy()->where('name', 'Web Wizard Co')->firstOrFail()->getKey(),
        ]));

        $vendor = Vendor::withoutTenancy()->where('name', 'Web Wizard Co')->firstOrFail();

        $this->actingAs($owner['user'])->get(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]))
            ->assertOk()
            ->assertSee('original onboarding record');

        $this->actingAs($owner['user'])->post(route('tenant.vendors.verify.store', ['vendor' => $vendor->getKey()]), [
            'verified_phone' => '+14155550199',
        ])->assertRedirect(route('tenant.vendors.index'));

        $this->assertSame('+14155550199', $vendor->refresh()->verified_phone);

        $this->cleanup($owner['tenant']);
    }

    public function test_audit_trail_page_and_csv_export(): void
    {
        $owner = $this->makeTenantOwner('auditweb');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Audited LLC',
        ]);

        tenancy()->initialize($owner['tenant']);
        app(VendorChangeDetectionService::class)->recordChange($vendor, [
            'field_changed' => 'routing_number',
            'new_value_hash' => 'hash-1',
            'source' => 'manual',
        ]);

        $this->actingAs($owner['user'])->get(route('tenant.audit.index'))
            ->assertOk()
            ->assertSee('incident.opened')
            ->assertSee('Audited LLC');

        $export = $this->actingAs($owner['user'])->get(
            route('tenant.audit.index', ['export' => 'csv'])
        );

        $export->assertOk();
        $this->assertStringContainsString('text/csv', $export->headers->get('Content-Type'));
        $this->assertStringContainsString('incident.opened', $export->streamedContent());

        $this->cleanup($owner['tenant']);
    }
}
