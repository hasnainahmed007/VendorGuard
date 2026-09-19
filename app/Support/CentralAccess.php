<?php

namespace App\Support;

use App\Models\User;

/**
 * Decides which dashboard a web user belongs to.
 *
 * Privileged (central-panel) users: Spatie Super Admin / Admin / Manager
 * roles, or the central staff role values in users.role. Everyone else —
 * tenant owners, bookkeepers, viewers — belongs to the tenant dashboard.
 */
final class CentralAccess
{
    /**
     * @var array<int, string>
     */
    private const PRIVILEGED_ROLES = ['super admin', 'admin', 'manager'];

    /**
     * @var array<int, string>
     */
    private const PRIVILEGED_USER_COLUMNS = ['superadmin', 'super admin', 'admin', 'manager', 'staff'];

    public static function isPrivileged(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
            return true;
        }

        $column = strtolower((string) ($user->role ?? ''));

        return in_array($column, self::PRIVILEGED_USER_COLUMNS, true);
    }

    public static function isTenantUser(?User $user): bool
    {
        return $user !== null && ! self::isPrivileged($user);
    }
}
