@extends('tenant.layout')

@section('title', 'VendorGuard — Audit trail')

@section('content')
<section id="sec-audit">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Audit trail</h1>
            <p class="text-[13.2px] text-inksoft">Who reviewed what, when, and what action was taken — exportable for auditors.</p>
        </div>
        <a href="{{ route('tenant.audit.index', ['export' => 'csv'] + request()->only('action')) }}" class="rounded-lg border border-line bg-panel px-4 py-2.5 text-[13px] font-semibold">Export CSV</a>
    </div>
    <div class="rounded-[10px] border border-line bg-panel">
        <form method="GET" action="{{ route('tenant.audit.index') }}" class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4">
            <select name="action" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px]">
                <option value="">Action: All</option>
                @foreach($actions as $option)
                <option value="{{ $option }}" @selected($actionFilter === $option)>Action: {{ $option }}</option>
                @endforeach
            </select>
            @if($actionFilter !== '')
            <a href="{{ route('tenant.audit.index') }}" class="rounded-lg border border-line px-3 py-2 text-[12.6px]">Clear</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Time</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Incident</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Vendor</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">User</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Action</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Note</th></tr></thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:hover:bg-white/5">
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $entry->created_at->format('j M Y, g:i A') }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">@if($entry->incident_id)<a href="{{ route('tenant.incidents.show', ['incident' => $entry->incident_id]) }}" class="font-semibold underline">#{{ $entry->incident_id }}</a>@else—@endif</td>
                        <td class="px-5 py-[13px] align-middle">{{ $entry->incident?->vendor?->name ?? '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $entry->user?->name ?? 'system' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $entry->action }}</td>
                        <td class="px-5 py-[13px] align-middle">{{ $entry->note ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-[13px] text-inksoft">No audit entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ $entries->total() }}</span>
            <div>{{ $entries->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
