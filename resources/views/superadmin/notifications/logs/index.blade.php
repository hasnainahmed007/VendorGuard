@extends('layouts.superadmin')

@section('title', 'CashPilot — Notification Logs')

@section('content')
<section id="sec-logs">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Notification Logs</h1>
            <p class="text-[13.2px] text-inksoft">Delivery history for every notification sent.</p>
        </div>
    </div>
    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <form method="GET" action="{{ route('superadmin.notifications.logs.index') }}" class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            <div class="flex min-w-[200px] items-center gap-2 rounded-lg border border-line bg-bg px-3 py-2 text-[12.8px] text-[#9497ab] dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#8d90ac] [&>svg]:size-[14px] [&>svg]:flex-none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="search" value="{{ $search }}" placeholder="Search by title or recipient" class="w-full bg-transparent text-[12.8px] text-ink outline-none dark:text-[#ededf5]"></div>
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Status: All</option>
                @foreach($statuses as $option)
                <option value="{{ $option }}" @selected($status === $option)>Status: {{ $option }}</option>
                @endforeach
            </select>
            <select name="channel" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
                <option value="">Channel: All</option>
                @foreach($channels as $option)
                <option value="{{ $option }}" @selected($channel === $option)>Channel: {{ $option }}</option>
                @endforeach
            </select>
            @if($search !== '' || $status !== '' || $channel !== '')
            <a href="{{ route('superadmin.notifications.logs.index') }}" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">Clear</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Title</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Recipient</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Channel</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Sent at</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Status</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle font-semibold text-ink dark:text-[#ededf5]">{{ $log->title }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->user?->name ?? $log->audience }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->channel }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $log->sent_at?->format('j M Y, g:i A') ?? '—' }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">@if(in_array($log->status, ['sent', 'delivered', 'queued'], true))<span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">{{ $log->status }}</span>@elseif($log->status === 'failed')<span class="inline-flex items-center gap-[5px] rounded-full bg-badtint px-2.5 py-1 text-[11.6px] font-bold text-bad dark:bg-[#3a1613] dark:text-[#f2685c]">{{ $log->status }}</span>@else<span class="inline-flex items-center gap-[5px] rounded-full bg-warntint px-2.5 py-1 text-[11.6px] font-bold text-warn dark:bg-[#3a2f10] dark:text-[#e8be55]">{{ $log->status }}</span>@endif</td></tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-[13px] text-inksoft">No notification logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} logs</span>
            <div>{{ $logs->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
