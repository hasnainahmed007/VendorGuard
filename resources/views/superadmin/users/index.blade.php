@extends('layouts.superadmin')

@section('title', 'CashPilot — Users')

@section('content')
<section id="sec-users">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Users</h1>
            <p class="text-[13.2px] text-inksoft">Every user across all tenant databases (read-only mirror).</p>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]"><div id="tenantUsersTotal" class="mb-[3px] text-[23px] font-bold tracking-tight">–</div><div class="text-[12.6px] text-inksoft">Total tenant users</div></div>
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]"><div id="tenantUsersTenants" class="mb-[3px] text-[23px] font-bold tracking-tight">–</div><div class="text-[12.6px] text-inksoft">Tenants</div></div>
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]"><div id="tenantUsersNew" class="mb-[3px] text-[23px] font-bold tracking-tight">–</div><div class="text-[12.6px] text-inksoft">New (7 days)</div></div>
    </div>

    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <div class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            <div class="flex min-w-[200px] items-center gap-2 rounded-lg border border-line bg-bg px-3 py-2 text-[12.8px] text-[#9497ab] dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#8d90ac] [&>svg]:size-[14px] [&>svg]:flex-none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input id="tenantUsersSearch" placeholder="Search by name or email" class="w-full bg-transparent text-[12.8px] text-ink outline-none dark:text-[#ededf5]"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Tenant</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">User</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Email</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Joined</th></tr></thead>
                <tbody id="tenantUsersBody">
                    <tr><td colspan="4" class="px-5 py-8 text-center text-[13px] text-inksoft">Loading…</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span id="tenantUsersRange">–</span>
            <div class="flex gap-1.5"><button type="button" id="tenantUsersPrev" class="rounded-[7px] border border-line bg-panel px-3 py-1.5 text-xs text-inksoft dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">‹ Prev</button><button type="button" id="tenantUsersNext" class="rounded-[7px] border border-line bg-panel px-3 py-1.5 text-xs text-inksoft dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">Next ›</button></div>
        </div>
    </div>
</section>
@endsection
