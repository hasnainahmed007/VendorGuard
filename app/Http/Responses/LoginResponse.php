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
            return redirect()->to(
                $this->safeIntended($request, config('fortify.home', '/superadmin/dashboard'), '/superadmin')
            );
        }

        return $this->tenantRedirect($request);
    }

    private function safeIntended(Request $request, string $fallback, string $prefix): string
    {
        $intended = (string) $request->session()->pull('url.intended', $fallback);
        $parts = parse_url($intended) ?: [];
        $host = $parts['host'] ?? null;
        $path = $parts['path'] ?? '/';

        if (($host !== null && $host !== $request->getHost()) || ! str_starts_with($path, $prefix)) {
            return $fallback;
        }

        return $intended;
    }

    public static function tenantRedirect(Request $request): Response
    {
        $user = $request->user();
        $fallback = ($user === null || TenantAccess::accessibleTenantIds($user) === [])
            ? route('tenant.tenants.index')
            : '/app/incidents';

        $intended = (new self)->safeIntended($request, $fallback, '/app');

        $response = redirect()->to($intended);

        if ($fallback === route('tenant.tenants.index')) {
            $response->with('success', 'Welcome! Ask your workspace administrator to invite you to a tenant.');
        }

        return $response;
    }
}
