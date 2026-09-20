@extends('tenant.layout')

@section('title', 'VendorGuard — Incidents')

@section('content')
<section id="sec-incidents">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Incidents</h1>
            <p class="text-[13.2px] text-inksoft">Every payment-detail change, held until verified by callback.</p>
        </div>
    </div>
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach($statuses as $tab)
        <a href="{{ route('tenant.incidents.index', ['status' => $tab]) }}" class="rounded-lg border px-3.5 py-2 text-[12.8px] font-semibold {{ $status === $tab ? 'border-tenant bg-tenant hover:bg-tenantdark text-white' : 'border-line bg-panel text-inksoft' }}">{{ ucfirst($tab) }} ({{ $counts[$tab] ?? 0 }})</a>
        @endforeach
    </div>
    <div class="rounded-[10px] border border-line bg-panel">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">ID</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Vendor</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Change</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Severity</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Callback number</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Detected</th></tr></thead>
                <tbody>
                    @forelse($incidents as $incident)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd]">
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">#{{ $incident->id }}</td>
                        <td class="px-5 py-[13px] align-middle"><a href="{{ route('tenant.incidents.show', ['incident' => $incident->getKey()]) }}" class="font-semibold underline">{{ $incident->vendor->name }}</a>@if($incident->vendor->payment_hold) <span class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-[11px] font-bold text-red-700">HELD</span>@endif</td>
                        <td class="px-5 py-[13px] align-middle">{{ $incident->changeLog?->field_changed ?? '—' }} <span class="text-inksoft">via {{ $incident->changeLog?->source ?? '—' }}</span></td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ ucfirst($incident->severity) }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle font-mono">{{ $incident->vendor->verified_phone ?? 'not verified' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $incident->created_at->format('j M Y, g:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-[13px] text-inksoft">No {{ $status }} incidents.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incidents->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $incidents->firstItem() }}–{{ $incidents->lastItem() }} of {{ $incidents->total() }}</span>
            <div>{{ $incidents->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
