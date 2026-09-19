<?php

namespace App\Models;

use App\Services\Billing\BillingService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

#[Fillable(['company', 'email', 'data', 'name', 'plan', 'stripe_customer_id', 'stripe_subscription_id', 'subscription_status', 'trial_ends_at'])]
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * @var array<string, string>
     */
    protected $attributes = [
        'plan' => 'trial',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'company',
            'data',
            'name',
            'plan',
            'stripe_customer_id',
            'stripe_subscription_id',
            'subscription_status',
            'trial_ends_at',
        ];
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public function displayName(): string
    {
        return $this->name ?? $this->company ?? $this->id;
    }

    public function isOnTrial(): bool
    {
        if ($this->plan !== 'trial') {
            return false;
        }

        // Registration never sets trial_ends_at, so a missing value means
        // "14 days from signup" via created_at.
        $endsAt = $this->trial_ends_at
            ?? $this->created_at?->copy()->addDays(BillingService::TRIAL_DAYS);

        return $endsAt === null || $endsAt->isFuture();
    }
}
