@extends('layouts.superadmin')

@section('title', 'CashPilot — Login Activity')

@section('content')
<section id="sec-login-activity">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Login Activity</h1>
            <p class="text-[13.2px] text-inksoft">Recent sign-ins across users and staff accounts.</p>
        </div>
    </div>
    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <form method="GET" action="{{ route('superadmin.system.login-activities.index') }}" class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            <div class="flex min-w-[200px] items-center gap-2 rounded-lg border border-line bg-bg px-3 py-2 text-[12.8px] text-[#9497ab] dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#8d90ac] [&>svg]:size-[14px] [&>svg]:flex-none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="search" value="{{ $search }}" placeholder="Search by user" class="w-full bg-transparent text-[12.8px] text-ink outline-none dark:text-[#ededf5]"></div>
            <select name="result" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Result: All</option>
                @foreach($results as $option)
                <option value="{{ $option }}" @selected($result === $option)>Result: {{ $option }}</option>
                @endforeach
            </select>
            <select name="device" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Device: All</option>
                @foreach($devices as $option)
                <option value="{{ $option }}" @selected($device === $option)>Device: {{ $option }}</option>
                @endforeach
            </select>
            @if($search !== '' || $result !== '' || $device !== '')
            <a href="{{ route('superadmin.system.login-activities.index') }}" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">Clear</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">User</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Device</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Location</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">IP address</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Time</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Result</th></tr></thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle font-semibold text-ink dark:text-[#ededf5]">{{ $activity->user?->name ?? $activity->email ?? 'Unknown' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $activity->device ?? 'Unknown' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $activity->location ?? '—' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $activity->ip_address ?? '—' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $activity->created_at->diffForHumans() }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">@if($activity->result === 'success')<span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Success</span>@else<span class="inline-flex items-center gap-[5px] rounded-full bg-badtint px-2.5 py-1 text-[11.6px] font-bold text-bad dark:bg-[#3a1613] dark:text-[#f2685c]">Failed</span>@endif</td></tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-[13px] text-inksoft">No login activity found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $activities->firstItem() }}–{{ $activities->lastItem() }} of {{ $activities->total() }} login events</span>
            <div>{{ $activities->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
