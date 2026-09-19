<?php

namespace Tests\Feature\Superadmin;

use App\Jobs\SendTenantNotification;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Notifications\TenantBroadcastNotification;
use App\Services\FcmResult;
use App\Services\FcmSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        foreach (['notifications.read', 'notifications.create', 'notifications.edit', 'notifications.delete'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['role' => 'superadmin']);
        $user->givePermissionTo(['notifications.read', 'notifications.create', 'notifications.edit', 'notifications.delete']);

        return $user;
    }

    public function test_templates_index_renders(): void
    {
        $this->actingAs($this->superadmin())
            ->get(route('superadmin.notifications.templates.index'))
            ->assertOk()
            ->assertSee('Templates');
    }

    public function test_template_store_creates_template(): void
    {
        $this->actingAs($this->superadmin())
            ->post(route('superadmin.notifications.templates.store'), [
                'name' => 'Monthly report ready',
                'type' => 'push_in_app',
                'subject' => 'Sent on the 1st of every month',
                'body' => 'Hi {{name}}, your monthly report is ready.',
                'channel' => 'push_in_app',
                'is_active' => true,
            ])
            ->assertRedirect(route('superadmin.notifications.templates.index'));

        $this->assertDatabaseHas('notification_templates', ['name' => 'Monthly report ready']);
    }

    public function test_template_store_validates_input(): void
    {
        $this->actingAs($this->superadmin())
            ->post(route('superadmin.notifications.templates.store'), ['name' => ''])
            ->assertSessionHasErrors(['name', 'type', 'body', 'channel']);
    }

    public function test_template_update_and_destroy(): void
    {
        $template = NotificationTemplate::create([
            'name' => 'Old name',
            'type' => 'push',
            'body' => 'Old body',
            'channel' => 'push',
            'is_active' => true,
        ]);

        $this->actingAs($this->superadmin())
            ->put(route('superadmin.notifications.templates.update', $template), [
                'name' => 'New name',
                'type' => 'push',
                'body' => 'New body',
                'channel' => 'push',
                'is_active' => false,
            ])
            ->assertRedirect(route('superadmin.notifications.templates.index'));

        $this->assertDatabaseHas('notification_templates', ['id' => $template->id, 'name' => 'New name']);

        $this->actingAs($this->superadmin())
            ->delete(route('superadmin.notifications.templates.destroy', $template))
            ->assertRedirect(route('superadmin.notifications.templates.index'));

        $this->assertDatabaseMissing('notification_templates', ['id' => $template->id]);
    }

    public function test_send_store_creates_one_log_per_recipient(): void
    {
        $this->app->instance(FcmSender::class, new class implements FcmSender
        {
            public function sendToToken(string $token, string $title, string $body, array $data = []): FcmResult
            {
                return FcmResult::success('message-id-1');
            }
        });
        Notification::fake();

        $admin = $this->superadmin();
        $other = User::factory()->create(['role' => 'tenant', 'fcm_token' => 'token-123']);

        $this->actingAs($admin)
            ->post(route('superadmin.notifications.send.store'), [
                'audience' => 'all',
                'title' => 'Hello',
                'message' => 'Broadcast message',
                'channel' => 'in_app',
            ])
            ->assertRedirect(route('superadmin.notifications.logs.index'));

        $this->assertDatabaseHas('notification_logs', ['user_id' => $admin->id, 'title' => 'Hello']);
        $this->assertDatabaseHas('notification_logs', ['user_id' => $other->id, 'title' => 'Hello']);
        Notification::assertSentTimes(TenantBroadcastNotification::class, 2);
    }

    public function test_send_store_validates_input(): void
    {
        $this->actingAs($this->superadmin())
            ->post(route('superadmin.notifications.send.store'), [
                'audience' => 'all',
                'channel' => 'push',
            ])
            ->assertSessionHasErrors(['title', 'message']);
    }

    public function test_logs_index_renders_and_filters_by_status(): void
    {
        $admin = $this->superadmin();
        NotificationLog::create([
            'user_id' => $admin->id,
            'audience' => 'all',
            'title' => 'Failed one',
            'message' => 'Body',
            'channel' => 'push',
            'status' => 'failed',
        ]);
        NotificationLog::create([
            'user_id' => $admin->id,
            'audience' => 'all',
            'title' => 'Sent one',
            'message' => 'Body',
            'channel' => 'push',
            'status' => 'sent',
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.notifications.logs.index', ['status' => 'failed']))
            ->assertOk()
            ->assertSee('Failed one')
            ->assertDontSee('Sent one');
    }

    public function test_invalid_fcm_token_is_cleared(): void
    {
        $user = User::factory()->create(['role' => 'tenant', 'fcm_token' => 'stale-token']);
        $log = NotificationLog::create([
            'user_id' => $user->id,
            'audience' => 'specific',
            'title' => 'Hi',
            'message' => 'Body',
            'channel' => 'push',
            'status' => 'queued',
        ]);

        $sender = new class implements FcmSender
        {
            public function sendToToken(string $token, string $title, string $body, array $data = []): FcmResult
            {
                return FcmResult::failure('Token not found', true);
            }
        };

        (new SendTenantNotification($log->id))->handle($sender);

        $this->assertNull($user->fresh()->fcm_token);
        $this->assertSame('failed', $log->fresh()->status);
    }
}
