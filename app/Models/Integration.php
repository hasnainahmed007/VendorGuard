<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable([
    'tenant_id',
    'provider',
    'external_account_id',
    'access_token',
    'refresh_token',
    'expires_at',
    'status',
])]
class Integration extends Model
{
    use BelongsToTenant;

    public const PROVIDERS = ['quickbooks', 'xero', 'gmail', 'outlook'];

    public const ACCOUNTING_PROVIDERS = ['quickbooks', 'xero'];

    public const STATUSES = ['connected', 'expired', 'error'];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<IntegrationSyncLog, $this>
     */
    public function syncLogs(): HasMany
    {
        return $this->hasMany(IntegrationSyncLog::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
