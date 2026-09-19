<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    #[Fillable(['user_id', 'email', 'ip_address', 'user_agent', 'device', 'location', 'result'])]
    protected $guarded = [];

    public const RESULTS = ['success', 'failed'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
