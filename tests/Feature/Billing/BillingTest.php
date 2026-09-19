<?php

namespace Tests\Feature\Billing;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\BillingService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BillingTest extends TestCase
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

    private function configureStripe(): void
    {
        config()->set('services.stripe', [
            'secret' => 'sk_test_123',
            'webhook_secret' => 'whsec_test_123',
            'price_id' => 'price_123',
        ]);
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    private function signedPayload(array $event): array
    {
        $payload = json_encode($event);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", 'whsec_test_123');

        return [$payload, "t={$timestamp},v1={$signature}"];
    }

    public function test_new_tenant_is_on_trial_without_card(): void
    {
        $owner = $this->makeTenantOwner('trialco');

        $this->assertTrue($owner['tenant']->isOnTrial());
        $this->assertTrue(app(BillingService::class)->isActive($owner['tenant']));

        $this->actingAs($owner['user'])->get(route('tenant.billing.index'))
            ->assertOk()
            ->assertSee('$29/month')
            ->assertSee('trial', true);

        $this->cleanup($owner['tenant']);
    }

    public function test_expired_trial_is_inactive(): void
    {
        $owner = $this->makeTenantOwner('oldco');
        $owner['tenant']->update(['trial_ends_at' => now()->subDay()]);

        $this->assertFalse($owner['tenant']->refresh()->isOnTrial());
        $this->assertFalse(app(BillingService::class)->isActive($owner['tenant']));

        $this->cleanup($owner['tenant']);
    }

    public function test_checkout_redirects_to_stripe(): void
    {
        $owner = $this->makeTenantOwner('payco');
        $this->configureStripe();

        Http::fake([
            'api.stripe.com/*' => Http::response([
                'id' => 'cs_123',
                'url' => 'https://checkout.stripe.com/pay/cs_123',
            ]),
        ]);

        $this->actingAs($owner['user'])
            ->post(route('tenant.billing.checkout'))
            ->assertStatus(303)
            ->assertHeader('Location', 'https://checkout.stripe.com/pay/cs_123');

        $this->cleanup($owner['tenant']);
    }

    public function test_checkout_completed_webhook_activates_tenant(): void
    {
        $owner = $this->makeTenantOwner('hookco');
        $this->configureStripe();

        [$payload, $signature] = $this->signedPayload([
            'id' => 'evt_1',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'customer' => 'cus_123',
                'subscription' => 'sub_123',
                'customer_details' => ['email' => 'hookco@test.example'],
            ]],
        ]);

        $this->postJson(
            route('api.v1.billing.stripe.webhook'),
            json_decode($payload, true),
            ['Stripe-Signature' => $signature]
        )->assertOk()->assertJsonPath('received', true);

        $tenant = $owner['tenant']->refresh();

        $this->assertSame('guardian', $tenant->plan);
        $this->assertSame('cus_123', $tenant->stripe_customer_id);
        $this->assertSame('sub_123', $tenant->stripe_subscription_id);
        $this->assertSame('active', $tenant->subscription_status);
        $this->assertTrue(app(BillingService::class)->isActive($tenant));

        $this->cleanup($owner['tenant']);
    }

    public function test_webhook_rejects_bad_signature(): void
    {
        $owner = $this->makeTenantOwner('sigco');
        $this->configureStripe();

        $this->postJson(
            route('api.v1.billing.stripe.webhook'),
            ['type' => 'checkout.session.completed'],
            ['Stripe-Signature' => 't=123,v1=invalid']
        )->assertForbidden();

        $this->assertSame('trial', $owner['tenant']->refresh()->plan);

        $this->cleanup($owner['tenant']);
    }

    public function test_subscription_deleted_webhook_marks_past_due_state(): void
    {
        $owner = $this->makeTenantOwner('cancelco');
        $this->configureStripe();

        $owner['tenant']->update([
            'plan' => 'guardian',
            'stripe_customer_id' => 'cus_999',
            'stripe_subscription_id' => 'sub_999',
            'subscription_status' => 'active',
        ]);

        [$payload, $signature] = $this->signedPayload([
            'id' => 'evt_2',
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => [
                'id' => 'sub_999',
                'customer' => 'cus_999',
                'status' => 'canceled',
            ]],
        ]);

        $this->postJson(
            route('api.v1.billing.stripe.webhook'),
            json_decode($payload, true),
            ['Stripe-Signature' => $signature]
        )->assertOk();

        $this->assertSame('canceled', $owner['tenant']->refresh()->subscription_status);

        $this->cleanup($owner['tenant']);
    }
}
