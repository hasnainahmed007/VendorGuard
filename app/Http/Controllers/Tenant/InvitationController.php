<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptInvitationRequest;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\TenantUserAccess;
use App\Models\User;
use App\Support\TenantAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    public function index(): View
    {
        $tenantId = tenant()->getTenantKey();

        $members = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $grants = TenantUserAccess::with('user:id,name,email')
            ->orderBy('id')
            ->get();

        $invitations = Invitation::whereNull('accepted_at')
            ->orderByDesc('id')
            ->get(['id', 'email', 'role', 'expires_at']);

        return view('tenant.team.index', [
            'members' => $members,
            'grants' => $grants,
            'invitations' => $invitations,
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $tenantId = tenant()->getTenantKey();

        $existingUser = User::withoutTenancy()->where('email', $input['email'])->first();

        if ($existingUser !== null && ($existingUser->tenant_id === $tenantId
            || TenantUserAccess::withoutTenancy()
                ->where('user_id', $existingUser->getKey())
                ->where('tenant_id', $tenantId)
                ->exists())) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'This user already has access to the tenant.']);
        }

        $pendingExists = Invitation::where('email', $input['email'])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pendingExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'A pending invitation already exists for this email.']);
        }

        Invitation::create([
            'tenant_id' => $tenantId,
            'email' => $input['email'],
            'role' => $input['role'],
            'token' => Invitation::newToken(),
            'invited_by_user_id' => $request->user()->getKey(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()
            ->route('tenant.team.index')
            ->with('success', "Invitation sent to {$input['email']}.");
    }

    public function accept(AcceptInvitationRequest $request): RedirectResponse
    {
        $invitation = Invitation::withoutTenancy()
            ->where('token', $request->validated()['token'])
            ->first();

        if ($invitation === null) {
            abort(404);
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('tenant.tenants.index')
                ->withErrors(['token' => 'Invitation was already accepted.']);
        }

        if ($invitation->isExpired()) {
            return redirect()->route('tenant.tenants.index')
                ->withErrors(['token' => 'Invitation has expired.']);
        }

        $user = $request->user();

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403, 'This invitation belongs to a different email address.');
        }

        DB::transaction(function () use ($invitation, $user) {
            TenantUserAccess::withoutTenancy()->firstOrCreate(
                ['tenant_id' => $invitation->tenant_id, 'user_id' => $user->getKey()],
                [
                    'role' => $invitation->role,
                    'invited_by_user_id' => $invitation->invited_by_user_id,
                    'joined_at' => now(),
                ]
            );

            $invitation->update(['accepted_at' => now()]);
        });

        $request->session()->put('tenant_id', $invitation->tenant_id);

        return redirect()
            ->route('tenant.tenants.index')
            ->with('success', 'Workspace joined.');
    }

    public function revoke(Invitation $invitation): RedirectResponse
    {
        $this->ensureTenantModel($invitation);

        if (! TenantAccess::canManage(request()->user(), $invitation->tenant_id)) {
            abort(403);
        }

        $invitation->delete();

        return redirect()
            ->route('tenant.team.index')
            ->with('success', 'Invitation revoked.');
    }

    public function removeMember(TenantUserAccess $access): RedirectResponse
    {
        $this->ensureTenantModel($access);

        if (! TenantAccess::canAdmin(request()->user(), $access->tenant_id)) {
            abort(403);
        }

        $access->delete();

        return redirect()
            ->route('tenant.team.index')
            ->with('success', 'Member removed.');
    }
}
