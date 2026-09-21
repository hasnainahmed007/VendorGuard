<?php

use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantAccess;

if (! function_exists('tenant_layout_data')) {
    function tenant_layout_data(?User $user): array
    {
        if ($user === null) {
            return ['switcherTenants' => collect(), 'currentTenant' => null];
        }

        $ids = TenantAccess::accessibleTenantIds($user);

        return [
            'switcherTenants' => $ids === []
                ? collect()
                : Tenant::whereIn('id', $ids)->orderBy('id')->get(),
            'currentTenant' => tenancy()->initialized ? tenant() : null,
        ];
    }
}
