<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Refuse cross-tenant models with a 404, regardless of when route-model
     * binding resolved them: SubstituteBindings runs before tenancy
     * middleware, so an implicitly bound model may come from another
     * tenant. Call this before any Gate check in tenant actions.
     */
    protected function ensureTenantModel(Model $model): void
    {
        $tenantId = $model->getAttribute('tenant_id');

        if (! tenancy()->initialized || $tenantId !== tenant()->getTenantKey()) {
            abort(404);
        }
    }
}
