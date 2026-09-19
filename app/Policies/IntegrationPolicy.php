<?php

namespace App\Policies;

use App\Models\Integration;
use App\Models\User;
use App\Support\TenantAccess;

class IntegrationPolicy
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
    public function view(User $user, Integration $integration): bool
    {
        return TenantAccess::canAccess($user, $integration->tenant_id);
    }

    /**
     * Determine whether the user can connect integrations.
     */
    public function create(User $user): bool
    {
        return tenancy()->initialized
            && TenantAccess::canManage($user, tenant()->getTenantKey());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Integration $integration): bool
    {
        return TenantAccess::canManage($user, $integration->tenant_id);
    }

    /**
     * Determine whether the user can disconnect the integration.
     */
    public function delete(User $user, Integration $integration): bool
    {
        return TenantAccess::canAdmin($user, $integration->tenant_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Integration $integration): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Integration $integration): bool
    {
        return false;
    }
}
