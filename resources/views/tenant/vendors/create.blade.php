@extends('tenant.layout')

@section('title', 'VendorGuard — Add vendor')

@section('content')
<section id="sec-vendor-create" class="mx-auto max-w-xl">
    <h1 class="mb-1 text-xl font-bold tracking-tight">Add vendor</h1>
    <p class="mb-6 text-[13.2px] text-inksoft">Bank account and routing numbers are hashed on receipt — only the last four digits are ever stored.</p>
    <form method="POST" action="{{ route('tenant.vendors.store') }}" class="space-y-4 rounded-[10px] border border-line bg-panel p-6">
        @csrf
        <div>
            <label for="name" class="mb-1 block text-[12.8px] font-semibold">Vendor name</label>
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13.2px]">
        </div>
        <div>
            <label for="verified_phone" class="mb-1 block text-[12.8px] font-semibold">Trusted callback number <span class="font-normal text-inksoft">(E.164, e.g. +15551234567 — optional now, required to verify)</span></label>
            <input id="verified_phone" name="verified_phone" value="{{ old('verified_phone') }}" maxlength="20" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[13.2px]">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="bank_account" class="mb-1 block text-[12.8px] font-semibold">Bank account</label>
                <input id="bank_account" name="bank_account" maxlength="34" autocomplete="off" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[13.2px]">
            </div>
            <div>
                <label for="routing_number" class="mb-1 block text-[12.8px] font-semibold">Routing number</label>
                <input id="routing_number" name="routing_number" maxlength="20" autocomplete="off" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[13.2px]">
            </div>
        </div>
        <div>
            <label for="invoice_note" class="mb-1 block text-[12.8px] font-semibold">First invoice note <span class="font-normal text-inksoft">(optional — pasted invoice text is screened for bank-change language)</span></label>
            <textarea id="invoice_note" name="invoice_note" rows="3" maxlength="2000" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13.2px]">{{ old('invoice_note') }}</textarea>
        </div>
        <button type="submit" class="w-full rounded-lg bg-ink px-4 py-2.5 text-[13.5px] font-bold text-white">Add vendor</button>
    </form>
</section>
@endsection
