<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable(['tenant_id', 'incident_id', 'user_id', 'action', 'note'])]
class AuditTrail extends Model
{
    use BelongsToTenant;

    protected $table = 'audit_trail';

    public const ACTIONS = [
        'incident.opened',
        'incident.verified',
        'incident.blocked',
        'incident.dismissed',
        'incident.note',
        'incident.assigned',
        'vendor.verified',
    ];

    /**
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
