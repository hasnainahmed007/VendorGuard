<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    use HasFactory;

    #[Fillable(['name', 'type', 'subject', 'body', 'channel', 'is_active'])]
    protected $guarded = [];

    public const TYPES = ['push', 'email', 'in_app', 'push_in_app'];

    public const CHANNELS = ['push', 'in_app', 'push_in_app'];

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
