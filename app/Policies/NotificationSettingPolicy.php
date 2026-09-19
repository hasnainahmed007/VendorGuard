<?php

namespace App\Policies;

use App\Models\NotificationSetting;
use App\Models\User;
use App\Support\TenantAccess;

class NotificationSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return tenancy()->initialized
            && TenantAccess::canAccess($user, tenant()->getTenantKey());
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NotificationSetting $notificationSetting): bool
    {
        return TenantAccess::canAccess($user, $notificationSetting->tenant_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return tenancy()->initialized
            && TenantAccess::canManage($user, tenant()->getTenantKey());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationSetting $notificationSetting): bool
    {
        return TenantAccess::canManage($user, $notificationSetting->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NotificationSetting $notificationSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NotificationSetting $notificationSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NotificationSetting $notificationSetting): bool
    {
        return false;
    }
}
