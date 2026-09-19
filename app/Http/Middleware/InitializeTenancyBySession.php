<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyBySession
{
    /**
     * Handle an incoming request.
     *
     * Resolves the tenant from the user's own session (set by the workspace
     * switcher), falling back to the user's home tenant. No tenant
     * identifier travels in URLs: the model-level tenant scope then
     * guarantees every query only ever sees this tenant's rows.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        // Always re-resolve: the session is authoritative per request, so a
        // stale initialization (long-lived contexts, tests) self-heals.
        // initialize() is a no-op when the tenant is already current.
        $tenantId = $request->session()->get('tenant_id', $user->tenant_id);

        if (! is_string($tenantId) || ! TenantAccess::canAccess($user, $tenantId)) {
            $tenantId = $user->tenant_id;
            $request->session()->put('tenant_id', $tenantId);
        }

        if (is_string($tenantId) && $tenantId !== '') {
            $tenant = Tenant::find($tenantId);

            if ($tenant !== null) {
                tenancy()->initialize($tenant);
            }
        }

        return $next($request);
    }
}
