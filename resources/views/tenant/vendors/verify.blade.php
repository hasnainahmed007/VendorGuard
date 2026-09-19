@extends('tenant.layout')

@section('title', "VendorGuard — Verify {$vendor->name}")

@section('content')
<section id="sec-vendor-verify" class="mx-auto max-w-xl">
    <h1 class="mb-1 text-xl font-bold tracking-tight">Verify {{ $vendor->name }}</h1>
    <p class="mb-6 text-[13.2px] text-inksoft">Confirm the trusted callback number from the vendor's original onboarding record — never a number from an email requesting a change. Every future incident is verified against this number.</p>
    <div class="rounded-[10px] border border-line bg-panel p-6">
        @if($vendor->isVerified())
        <p class="mb-4 rounded-lg border border-good bg-goodtint px-4 py-3 text-[13px] font-medium text-good">Currently verified: <span class="font-mono font-bold">{{ $vendor->verified_phone }}</span> ({{ $vendor->verified_at?->format('j M Y') }}). Re-enter below to update.</p>
        @else
        <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-[13px] font-semibold text-red-700">This vendor has no verified callback number yet.</p>
        @endif
        <form method="POST" action="{{ route('tenant.vendors.verify.store', ['vendor' => $vendor->getKey()]) }}" class="space-y-4">
            @csrf
            <div>
                <label for="verified_phone" class="mb-1 block text-[12.8px] font-semibold">Trusted callback number (E.164)</label>
                <input id="verified_phone" name="verified_phone" value="{{ old('verified_phone', $vendor->verified_phone) }}" required maxlength="20" placeholder="+15551234567" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[15px]">
            </div>
            <button type="submit" class="w-full rounded-lg bg-ink px-4 py-2.5 text-[13.5px] font-bold text-white">Save verified number</button>
        </form>
    </div>
    <div class="mt-4 rounded-[10px] border border-line bg-panel p-6">
        <h2 class="mb-1 text-[14px] font-bold">Record a detected change</h2>
        <p class="mb-4 text-[12.8px] text-inksoft">Spotted a bank-detail change outside QuickBooks or email? Recording it opens an incident and holds the vendor immediately.</p>
        <form method="POST" action="{{ route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="field_changed" class="mb-1 block text-[12.8px] font-semibold">Field</label>
                    <select id="field_changed" name="field_changed" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13.2px]">
                        <option value="bank_account">Bank account</option>
                        <option value="routing_number">Routing number</option>
                        <option value="address">Address</option>
                    </select>
                </div>
                <div>
                    <label for="new_value_hash" class="mb-1 block text-[12.8px] font-semibold">New value reference</label>
                    <input id="new_value_hash" name="new_value_hash" maxlength="64" placeholder="e.g. last4:5678" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[13.2px]">
                </div>
            </div>
            <button type="submit" class="w-full rounded-lg border border-red-300 bg-red-50 px-4 py-2.5 text-[13.5px] font-bold text-red-700">Record change + hold payment</button>
        </form>
    </div>
</section>
@endsection
