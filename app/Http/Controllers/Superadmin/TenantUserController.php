<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantUserController extends Controller
{
    /**
     * Paginated directory of all tenant users (central mirror, view-only).
     */
    public function index(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'tenant_id' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $users = User::query()
            ->with('tenant')
            ->when($input['search'] ?? null, function ($query, string $search): void {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like));
            })
            ->when($input['tenant_id'] ?? null, fn ($query, string $tenantId) => $query->where('tenant_id', $tenantId))
            ->orderByDesc('id')
            ->paginate($input['per_page'] ?? 15)
            ->through(fn (User $user): array => [
                'id' => $user->getKey(),
                'tenant_id' => $user->tenant_id,
                'tenant_company' => $user->tenant?->company,
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at?->toDateTimeString(),
            ]);

        return response()->json($users);
    }

    /**
     * Headline counts for the users section.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_users' => User::count(),
            'total_tenants' => Tenant::count(),
            'new_users_7d' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }
}
