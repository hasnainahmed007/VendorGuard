<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use App\Support\TenantAccess;

class VendorPolicy
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
    public function view(User $user, Vendor $vendor): bool
    {
        return TenantAccess::canAccess($user, $vendor->tenant_id);
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
    public function update(User $user, Vendor $vendor): bool
    {
        return TenantAccess::canManage($user, $vendor->tenant_id);
    }

    /**
     * Determine whether the user can verify the vendor's callback number.
     */
    public function verify(User $user, Vendor $vendor): bool
    {
        return TenantAccess::canManage($user, $vendor->tenant_id);
    }

    /**
     * Determine whether the user can soft-delete the model.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        return TenantAccess::canAdmin($user, $vendor->tenant_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vendor $vendor): bool
    {
        return TenantAccess::canAdmin($user, $vendor->tenant_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * Never allowed: vendors with fraud history are soft-deleted only, for
     * audit/compliance reasons.
     */
    public function forceDelete(User $user, Vendor $vendor): bool
    {
        return false;
    }
}
