@extends('layouts.superadmin')

@section('title', 'CashPilot — Plans')

@section('content')
<section id="sec-plan-list">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Plan List</h1>
            <p class="text-[13.2px] text-inksoft">Subscription plans available to CashPilot users.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.plans.create') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#7c72ff] dark:bg-[#7c72ff] dark:hover:bg-[#9089ff] [&>svg]:size-[14px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Create plan</a>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="relative rounded-[10px] border border-line bg-panel p-[22px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-1 text-[15px] font-bold">Free</div>
            <div class="mb-4 text-[12.3px] text-inksoft">For individuals just getting started</div>
            <div class="mb-0.5 text-[26px] font-extrabold tracking-tight">$0 <span class="text-[12.5px] font-semibold text-inksoft">/ month</span></div>
            <div class="mb-[18px] text-[11.8px] text-inksoft">1,596 active users</div>
            <ul class="mb-[18px] flex list-none flex-col gap-2.5 p-0">
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>2 accounts</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Basic expense tracking</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Ads supported</li>
            </ul>
            <div class="mt-0.5 flex items-center justify-between border-t border-line pt-3 text-[11.6px] text-inksoft dark:border-[#2a2c3d]"><span>Created 12 Jan 2025</span><div class="flex gap-1.5"><a href="{{ route('superadmin.plans.edit', 'free') }}" title="Edit plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></a><button type="button" title="Delete plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-bad dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#f2685c] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></div></div>
        </div>
        <div class="relative rounded-[10px] border border-brand bg-panel p-[22px] ring-1 ring-brand dark:border-[#7c72ff] dark:bg-[#1b1d2a] dark:ring-[#7c72ff]">
            <span class="absolute -top-[11px] left-[22px] rounded-full bg-brand px-2.5 py-1 text-[10.6px] font-bold text-white dark:bg-[#7c72ff]">Most popular</span>
            <div class="mb-1 text-[15px] font-bold">Pro</div>
            <div class="mb-4 text-[12.3px] text-inksoft">For individuals serious about budgeting</div>
            <div class="mb-0.5 text-[26px] font-extrabold tracking-tight">$12 <span class="text-[12.5px] font-semibold text-inksoft">/ month</span></div>
            <div class="mb-[18px] text-[11.8px] text-inksoft">5,818 active users</div>
            <ul class="mb-[18px] flex list-none flex-col gap-2.5 p-0">
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Unlimited accounts</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Advanced reports</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>No ads</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Cloud backup</li>
            </ul>
            <div class="mt-0.5 flex items-center justify-between border-t border-line pt-3 text-[11.6px] text-inksoft dark:border-[#2a2c3d]"><span>Created 12 Jan 2025</span><div class="flex gap-1.5"><a href="{{ route('superadmin.plans.edit', 'pro') }}" title="Edit plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></a><button type="button" title="Delete plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-bad dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#f2685c] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></div></div>
        </div>
        <div class="relative rounded-[10px] border border-line bg-panel p-[22px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-1 text-[15px] font-bold">Business</div>
            <div class="mb-4 text-[12.3px] text-inksoft">For teams and small businesses</div>
            <div class="mb-0.5 text-[26px] font-extrabold tracking-tight">$29 <span class="text-[12.5px] font-semibold text-inksoft">/ month</span></div>
            <div class="mb-[18px] text-[11.8px] text-inksoft">1,970 active users</div>
            <ul class="mb-[18px] flex list-none flex-col gap-2.5 p-0">
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Everything in Pro</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Up to 5 team members</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Bank sync &amp; export</li>
                <li class="flex items-center gap-2 text-[12.6px] text-[#3e4159] dark:text-[#d6d8e8] [&>svg]:size-[14px] [&>svg]:flex-none [&>svg]:text-good dark:[&>svg]:text-[#3ed9a0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Priority support</li>
            </ul>
            <div class="mt-0.5 flex items-center justify-between border-t border-line pt-3 text-[11.6px] text-inksoft dark:border-[#2a2c3d]"><span>Created 3 Mar 2025</span><div class="flex gap-1.5"><a href="{{ route('superadmin.plans.edit', 'business') }}" title="Edit plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></a><button type="button" title="Delete plan" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-bad dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#f2685c] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></div></div>
        </div>
    </div>
</section>
@endsection
