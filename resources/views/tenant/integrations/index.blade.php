@extends('tenant.layout')

@section('title', 'VendorGuard — Connected accounts')

@section('content')
<section id="sec-integrations" class="mx-auto max-w-2xl">
    <div class="mb-6">
        <h1 class="mb-1 text-xl font-bold tracking-tight">Connected accounts</h1>
        <p class="text-[13.2px] text-inksoft">Accounting software and inboxes VendorGuard watches for payment-detail changes.</p>
    </div>
    @unless($quickbooksConfigured)
    <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-[13px] font-semibold text-red-700 dark:border dark:border-red-500/25 dark:bg-red-500/10 dark:text-red-300">QuickBooks is not configured yet. Set QB_CLIENT_ID, QB_CLIENT_SECRET and QB_REDIRECT_URI to enable connecting.</p>
    @endunless
    @unless($xeroConfigured)
    <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-[13px] font-semibold text-red-700 dark:border dark:border-red-500/25 dark:bg-red-500/10 dark:text-red-300">Xero is not configured yet. Set XERO_CLIENT_ID, XERO_CLIENT_SECRET and XERO_REDIRECT_URI to enable connecting.</p>
    @endunless
    <div class="space-y-3">
        @forelse($integrations as $integration)
        <div class="flex items-center gap-3 rounded-[10px] border border-line bg-panel p-4">
            <div class="min-w-0 flex-1">
                <p class="text-[13.5px] font-bold">{{ ucfirst($integration->provider) }}@if($integration->external_account_id) <span class="font-mono font-medium text-inksoft">{{ $integration->external_account_id }}</span>@endif</p>
                <p class="text-[12.5px] text-inksoft">Status: {{ $integration->status }}</p>
            </div>
            <form method="POST" action="{{ route('tenant.integrations.destroy', ['integration' => $integration->getKey()]) }}" onsubmit="return confirm('Disconnect this account?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-line px-3 py-2 text-[12.6px] font-semibold">Disconnect</button>
            </form>
        </div>
        @empty
        <p class="rounded-[10px] border border-line bg-panel p-6 text-center text-[13px] text-inksoft">No accounts connected yet.</p>
        @endforelse
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <a href="{{ route('tenant.integrations.connect', ['provider' => 'quickbooks']) }}" class="rounded-lg bg-tenant hover:bg-tenantdark px-4 py-2.5 text-center text-[13px] font-bold text-white">Connect QuickBooks</a>
        <a href="{{ route('tenant.integrations.connect', ['provider' => 'xero']) }}" class="rounded-lg bg-tenant hover:bg-tenantdark px-4 py-2.5 text-center text-[13px] font-bold text-white">Connect Xero</a>
        <a href="{{ route('tenant.integrations.connect', ['provider' => 'gmail']) }}" class="rounded-lg bg-tenant hover:bg-tenantdark px-4 py-2.5 text-center text-[13px] font-bold text-white">Connect Gmail</a>
    </div>
</section>
@endsection
