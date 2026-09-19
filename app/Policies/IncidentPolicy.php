<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use App\Support\TenantAccess;

class IncidentPolicy
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
    public function view(User $user, Incident $incident): bool
    {
        return TenantAccess::canAccess($user, $incident->tenant_id);
    }

    /**
     * Determine whether the user can action the incident (verify, block,
     * dismiss, request info, assign).
     */
    public function transition(User $user, Incident $incident): bool
    {
        return TenantAccess::canManage($user, $incident->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Incident $incident): bool
    {
        return TenantAccess::canAdmin($user, $incident->tenant_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Incident $incident): bool
    {
        return TenantAccess::canAdmin($user, $incident->tenant_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * Never allowed: incidents are compliance records.
     */
    public function forceDelete(User $user, Incident $incident): bool
    {
        return false;
    }
}
