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
    'vendor_id',
    'change_log_id',
    'status',
    'severity',
    'assigned_to_user_id',
    'resolved_at',
    'resolution_note',
])]
class Incident extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const STATUSES = ['open', 'verified', 'blocked', 'dismissed'];

    public const SEVERITIES = ['low', 'medium', 'high', 'critical'];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return BelongsTo<VendorChangeLog, $this>
     */
    public function changeLog(): BelongsTo
    {
        return $this->belongsTo(VendorChangeLog::class, 'change_log_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * @return HasMany<AuditTrail, $this>
     */
    public function auditEntries(): HasMany
    {
        return $this->hasMany(AuditTrail::class)->orderBy('id');
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('status', 'open');
    }

    #[Scope]
    protected function ofStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
