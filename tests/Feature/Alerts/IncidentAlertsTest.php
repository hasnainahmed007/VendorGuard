<?php

namespace Tests\Feature\Alerts;

use App\Mail\IncidentCreatedMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Alerts\WhatsAppSender;
use App\Services\VendorChangeDetectionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FakeWhatsAppSender implements WhatsAppSender
{
    /** @var array<int, array{to: string, message: string}> */
    public static array $sent = [];

    public function send(string $to, string $message): string
    {
        static::$sent[] = ['to' => $to, 'message' => $message];

        return 'fake-1';
    }
}

class IncidentAlertsTest extends TestCase
{
    // DatabaseMigrations (not RefreshDatabase): tenant provisioning runs DDL
    // which cannot live inside a transaction, same as production.
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        FakeWhatsAppSender::$sent = [];
        $this->app->bind(WhatsAppSender::class, FakeWhatsAppSender::class);
    }

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

    public function test_incident_creation_emails_members_and_logs_delivery(): void
    {
        Mail::fake();

        $owner = $this->makeTenantOwner('alertco');

        // A viewer must never be alerted.
        User::create([
            'name' => 'Watcher',
            'email' => 'watcher@test.example',
            'role' => 'viewer',
            'password' => Hash::make('password'),
            'tenant_id' => $owner['tenant']->getKey(),
        ]);

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Alert Payee',
        ]);

        tenancy()->initialize($owner['tenant']);

        app(VendorChangeDetectionService::class)->recordChange($vendor, [
            'field_changed' => 'bank_account',
            'new_value_hash' => 'last4:9999',
            'source' => 'manual',
        ]);

        tenancy()->end();

        // QUEUE_CONNECTION=sync in tests: the queued listener runs inline.
        // IncidentCreatedMail implements ShouldQueue, so it is queued.
        Mail::assertQueued(IncidentCreatedMail::class, function (IncidentCreatedMail $mail) {
            return $mail->hasTo('alertco@test.example');
        });

        Mail::assertNotQueued(IncidentCreatedMail::class, function (IncidentCreatedMail $mail) {
            return $mail->hasTo('watcher@test.example');
        });

        $this->assertDatabaseHas('notifications_log', [
            'tenant_id' => $owner['tenant']->getKey(),
            'channel' => 'email',
            'delivered' => true,
        ]);

        $this->assertSame([], FakeWhatsAppSender::$sent);

        $this->cleanup($owner['tenant']);
    }

    public function test_whatsapp_alert_sent_when_enabled(): void
    {
        Mail::fake();

        $owner = $this->makeTenantOwner('waco');

        $this->actingAs($owner['user'])->put(
            route('tenant.settings.notifications.update'),
            ['whatsapp_enabled' => true, 'whatsapp_to' => '+15550003333']
        )->assertRedirect(route('tenant.settings.index'));

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'WA Payee',
            'verified_phone' => '+15550004444',
        ]);

        tenancy()->initialize($owner['tenant']);

        app(VendorChangeDetectionService::class)->recordChange($vendor, [
            'field_changed' => 'routing_number',
            'new_value_hash' => 'rh-1',
            'source' => 'manual',
        ]);

        tenancy()->end();

        $this->assertCount(1, FakeWhatsAppSender::$sent);
        $this->assertSame('+15550003333', FakeWhatsAppSender::$sent[0]['to']);
        $this->assertStringContainsString('WA Payee', FakeWhatsAppSender::$sent[0]['message']);
        $this->assertStringContainsString('+15550004444', FakeWhatsAppSender::$sent[0]['message']);

        $this->assertDatabaseHas('notifications_log', [
            'tenant_id' => $owner['tenant']->getKey(),
            'channel' => 'whatsapp',
            'delivered' => true,
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_notification_settings_page_and_validation(): void
    {
        $owner = $this->makeTenantOwner('prefco');

        $this->actingAs($owner['user'])->get(route('tenant.settings.index'))
            ->assertOk()
            ->assertSee('Email alerts');

        $this->actingAs($owner['user'])->put(
            route('tenant.settings.notifications.update'),
            ['whatsapp_enabled' => true]
        )->assertRedirect()->assertSessionHasErrors('whatsapp_to');

        $show = $this->actingAs($owner['user'])->put(
            route('tenant.settings.notifications.update'),
            ['email_enabled' => false, 'whatsapp_enabled' => false]
        );

        $show->assertRedirect(route('tenant.settings.index'));

        $this->assertDatabaseHas('notification_settings', [
            'tenant_id' => $owner['tenant']->getKey(),
            'email_enabled' => false,
        ]);

        $this->cleanup($owner['tenant']);
    }
}
