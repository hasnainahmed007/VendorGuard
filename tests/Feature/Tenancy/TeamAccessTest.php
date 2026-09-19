<?php

namespace Tests\Feature\Tenancy;

use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\TenantUserAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeamAccessTest extends TestCase
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

    public function test_owner_can_invite_from_team_page(): void
    {
        $owner = $this->makeTenantOwner('acme');

        $this->actingAs($owner['user'])
            ->get(route('tenant.team.index'))
            ->assertOk()
            ->assertSee('Invite a team member');

        $this->actingAs($owner['user'])->post(route('tenant.team.invitations.store'), [
            'email' => 'keeper@test.example',
            'role' => 'bookkeeper',
        ])->assertRedirect(route('tenant.team.index'));

        $this->assertDatabaseHas('invitations', [
            'tenant_id' => $owner['tenant']->getKey(),
            'email' => 'keeper@test.example',
            'role' => 'bookkeeper',
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_duplicate_pending_invitation_is_rejected(): void
    {
        $owner = $this->makeTenantOwner('dupco');

        $payload = ['email' => 'keeper@test.example', 'role' => 'bookkeeper'];

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'), $payload
        )->assertRedirect(route('tenant.team.index'));

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'), $payload
        )->assertRedirect()->assertSessionHasErrors('email');

        $this->cleanup($owner['tenant']);
    }

    public function test_viewer_cannot_invite(): void
    {
        $owner = $this->makeTenantOwner('viewco', 'viewer');

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'),
            ['email' => 'keeper@test.example', 'role' => 'bookkeeper']
        )->assertForbidden();

        $this->cleanup($owner['tenant']);
    }

    public function test_invited_user_accepts_via_link_and_gains_access(): void
    {
        $owner = $this->makeTenantOwner('hostco');
        $guest = $this->makeTenantOwner('guestco');

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'),
            ['email' => 'guestco@test.example', 'role' => 'bookkeeper']
        )->assertRedirect(route('tenant.team.index'));

        $token = Invitation::withoutTenancy()->where('email', 'guestco@test.example')->value('token');
        $this->assertIsString($token);

        $accept = $this->actingAs($guest['user'])
            ->get(route('tenant.invitations.accept', ['token' => $token]));

        $accept->assertRedirect(route('tenant.tenants.index'));

        $this->assertDatabaseHas('tenant_user_access', [
            'tenant_id' => $owner['tenant']->getKey(),
            'user_id' => $guest['user']->getKey(),
            'role' => 'bookkeeper',
        ]);

        $this->assertNotNull(Invitation::withoutTenancy()->where('token', $token)->value('accepted_at'));

        // Accepting twice is rejected.
        $this->actingAs($guest['user'])
            ->get(route('tenant.invitations.accept', ['token' => $token]))
            ->assertRedirect()
            ->assertSessionHasErrors('token');

        $this->cleanup($owner['tenant'], $guest['tenant']);
    }

    public function test_accept_rejects_wrong_email_expired_and_unknown_tokens(): void
    {
        $owner = $this->makeTenantOwner('tokenco');
        $guest = $this->makeTenantOwner('otherco');

        $this->actingAs($guest['user'])
            ->get(route('tenant.invitations.accept', ['token' => str_repeat('a', 64)]))
            ->assertNotFound();

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'),
            ['email' => 'otherco@test.example', 'role' => 'viewer']
        )->assertRedirect(route('tenant.team.index'));

        $stranger = $this->makeTenantOwner('strangerco');

        $invitation = Invitation::withoutTenancy()->where('email', 'otherco@test.example')->firstOrFail();

        $this->actingAs($stranger['user'])
            ->get(route('tenant.invitations.accept', ['token' => $invitation->token]))
            ->assertForbidden();

        $invitation->update(['expires_at' => now()->subDay()]);

        $this->actingAs($guest['user'])
            ->get(route('tenant.invitations.accept', ['token' => $invitation->token]))
            ->assertRedirect()
            ->assertSessionHasErrors('token');

        $this->cleanup($owner['tenant'], $guest['tenant'], $stranger['tenant']);
    }

    public function test_owner_can_revoke_invitation_and_remove_member(): void
    {
        $owner = $this->makeTenantOwner('firmco');
        $guest = $this->makeTenantOwner('leaverco');

        $this->actingAs($owner['user'])->post(
            route('tenant.team.invitations.store'),
            ['email' => 'gone@test.example', 'role' => 'viewer']
        )->assertRedirect(route('tenant.team.index'));

        $invitation = Invitation::withoutTenancy()->where('email', 'gone@test.example')->firstOrFail();

        $this->actingAs($owner['user'])->delete(
            route('tenant.team.invitations.revoke', ['invitation' => $invitation->getKey()])
        )->assertRedirect(route('tenant.team.index'));

        $this->assertDatabaseMissing('invitations', ['id' => $invitation->getKey()]);

        $access = TenantUserAccess::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'user_id' => $guest['user']->getKey(),
            'role' => 'bookkeeper',
            'joined_at' => now(),
        ]);

        // Viewers inside this workspace cannot revoke or remove.
        $viewer = $this->makeTenantOwner('spectco', 'viewer');

        TenantUserAccess::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'user_id' => $viewer['user']->getKey(),
            'role' => 'viewer',
            'joined_at' => now(),
        ]);

        $viewerSession = ['tenant_id' => $owner['tenant']->getKey()];

        $this->actingAs($viewer['user'])->withSession($viewerSession)->delete(
            route('tenant.team.members.remove', ['access' => $access->getKey()])
        )->assertForbidden();

        $this->actingAs($owner['user'])->delete(
            route('tenant.team.members.remove', ['access' => $access->getKey()])
        )->assertRedirect(route('tenant.team.index'));

        $this->assertDatabaseMissing('tenant_user_access', ['id' => $access->getKey()]);

        $this->cleanup($owner['tenant'], $guest['tenant'], $viewer['tenant']);
    }

    public function test_switcher_lists_and_switches_granted_tenants(): void
    {
        $owner = $this->makeTenantOwner('homeco');
        $guest = $this->makeTenantOwner('awayco');

        TenantUserAccess::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'user_id' => $guest['user']->getKey(),
            'role' => 'bookkeeper',
            'joined_at' => now(),
        ]);

        $index = $this->actingAs($guest['user'])->getJson(route('tenant.tenants.index'));

        $index->assertOk()
            ->assertJsonPath('current_tenant_id', $guest['tenant']->getKey())
            ->assertJsonCount(2, 'tenants');

        // Unauthorized tenant switch is rejected.
        $stranger = $this->makeTenantOwner('farco');

        $this->actingAs($guest['user'])->postJson(route('tenant.tenants.switch'), [
            'tenant_id' => $stranger['tenant']->getKey(),
        ])->assertForbidden();

        // Granted tenant switch works and persists in session.
        $switch = $this->actingAs($guest['user'])->postJson(route('tenant.tenants.switch'), [
            'tenant_id' => $owner['tenant']->getKey(),
        ]);

        $switch->assertOk()->assertJsonPath('current_tenant_id', $owner['tenant']->getKey());

        $this->cleanup($owner['tenant'], $guest['tenant'], $stranger['tenant']);
    }
}
