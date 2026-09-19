<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable([
    'tenant_id',
    'provider',
    'external_id',
    'name',
    'verified_phone',
    'verified_at',
    'verified_by_user_id',
    'current_bank_last4',
    'current_routing_hash',
    'risk_score',
    'payment_hold',
])]
class Vendor extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const PROVIDERS = ['manual', 'quickbooks', 'xero'];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'risk_score' => 'integer',
            'payment_hold' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * @return HasMany<VendorChangeLog, $this>
     */
    public function changeLogs(): HasMany
    {
        return $this->hasMany(VendorChangeLog::class);
    }

    /**
     * @return HasMany<Incident, $this>
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    #[Scope]
    protected function verified(Builder $query): void
    {
        $query->whereNotNull('verified_at');
    }

    #[Scope]
    protected function unverified(Builder $query): void
    {
        $query->whereNull('verified_at');
    }

    #[Scope]
    protected function paymentHeld(Builder $query): void
    {
        $query->where('payment_hold', true);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null
            && $this->verified_phone !== null;
    }
}
