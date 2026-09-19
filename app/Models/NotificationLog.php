<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    #[Fillable(['notification_template_id', 'user_id', 'audience', 'title', 'message', 'channel', 'status', 'fcm_message_id', 'error', 'sent_at'])]
    protected $guarded = [];

    public const STATUSES = ['queued', 'sent', 'delivered', 'failed', 'partial'];

    public const CHANNELS = ['push', 'in_app', 'push_in_app'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
