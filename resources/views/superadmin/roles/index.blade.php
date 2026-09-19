@extends('layouts.superadmin')

@section('title', 'CashPilot — Roles')

@section('content')
<section id="sec-roles">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Roles</h1>
            <p class="text-[13.2px] text-inksoft">Manage staff roles and their permissions.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.roles.create') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#7c72ff] dark:bg-[#7c72ff] dark:hover:bg-[#9089ff] [&>svg]:size-[14px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>New role</a>
        </div>
    </div>
    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Role</th>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Permissions</th>
                        <th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]">
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#6e62f2] text-[11px] font-bold text-white">{{ strtoupper(substr($role->name, 0, 2)) }}</div><div class="font-semibold text-ink dark:text-[#ededf5]">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</div></div></td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle text-[12.8px] text-inksoft dark:text-[#c7c9de]">{{ $role->permissions->count() }} permissions</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">
                            <div class="flex gap-1.5">
                                <a href="{{ route('superadmin.roles.edit', $role) }}" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                                <form method="POST" action="{{ route('superadmin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-bad dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#f2685c] [&>svg]:size-[14.5px]" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
