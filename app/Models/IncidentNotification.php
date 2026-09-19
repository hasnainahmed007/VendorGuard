<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Spec table notifications_log: one row per incident alert attempt.
 * (The existing NotificationLog/notification_logs model serves the
 * FCM push system and is left untouched.)
 */
#[Fillable(['tenant_id', 'incident_id', 'channel', 'sent_at', 'delivered'])]
class IncidentNotification extends Model
{
    use BelongsToTenant;

    protected $table = 'notifications_log';

    public const CHANNELS = ['email', 'whatsapp'];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
