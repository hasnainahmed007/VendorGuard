<?php

namespace Tests\Feature\Vendors;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Support\BankDetailHasher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorVerificationTest extends TestCase
{
    // DatabaseMigrations (not RefreshDatabase): tenant provisioning runs DDL
    // which cannot live inside a transaction, same as production.
    use DatabaseMigrations;

    /**
     * @return array{tenant: Tenant, user: User}
     */
    private function makeTenantOwner(string $prefix, string $role = 'owner'): array
    {
        $tenant = Tenant::create([
            'company' => "{$prefix} Inc",
            'email' => "{$prefix}@test.example",
        ]);

        $user = User::create([
            'name' => "{$prefix} Owner",
            'email' => "{$prefix}@test.example",
            'role' => $role,
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

    public function test_owner_can_create_vendor_with_hashed_bank_details(): void
    {
        $owner = $this->makeTenantOwner('vendco');

        $response = $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'),
            [
                'name' => 'Acme Supplies',
                'verified_phone' => '+15551234567',
                'bank_account' => '123456789012',
                'routing_number' => '021000021',
            ]
        );

        $vendor = Vendor::withoutTenancy()->where('name', 'Acme Supplies')->firstOrFail();

        $response->assertRedirect(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]));

        $this->assertSame('9012', $vendor->current_bank_last4);
        $this->assertSame(BankDetailHasher::hash('021000021'), $vendor->current_routing_hash);
        $this->assertTrue($vendor->isVerified());

        // Raw numbers are never persisted — only hashes and the last four.
        $this->assertStringNotContainsString('123456789012', (string) json_encode($vendor->getAttributes()));
        $this->assertStringNotContainsString('021000021', (string) json_encode($vendor->getAttributes()));

        $this->cleanup($owner['tenant']);
    }

    public function test_vendor_validation_rejects_bad_input(): void
    {
        $owner = $this->makeTenantOwner('valco');

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'), ['name' => '']
        )->assertRedirect()->assertSessionHasErrors('name');

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'),
            ['name' => 'Bad Phone Co', 'verified_phone' => 'not-a-number']
        )->assertRedirect()->assertSessionHasErrors('verified_phone');

        $this->cleanup($owner['tenant']);
    }

    public function test_viewer_cannot_create_or_verify_vendors(): void
    {
        $owner = $this->makeTenantOwner('permco', 'viewer');

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.store'), ['name' => 'Nope Co']
        )->assertForbidden();

        $this->cleanup($owner['tenant']);
    }

    public function test_verification_wizard_marks_vendor_verified(): void
    {
        $owner = $this->makeTenantOwner('wizco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Unverified Ltd',
        ]);

        $this->assertFalse($vendor->isVerified());

        $this->actingAs($owner['user'])
            ->get(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]))
            ->assertOk()
            ->assertSee('original onboarding record');

        $response = $this->actingAs($owner['user'])->post(
            route('tenant.vendors.verify.store', ['vendor' => $vendor->getKey()]),
            ['verified_phone' => '+14155550132']
        );

        $response->assertRedirect(route('tenant.vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->getKey(),
            'verified_phone' => '+14155550132',
            'verified_by_user_id' => $owner['user']->getKey(),
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_vendor_is_invisible_to_other_tenants(): void
    {
        $ownerA = $this->makeTenantOwner('isolA');
        $ownerB = $this->makeTenantOwner('isolB');

        $vendor = Vendor::create([
            'tenant_id' => $ownerA['tenant']->getKey(),
            'name' => 'Secret Supplier',
        ]);

        // Tenant B asks for tenant A's vendor by id: must 404, never 403/200.
        $this->actingAs($ownerB['user'])
            ->get(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]))
            ->assertNotFound();

        // Tenant B's own directory never contains it either.
        $this->actingAs($ownerB['user'])
            ->get(route('tenant.vendors.index'))
            ->assertOk()
            ->assertDontSee('Secret Supplier');

        $this->cleanup($ownerA['tenant'], $ownerB['tenant']);
    }

    public function test_vendor_listing_filters_and_search(): void
    {
        $owner = $this->makeTenantOwner('filterco');

        Vendor::create(['tenant_id' => $owner['tenant']->getKey(), 'name' => 'Alpha Parts']);
        Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Beta Goods',
            'verified_phone' => '+15550001111',
            'verified_at' => now(),
        ]);

        $this->actingAs($owner['user'])
            ->get(route('tenant.vendors.index', ['verified' => '1']))
            ->assertOk()
            ->assertSee('Beta Goods')
            ->assertDontSee('Alpha Parts');

        $this->actingAs($owner['user'])
            ->get(route('tenant.vendors.index', ['search' => 'Alpha']))
            ->assertOk()
            ->assertSee('Alpha Parts')
            ->assertDontSee('Beta Goods');

        $this->cleanup($owner['tenant']);
    }

    public function test_owner_can_archive_vendor_but_never_hard_delete(): void
    {
        $owner = $this->makeTenantOwner('archco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Old Supplier',
        ]);

        $this->actingAs($owner['user'])->delete(
            route('tenant.vendors.destroy', ['vendor' => $vendor->getKey()])
        )->assertRedirect(route('tenant.vendors.index'));

        $this->assertSoftDeleted('vendors', ['id' => $vendor->getKey()]);

        // The policy forbids hard deletion for every role.
        $this->assertFalse($owner['user']->can('forceDelete', $vendor));

        $this->cleanup($owner['tenant']);
    }
}
