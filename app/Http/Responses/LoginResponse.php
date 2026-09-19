<?php

namespace App\Http\Responses;

use App\Support\CentralAccess;
use App\Support\TenantAccess;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): Response
    {
        $user = $request->user();

        if (CentralAccess::isPrivileged($user)) {
            return redirect()->intended(config('fortify.home', '/superadmin/dashboard'));
        }

        return $this->tenantRedirect($request);
    }

    public static function tenantRedirect(Request $request): Response
    {
        $user = $request->user();
        $fallback = ($user === null || TenantAccess::accessibleTenantIds($user) === [])
            ? route('tenant.tenants.index')
            : '/app/incidents';

        $intended = (string) $request->session()->pull('url.intended', $fallback);
        $path = parse_url($intended, PHP_URL_PATH);

        // Never land a tenant user on the central panel they cannot enter.
        if (is_string($path) && str_starts_with($path, '/superadmin')) {
            $intended = $fallback;
        }

        $response = redirect()->to($intended);

        if ($fallback === route('tenant.tenants.index')) {
            $response->with('success', 'Welcome! Ask your workspace administrator to invite you to a tenant.');
        }

        return $response;
    }
}
