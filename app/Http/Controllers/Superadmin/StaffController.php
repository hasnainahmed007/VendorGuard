<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:staff.read')->only('index');
        $this->middleware('permission:staff.create')->only(['create', 'store']);
        $this->middleware('permission:staff.edit')->only(['edit', 'update']);
        $this->middleware('permission:staff.delete')->only('destroy');
    }

    public function index(): View
    {
        $staffs = User::whereNotIn('role', ['superadmin', 'tenant'])->get();

        return view('superadmin.staff.index', compact('staffs'));
    }

    public function create(): View
    {
        $roles = Role::where('guard_name', 'web')->get();

        return view('superadmin.staff.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create($request->except('password') + [
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff): View
    {
        $roles = Role::where('guard_name', 'web')->get();

        return view('superadmin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $staff->update($request->except('password') + [
            'password' => $request->filled('password') ? Hash::make($request->password) : $staff->password,
        ]);
        $staff->syncRoles([$request->role]);

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        $staff->syncRoles([]);
        $staff->role = null;
        $staff->save();

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff role removed successfully.');
    }
}
