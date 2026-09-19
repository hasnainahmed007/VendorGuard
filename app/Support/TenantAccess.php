<?php

namespace App\Support;

use App\Models\TenantUserAccess;
use App\Models\User;

class TenantAccess
{
    /**
     * Whether the user may act inside the given tenant: either it is the
     * user's home tenant or a tenant_user_access row grants it.
     */
    public static function canAccess(?User $user, ?string $tenantId): bool
    {
        if ($user === null || $tenantId === null || $tenantId === '') {
            return false;
        }

        if ($user->tenant_id === $tenantId) {
            return true;
        }

        return TenantUserAccess::withoutTenancy()
            ->where('user_id', $user->getKey())
            ->where('tenant_id', $tenantId)
            ->exists();
    }

    /**
     * The user's effective role inside the tenant: the users.role value for
     * the home tenant, otherwise the granted access-row role.
     */
    public static function role(?User $user, ?string $tenantId): ?string
    {
        if ($user === null || $tenantId === null || $tenantId === '') {
            return null;
        }

        if ($user->tenant_id === $tenantId) {
            return $user->role !== null && $user->role !== '' ? $user->role : 'owner';
        }

        $role = TenantUserAccess::withoutTenancy()
            ->where('user_id', $user->getKey())
            ->where('tenant_id', $tenantId)
            ->value('role');

        return is_string($role) ? $role : null;
    }

    /**
     * Whether the user may manage the tenant (invite members, verify
     * vendors, resolve incidents). Viewers are read-only.
     */
    public static function canManage(?User $user, ?string $tenantId): bool
    {
        $role = static::role($user, $tenantId);

        if ($role === null) {
            return false;
        }

        $normalized = strtolower($role);

        return in_array($normalized, ['owner', 'admin', 'super admin'], true)
            || $user?->hasRole('Super Admin') === true;
    }

    /**
     * Whether the user may administer the tenant (delete vendors, manage
     * billing, manage team). Bookkeepers and viewers are excluded.
     */
    public static function canAdmin(?User $user, ?string $tenantId): bool
    {
        $role = static::role($user, $tenantId);

        if ($role === null) {
            return false;
        }

        $normalized = strtolower($role);

        return in_array($normalized, ['owner', 'admin', 'super admin'], true)
            || $user?->hasRole('Super Admin') === true;
    }

    /**
     * Every tenant id the user may switch into: home tenant first, then
     * granted tenants.
     *
     * @return array<int, string>
     */
    public static function accessibleTenantIds(User $user): array
    {
        $ids = [];

        if ($user->tenant_id !== null && $user->tenant_id !== '') {
            $ids[] = $user->tenant_id;
        }

        $granted = TenantUserAccess::withoutTenancy()
            ->where('user_id', $user->getKey())
            ->pluck('tenant_id')
            ->all();

        return array_values(array_unique(array_merge($ids, $granted)));
    }
}
