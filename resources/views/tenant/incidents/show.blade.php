@extends('tenant.layout')

@section('title', "VendorGuard — Incident #{$incident->id}")

@section('content')
<section id="sec-incident-detail">
    <div class="mb-6">
        <a href="{{ route('tenant.incidents.index') }}" class="text-[12.8px] font-medium text-inksoft underline">← Back to queue</a>
        <h1 class="mb-1 mt-2 text-xl font-bold tracking-tight">Incident #{{ $incident->id }} — {{ $incident->vendor->name }}</h1>
        <p class="text-[13.2px] text-inksoft">Status: <strong>{{ ucfirst($incident->status) }}</strong> · Severity: <strong>{{ ucfirst($incident->severity) }}</strong> · Detected {{ $incident->created_at->format('j M Y, g:i A') }}</p>
    </div>
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="mb-4 rounded-[10px] border border-line bg-panel p-5">
                <h2 class="mb-2 text-[14px] font-bold">What changed</h2>
                <dl class="grid grid-cols-2 gap-2 text-[13px]">
                    <dt class="text-inksoft">Field</dt><dd class="font-medium">{{ $incident->changeLog?->field_changed ?? '—' }}</dd>
                    <dt class="text-inksoft">Source</dt><dd class="font-medium">{{ $incident->changeLog?->source ?? '—' }}</dd>
                    <dt class="text-inksoft">Detected at</dt><dd class="font-medium">{{ $incident->changeLog?->detected_at?->format('j M Y, g:i A') ?? '—' }}</dd>
                    <dt class="text-inksoft">Payment hold</dt><dd class="font-medium">{{ $incident->vendor->payment_hold ? 'HELD — do not pay' : 'Released' }}</dd>
                </dl>
            </div>
            <div class="rounded-[10px] border border-line bg-panel p-5">
                <h2 class="mb-2 text-[14px] font-bold">Audit trail</h2>
                <ul class="space-y-2 text-[13px]">
                    @forelse($incident->auditEntries as $entry)
                    <li><span class="text-inksoft">{{ $entry->created_at->format('j M Y, g:i A') }}</span> — <strong>{{ $entry->action }}</strong> by {{ $entry->user?->name ?? 'system' }}@if($entry->note)<br><span class="text-inksoft">{{ $entry->note }}</span>@endif</li>
                    @empty
                    <li class="text-inksoft">No audit entries yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div>
            <div class="mb-4 rounded-[10px] border-2 border-ink bg-panel p-5">
                <h2 class="mb-1 text-[14px] font-bold">Trusted callback number</h2>
                <p class="mb-3 text-[12.5px] text-inksoft">Call this number — never the number in the change request.</p>
                @if($incident->vendor->verified_phone)
                <a href="tel:{{ $incident->vendor->verified_phone }}" class="block rounded-lg bg-tenant hover:bg-tenantdark px-4 py-3 text-center font-mono text-[16px] font-bold text-white">Call {{ $incident->vendor->verified_phone }}</a>
                <p class="mt-2 text-[12px] text-inksoft">Verified {{ $incident->vendor->verified_at?->format('j M Y') ?? '—' }}</p>
                @else
                <p class="rounded-lg bg-red-50 px-4 py-3 text-[13px] font-semibold text-red-700">No verified number on file. Verify this vendor before releasing payment.</p>
                <a href="{{ route('tenant.vendors.verify', ['vendor' => $incident->vendor->getKey()]) }}" class="mt-2 block rounded-lg border border-line px-4 py-2.5 text-center text-[13px] font-semibold">Open verification wizard</a>
                @endif
            </div>
            @if($incident->isOpen())
            <div class="rounded-[10px] border border-line bg-panel p-5">
                <h2 class="mb-3 text-[14px] font-bold">Resolve incident</h2>
                <form method="POST" action="{{ route('tenant.incidents.transition', ['incident' => $incident->getKey()]) }}" class="space-y-2.5">
                    @csrf
                    <div>
                        <label for="resolution_note" class="mb-1 block text-[12.5px] font-semibold">Resolution note (required to block or dismiss)</label>
                        <textarea id="resolution_note" name="resolution_note" rows="3" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px]">{{ old('resolution_note') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" name="action" value="verified" class="rounded-lg bg-tenant hover:bg-tenantdark px-3 py-2.5 text-[13px] font-bold text-white">Verified — release</button>
                        <button type="submit" name="action" value="blocked" class="rounded-lg bg-red-600 px-3 py-2.5 text-[13px] font-bold text-white">Confirmed fraud — block</button>
                        <button type="submit" name="action" value="dismissed" class="rounded-lg border border-line px-3 py-2.5 text-[13px] font-semibold">Dismiss</button>
                        <button type="submit" name="action" value="needs_info" class="rounded-lg border border-line px-3 py-2.5 text-[13px] font-semibold">Needs more info</button>
                    </div>
                </form>
            </div>
            @else
            <div class="rounded-[10px] border border-line bg-panel p-5 text-[13px]">
                <p><strong>Resolved {{ $incident->resolved_at?->format('j M Y, g:i A') }}</strong></p>
                @if($incident->resolution_note)<p class="mt-1 text-inksoft">{{ $incident->resolution_note }}</p>@endif
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
