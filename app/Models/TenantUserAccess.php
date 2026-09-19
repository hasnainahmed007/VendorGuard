<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

#[Fillable(['tenant_id', 'user_id', 'role', 'invited_by_user_id', 'joined_at'])]
class TenantUserAccess extends Model
{
    use BelongsToTenant;

    /**
     * The spec names this table tenant_user_access (singular).
     */
    protected $table = 'tenant_user_access';

    public const ROLES = ['owner', 'admin', 'bookkeeper', 'viewer'];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }
}
