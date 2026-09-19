<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.read')->only('index');
        $this->middleware('permission:permissions.edit')->only(['assign', 'remove']);
    }

    public function index(): View
    {
        $users = User::whereNotIn('role', ['superadmin'])->get();
        $roles = Role::where('guard_name', 'web')->get();

        return view('superadmin.permissions.index', compact('users', 'roles'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::where('name', $request->role_name)->firstOrFail();

        $user->syncRoles([$role->name]);
        $user->role = $role->name;
        $user->save();

        return redirect()->route('superadmin.permissions.index')
            ->with('success', 'Role assigned to user successfully.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([]);
        $user->role = null;
        $user->save();

        return redirect()->route('superadmin.permissions.index')
            ->with('success', 'Role removed from user successfully.');
    }
}
