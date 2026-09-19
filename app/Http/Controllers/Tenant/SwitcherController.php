<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Support\TenantAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitcherController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $current = tenancy()->initialized ? tenant()->getTenantKey() : $user->tenant_id;

        $tenants = Tenant::whereIn('id', TenantAccess::accessibleTenantIds($user))
            ->orderBy('id')
            ->get();

        $rows = $tenants->map(fn (Tenant $tenant) => [
            'id' => $tenant->getKey(),
            'name' => $tenant->displayName(),
            'plan' => $tenant->plan,
            'role' => TenantAccess::role($user, $tenant->getKey()),
            'is_current' => $tenant->getKey() === $current,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'current_tenant_id' => $current,
                'tenants' => $rows,
            ]);
        }

        return view('tenant.tenants.index', [
            'currentTenantId' => $current,
            'tenants' => $rows,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $input = $request->validate([
            'tenant_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! TenantAccess::canAccess($user, $input['tenant_id'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden for this tenant.'], Response::HTTP_FORBIDDEN);
            }

            return redirect()->back()->withErrors(['tenant_id' => 'Forbidden for this tenant.']);
        }

        $request->session()->put('tenant_id', $input['tenant_id']);

        if ($request->expectsJson()) {
            return response()->json(['current_tenant_id' => $input['tenant_id']]);
        }

        return redirect()->route('tenant.incidents.index')
            ->with('success', 'Workspace switched.');
    }
}
