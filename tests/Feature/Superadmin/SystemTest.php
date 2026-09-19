<?php

namespace Tests\Feature\Superadmin;

use App\Models\AuditLog;
use App\Models\LoginActivity;
use App\Models\User;
use App\Services\LocationResolver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SystemTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        foreach (['audit-logs.read', 'login-activities.read', 'notifications.create'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['role' => 'superadmin']);
        $user->givePermissionTo(['audit-logs.read', 'login-activities.read', 'notifications.create']);

        return $user;
    }

    public function test_audit_logs_index_renders_dynamic_rows(): void
    {
        $admin = $this->superadmin();
        AuditLog::create(['user_id' => $admin->id, 'action' => 'Created template', 'target' => 'Welcome', 'method' => 'POST', 'path' => 'superadmin/notifications/templates', 'ip_address' => '127.0.0.1']);

        $this->actingAs($admin)
            ->get(route('superadmin.system.audit-logs.index'))
            ->assertOk()
            ->assertSee('Audit Logs')
            ->assertSee('Created template')
            ->assertSee('Welcome');
    }

    public function test_audit_logs_index_filters_by_action(): void
    {
        $admin = $this->superadmin();
        AuditLog::create(['user_id' => $admin->id, 'action' => 'Created template', 'target' => 'Alpha Target ZZ', 'method' => 'POST', 'path' => 'superadmin/notifications/templates', 'ip_address' => '127.0.0.1']);
        AuditLog::create(['user_id' => $admin->id, 'action' => 'Deleted template', 'target' => 'Beta Target ZZ', 'method' => 'DELETE', 'path' => 'superadmin/notifications/templates/1', 'ip_address' => '127.0.0.1']);

        $this->actingAs($admin)
            ->get(route('superadmin.system.audit-logs.index', ['action' => 'Created template']))
            ->assertOk()
            ->assertSee('Alpha Target ZZ')
            ->assertDontSee('Beta Target ZZ');
    }

    public function test_audit_logs_requires_permission(): void
    {
        Permission::firstOrCreate(['name' => 'dashboard.read', 'guard_name' => 'web']);
        $user = User::factory()->create(['role' => 'staff']);
        $user->givePermissionTo('dashboard.read');

        $this->actingAs($user)
            ->get(route('superadmin.system.audit-logs.index'))
            ->assertForbidden();
    }

    public function test_admin_action_creates_audit_log(): void
    {
        $admin = $this->superadmin();
        Permission::firstOrCreate(['name' => 'notifications.read', 'guard_name' => 'web']);
        $admin->givePermissionTo('notifications.read');

        $this->actingAs($admin)
            ->post(route('superadmin.notifications.templates.store'), [
                'name' => 'Audited template',
                'type' => 'push',
                'body' => 'Body',
                'channel' => 'push',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'Created template',
            'target' => 'Audited template',
        ]);
    }

    public function test_login_activity_index_renders_dynamic_rows(): void
    {
        $admin = $this->superadmin();
        LoginActivity::create(['user_id' => $admin->id, 'email' => $admin->email, 'result' => 'success', 'device' => 'Chrome · Windows', 'ip_address' => '127.0.0.1']);
        LoginActivity::create(['user_id' => $admin->id, 'email' => $admin->email, 'result' => 'failed', 'device' => 'Safari · macOS', 'ip_address' => '127.0.0.1']);

        $this->actingAs($admin)
            ->get(route('superadmin.system.login-activities.index'))
            ->assertOk()
            ->assertSee('Login Activity')
            ->assertSee('Chrome · Windows');
    }

    public function test_login_activity_filters_by_result(): void
    {
        $admin = $this->superadmin();
        $other = User::factory()->create(['role' => 'tenant', 'name' => 'Zzz Filter Check']);
        LoginActivity::create(['user_id' => $other->id, 'email' => $other->email, 'result' => 'success', 'device' => 'Chrome · Windows', 'ip_address' => '127.0.0.1']);
        LoginActivity::create(['user_id' => $admin->id, 'email' => $admin->email, 'result' => 'failed', 'device' => 'Safari · macOS', 'ip_address' => '127.0.0.1']);

        $this->actingAs($admin)
            ->get(route('superadmin.system.login-activities.index', ['result' => 'failed']))
            ->assertOk()
            ->assertSee('Safari · macOS')
            ->assertDontSee('Zzz Filter Check');
    }

    public function test_login_activity_requires_permission(): void
    {
        Permission::firstOrCreate(['name' => 'dashboard.read', 'guard_name' => 'web']);
        $user = User::factory()->create(['role' => 'staff']);
        $user->givePermissionTo('dashboard.read');

        $this->actingAs($user)
            ->get(route('superadmin.system.login-activities.index'))
            ->assertForbidden();
    }

    public function test_login_and_failed_events_are_recorded_with_location(): void
    {
        $this->app->instance(LocationResolver::class, new class implements LocationResolver
        {
            public function resolve(?string $ip): ?string
            {
                return 'Dhaka, Bangladesh';
            }
        });

        $user = User::factory()->create(['role' => 'tenant']);

        event(new Login('web', $user, false));
        event(new Failed('web', null, ['email' => 'ghost@example.com']));

        // Exactly one row per event: the listener must be registered once.
        $this->assertDatabaseCount('login_activities', 2);
        $this->assertDatabaseHas('login_activities', [
            'user_id' => $user->id,
            'result' => 'success',
            'location' => 'Dhaka, Bangladesh',
        ]);
        $this->assertDatabaseHas('login_activities', [
            'email' => 'ghost@example.com',
            'result' => 'failed',
            'location' => 'Dhaka, Bangladesh',
        ]);
    }
}
