<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantInitialized
{
    /**
     * Handle an incoming request.
     *
     * Without an initialized tenant the tenant-scoped models would query
     * unfiltered across every tenant, so resource routes refuse to serve
     * tenantless users instead of leaking cross-tenant data.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenancy()->initialized) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No workspace found for your account. Ask your administrator for an invite.',
                ], 403);
            }

            abort(403, 'No workspace found for your account. Ask your administrator for an invite.');
        }

        return $next($request);
    }
}
