<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable([
    'tenant_id',
    'vendor_id',
    'field_changed',
    'old_value_hash',
    'new_value_hash',
    'source',
    'raw_source_ref',
    'detected_at',
])]
class VendorChangeLog extends Model
{
    use BelongsToTenant;

    public const SOURCES = ['manual', 'accounting', 'email'];

    public const BANK_FIELDS = ['bank_account', 'routing_number'];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
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
     * @return HasOne<Incident, $this>
     */
    public function incident(): HasOne
    {
        return $this->hasOne(Incident::class, 'change_log_id');
    }

    public function isBankDetailChange(): bool
    {
        return in_array($this->field_changed, self::BANK_FIELDS, true);
    }
}
