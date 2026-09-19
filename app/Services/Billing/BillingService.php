<?php

namespace App\Services\Billing;

use App\Models\Tenant;

class BillingService
{
    public const TRIAL_DAYS = 14;

    public const ACCOUNTING_ORG_LIMIT = 2;

    public function __construct(private StripeClient $stripe) {}

    /**
     * Whether the tenant may use the product: active subscription or
     * an unexpired trial.
     */
    public function isActive(Tenant $tenant): bool
    {
        if (in_array($tenant->subscription_status, ['active', 'trialing'], true)) {
            return true;
        }

        return $tenant->isOnTrial();
    }

    /**
     * Apply a verified Stripe webhook event to the matching tenant.
     */
    public function handleWebhookEvent(array $event): ?Tenant
    {
        $type = $event['type'] ?? null;
        $object = $event['data']['object'] ?? [];

        if (! is_string($type) || ! is_array($object)) {
            return null;
        }

        return match ($type) {
            'checkout.session.completed' => $this->applyCheckoutCompleted($object),
            'customer.subscription.updated',
            'customer.subscription.deleted' => $this->applySubscriptionChanged($object),
            default => null,
        };
    }

    private function applyCheckoutCompleted(array $session): ?Tenant
    {
        $customerId = $session['customer'] ?? null;
        $subscriptionId = $session['subscription'] ?? null;
        $customerEmail = $session['customer_details']['email'] ?? $session['customer_email'] ?? null;

        if (! is_string($customerId) || ! is_string($customerEmail)) {
            return null;
        }

        $tenant = Tenant::where('email', $customerEmail)->first();

        if ($tenant === null) {
            return null;
        }

        $tenant->update([
            'plan' => 'guardian',
            'stripe_customer_id' => $customerId,
            'stripe_subscription_id' => is_string($subscriptionId) ? $subscriptionId : null,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);

        return $tenant->refresh();
    }

    private function applySubscriptionChanged(array $subscription): ?Tenant
    {
        $customerId = $subscription['customer'] ?? null;

        if (! is_string($customerId)) {
            return null;
        }

        $tenant = Tenant::where('stripe_customer_id', $customerId)->first();

        if ($tenant === null) {
            return null;
        }

        $status = $subscription['status'] ?? null;

        $tenant->update([
            'stripe_subscription_id' => (string) ($subscription['id'] ?? $tenant->stripe_subscription_id),
            'subscription_status' => is_string($status) ? $status : $tenant->subscription_status,
        ]);

        return $tenant->refresh();
    }
}
