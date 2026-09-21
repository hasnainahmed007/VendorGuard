<?php

namespace Tests\Feature\Tenant;

use App\Models\Tenant;
use App\Models\TenantUserAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TenantAccessControlTest extends TestCase
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

    private function makeCentralUser(string $prefix, ?string $role): User
    {
        return User::create([
            'name' => "{$prefix} User",
            'email' => "{$prefix}@test.example",
            'role' => $role,
            'password' => Hash::make('password'),
        ]);
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    public function test_tenant_owner_login_lands_on_tenant_dashboard(): void
    {
        $owner = $this->makeTenantOwner('loginco');

        $this->post('/login', [
            'email' => 'loginco@test.example',
            'password' => 'password',
        ])->assertRedirect('/app/incidents');

        $this->actingAs($owner['user'])->get(route('tenant.incidents.index'))
            ->assertOk();

        $this->cleanup($owner['tenant']);
    }

    public function test_tenant_user_can_never_open_superadmin_routes(): void
    {
        $owner = $this->makeTenantOwner('lockedco');

        $this->actingAs($owner['user'])->get('/superadmin/dashboard')->assertForbidden();
        $this->actingAs($owner['user'])->get('/superadmin/users')->assertForbidden();
        $this->actingAs($owner['user'])->get('/superadmin/system/audit-logs')->assertForbidden();

        // The previously ungated directory endpoints are closed too.
        $this->actingAs($owner['user'])->get('/superadmin/tenant-users')->assertForbidden();
        $this->actingAs($owner['user'])->get('/superadmin/tenant-users/stats')->assertForbidden();

        $this->cleanup($owner['tenant']);
    }

    public function test_super_admin_role_login_lands_on_central_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.read', 'guard_name' => 'web']);

        $admin = $this->makeCentralUser('bossco', 'superadmin');
        $admin->assignRole('Super Admin');
        $admin->givePermissionTo('dashboard.read');

        $this->post('/login', [
            'email' => 'bossco@test.example',
            'password' => 'password',
        ])->assertRedirect('/superadmin/dashboard');

        $this->actingAs($admin)->get('/superadmin/dashboard')->assertOk();
    }

    public function test_manager_role_login_lands_on_central_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'dashboard.read', 'guard_name' => 'web']);

        $manager = $this->makeCentralUser('managco', 'manager');
        $manager->assignRole('Manager');
        $manager->givePermissionTo('dashboard.read');

        $this->post('/login', [
            'email' => 'managco@test.example',
            'password' => 'password',
        ])->assertRedirect('/superadmin/dashboard');

        $this->actingAs($manager)->get('/superadmin/dashboard')->assertOk();
    }

    public function test_privileged_login_ignores_stored_tenant_url(): void
    {
        $admin = $this->makeCentralUser('storedco', 'admin');

        // A stale tenant page (e.g. bookmark, expired session) must not drag
        // a superadmin into a workspace they do not belong to.
        $this->withSession(['url.intended' => 'http://localhost/app/incidents'])
            ->post('/login', [
                'email' => 'storedco@test.example',
                'password' => 'password',
            ])->assertRedirect('/superadmin/dashboard');
    }

    public function test_tenant_login_ignores_stored_central_url(): void
    {
        $owner = $this->makeTenantOwner('centralco');

        $this->withSession(['url.intended' => 'http://localhost/superadmin/dashboard'])
            ->post('/login', [
                'email' => 'centralco@test.example',
                'password' => 'password',
            ])->assertRedirect('/app/incidents');

        $this->cleanup($owner['tenant']);
    }

    public function test_tenantless_user_gets_switcher_and_no_workspace(): void
    {
        $user = $this->makeCentralUser('lonelyco', 'tenant');

        $this->post('/login', [
            'email' => 'lonelyco@test.example',
            'password' => 'password',
        ])->assertRedirect(route('tenant.tenants.index'));

        // With no workspace the resource routes refuse instead of leaking.
        $this->actingAs($user)->get('/app/incidents')->assertForbidden();
        $this->actingAs($user)->get('/app/vendors')->assertForbidden();

        // The switcher itself renders an empty state.
        $this->actingAs($user)->get('/app/tenants')
            ->assertOk()
            ->assertSee('No workspaces yet');
    }

    public function test_bookkeeper_with_grant_reaches_granted_workspace_only(): void
    {
        $ownerA = $this->makeTenantOwner('w1co');
        $ownerB = $this->makeTenantOwner('w2co');

        TenantUserAccess::create([
            'tenant_id' => $ownerA['tenant']->getKey(),
            'user_id' => $ownerB['user']->getKey(),
            'role' => 'bookkeeper',
            'joined_at' => now(),
        ]);

        // Bookkeeper's own login still lands on the tenant dashboard.
        $this->post('/login', [
            'email' => 'w2co@test.example',
            'password' => 'password',
        ])->assertRedirect('/app/incidents');

        // And the central panel stays closed to them.
        $this->actingAs($ownerB['user'])->get('/superadmin/dashboard')->assertForbidden();

        $this->cleanup($ownerA['tenant'], $ownerB['tenant']);
    }
}
