@extends('tenant.layout')

@section('title', 'VendorGuard — Billing')

@section('content')
<section id="sec-billing" class="mx-auto max-w-2xl">
    <h1 class="mb-1 text-xl font-bold tracking-tight">Billing</h1>
    <p class="mb-6 text-[13.2px] text-inksoft">Single tier: <strong>$29/month</strong>, up to 2 connected accounting orgs, unlimited vendors. 14-day free trial, no card required.</p>
    <div class="rounded-[10px] border border-line bg-panel p-6">
        <dl class="grid grid-cols-2 gap-3 text-[13.2px]">
            <dt class="text-inksoft">Plan</dt><dd class="font-bold">{{ ucfirst($tenant->plan ?? 'trial') }}</dd>
            <dt class="text-inksoft">Status</dt><dd class="font-bold">{{ $isActive ? 'Active' : 'Expired' }}</dd>
            <dt class="text-inksoft">Subscription</dt><dd class="font-mono text-[12.5px]">{{ $tenant->subscription_status ?? 'trial' }}</dd>
        </dl>
        @if(request('checkout') === 'success')
        <p class="mt-4 rounded-lg border border-good bg-goodtint px-4 py-3 text-[13px] font-medium text-good dark:border-good/30 dark:bg-good/15 dark:text-[#3ed9a0]">Checkout complete — your subscription is activating.</p>
        @endif
        @if(request('checkout') === 'cancelled')
        <p class="mt-4 rounded-lg border border-line bg-bg px-4 py-3 text-[13px] text-inksoft">Checkout was cancelled. You can subscribe any time.</p>
        @endif
        @if(! $isActive)
        <form method="POST" action="{{ route('tenant.billing.checkout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-tenant hover:bg-tenantdark px-4 py-2.5 text-[13.5px] font-bold text-white" @disabled(! $stripeConfigured)>Subscribe — $29/month</button>
        </form>
        @unless($stripeConfigured)
        <p class="mt-2 text-[12.5px] text-inksoft">Billing is not configured yet (STRIPE_SECRET / STRIPE_PRICE_ID).</p>
        @endunless
        @endif
    </div>
</section>
@endsection
