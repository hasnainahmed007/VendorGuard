@extends('layouts.superadmin')

@section('title', 'CashPilot — Permissions')

@section('content')
<section id="sec-permissions">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Permissions</h1>
            <p class="text-[13.2px] text-inksoft">Assign roles to users and manage their permissions.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('superadmin.permissions.assign') }}" class="max-w-4xl rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        @csrf
        <div class="flex flex-wrap items-end gap-3 px-5 py-4 border-b border-line dark:border-[#2a2c3d]">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">User</label>
                <select name="user_id" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ ucfirst(str_replace('-', ' ', $user->name)) }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Role</label>
                <select name="role_name" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2.5 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#7c72ff] dark:bg-[#7c72ff] dark:hover:bg-[#9089ff]">Assign role</button>
        </div>
    </form>
    <div class="mt-6 rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead>
                    <tr class="border-b border-line dark:border-[#2a2c3d]">
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">User</th>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Role</th>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Permissions</th>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    @php
                        $userRoles = $user->roles->pluck('name')->toArray();
                        $currentRole = $userRoles[0] ?? null;
                        $rolePermissions = $currentRole ? \Spatie\Permission\Models\Role::where('name', $currentRole)->first()?->permissions->pluck('name')->toArray() ?? [] : [];
                    @endphp
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]">
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle font-semibold text-ink dark:text-[#ededf5]">{{ ucfirst(str_replace('-', ' ', $user->name)) }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle text-[12.8px] text-inksoft dark:text-[#c7c9de]">{{ ucfirst(str_replace('-', ' ', $currentRole ?? '—')) }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle text-[12.8px] text-inksoft dark:text-[#c7c9de]">{{ count($rolePermissions) }} permissions</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">
                            <form method="POST" action="{{ route('superadmin.permissions.remove') }}" class="inline" onsubmit="return confirm('Remove this role from {{ addslashes($user->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-bad dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#f2685c] [&>svg]:size-[14.5px]" title="Remove role">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
