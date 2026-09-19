@extends('layouts.superadmin')

@section('title', 'CashPilot — Audit Logs')

@section('content')
<section id="sec-audit-logs">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Audit Logs</h1>
            <p class="text-[13.2px] text-inksoft">A record of every sensitive action taken in the admin panel.</p>
        </div>
    </div>
    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <form method="GET" action="{{ route('superadmin.system.audit-logs.index') }}" class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            <div class="flex min-w-[200px] items-center gap-2 rounded-lg border border-line bg-bg px-3 py-2 text-[12.8px] text-[#9497ab] dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#8d90ac] [&>svg]:size-[14px] [&>svg]:flex-none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="search" value="{{ $search }}" placeholder="Search actions" class="w-full bg-transparent text-[12.8px] text-ink outline-none dark:text-[#ededf5]"></div>
            <select name="staff" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Staff: All</option>
                @foreach($staffs as $staff)
                <option value="{{ $staff->id }}" @selected($staffId === $staff->id)>Staff: {{ $staff->name }}</option>
                @endforeach
            </select>
            <select name="action" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Action: All</option>
                @foreach($actions as $option)
                <option value="{{ $option }}" @selected($action === $option)>Action: {{ $option }}</option>
                @endforeach
            </select>
            @if($search !== '' || $staffId > 0 || $action !== '')
            <a href="{{ route('superadmin.system.audit-logs.index') }}" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">Clear</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Timestamp</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Staff</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Action</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Target</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">IP address</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->created_at->format('j M Y, g:i A') }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->user?->name ?? '—' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->action }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->target ?? '—' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->ip_address ?? '—' }}</td></tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-[13px] text-inksoft">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} log entries</span>
            <div>{{ $logs->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
