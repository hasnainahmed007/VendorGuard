@extends('layouts.superadmin')

@section('title', 'CashPilot — Dashboard')

@section('content')
<section id="sec-dashboard">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Welcome back, Sarah</h1>
            <p class="text-[13.2px] text-inksoft">Here&#39;s what&#39;s happening with CashPilot today, 3 Sep 2026.</p>
        </div>
        <div class="flex gap-2.5">
            <button type="button" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639] [&>svg]:size-[14px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Export report</button>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-3.5 flex items-center justify-between">
                <div class="flex size-8 items-center justify-center rounded-lg bg-brandtint text-brand dark:bg-[#2a2650] dark:text-[#7c72ff] [&>svg]:size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                <span class="rounded-full bg-goodtint px-[7px] py-[3px] text-[11.5px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">+8.2%</span>
            </div>
            <div class="mb-[3px] text-[23px] font-bold tracking-tight">48,920</div>
            <div class="text-[12.6px] text-inksoft">Total users</div>
        </div>
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-3.5 flex items-center justify-between">
                <div class="flex size-8 items-center justify-center rounded-lg bg-goodtint text-good dark:bg-[#123b2c] dark:text-[#3ed9a0] [&>svg]:size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                <span class="rounded-full bg-goodtint px-[7px] py-[3px] text-[11.5px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">+12.4%</span>
            </div>
            <div class="mb-[3px] text-[23px] font-bold tracking-tight">$68,240</div>
            <div class="text-[12.6px] text-inksoft">Monthly recurring revenue</div>
        </div>
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-3.5 flex items-center justify-between">
                <div class="flex size-8 items-center justify-center rounded-lg bg-warntint text-warn dark:bg-[#3a2f10] dark:text-[#e8be55] [&>svg]:size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></div>
                <span class="rounded-full bg-goodtint px-[7px] py-[3px] text-[11.5px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">+3.1%</span>
            </div>
            <div class="mb-[3px] text-[23px] font-bold tracking-tight">9,384</div>
            <div class="text-[12.6px] text-inksoft">Active subscriptions</div>
        </div>
        <div class="rounded-[10px] border border-line bg-panel p-[18px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="mb-3.5 flex items-center justify-between">
                <div class="flex size-8 items-center justify-center rounded-lg bg-badtint text-bad dark:bg-[#3a1613] dark:text-[#f2685c] [&>svg]:size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <span class="rounded-full bg-badtint px-[7px] py-[3px] text-[11.5px] font-bold text-bad dark:bg-[#3a1613] dark:text-[#f2685c]">-1.6%</span>
            </div>
            <div class="mb-[3px] text-[23px] font-bold tracking-tight">2.3%</div>
            <div class="text-[12.6px] text-inksoft">Churn rate</div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
                <div><h3 class="text-[14.5px] font-semibold">Revenue overview</h3><div class="mt-0.5 text-xs text-inksoft">Last 7 months, subscription revenue</div></div>
                <button type="button" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">Monthly ▾</button>
            </div>
            <div class="flex h-40 items-end gap-2.5 px-5 pb-1 pt-4">
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[46%]"></div><span class="text-[10.8px] text-inksoft">Mar</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[58%]"></div><span class="text-[10.8px] text-inksoft">Apr</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[52%]"></div><span class="text-[10.8px] text-inksoft">May</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[71%]"></div><span class="text-[10.8px] text-inksoft">Jun</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[65%]"></div><span class="text-[10.8px] text-inksoft">Jul</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-[84%]"></div><span class="text-[10.8px] text-inksoft">Aug</span></div>
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full max-w-[30px] rounded-t-[5px] bg-gradient-to-b from-[#8579f5] to-brand h-full"></div><span class="text-[10.8px] text-inksoft">Sep</span></div>
            </div>
        </div>
        <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
                <div><h3 class="text-[14.5px] font-semibold">Plan distribution</h3><div class="mt-0.5 text-xs text-inksoft">Active subscribers by plan</div></div>
            </div>
            <div class="flex items-center gap-5 p-5">
                <div class="relative size-[120px] flex-none rounded-full bg-[conic-gradient(var(--color-brand)_0%_62%,#9e94f8_62%_83%,#e4e1fb_83%_100%)]">
                    <div class="absolute inset-[18px] rounded-full bg-panel dark:bg-[#1b1d2a]"></div>
                </div>
                <div class="flex flex-1 flex-col gap-2.5">
                    <div class="flex items-center justify-between text-[12.6px]"><span class="flex items-center gap-2 text-inksoft"><span class="size-2 rounded-full bg-brand"></span>Pro — 62%</span><span class="font-bold">5,818</span></div>
                    <div class="flex items-center justify-between text-[12.6px]"><span class="flex items-center gap-2 text-inksoft"><span class="size-2 rounded-full bg-[#9e94f8]"></span>Business — 21%</span><span class="font-bold">1,970</span></div>
                    <div class="flex items-center justify-between text-[12.6px]"><span class="flex items-center gap-2 text-inksoft"><span class="size-2 rounded-full bg-[#e4e1fb]"></span>Free — 17%</span><span class="font-bold">1,596</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
                <div><h3 class="text-[14.5px] font-semibold">Recent transactions</h3><div class="mt-0.5 text-xs text-inksoft">Latest payments across all plans</div></div>
                <a href="{{ route('superadmin.payments.index') }}#sec-all-payments" class="cursor-pointer rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[13.2px]">
                    <tbody>
                        <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#6e62f2] text-[11px] font-bold text-white">MR</div><div><div class="font-semibold text-ink dark:text-[#ededf5]">Mahin Rahman</div><div class="mt-px text-[11.8px] text-inksoft">Pro plan</div></div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">$12.00</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Success</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">2m ago</td></tr>
                        <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#e9a23b] text-[11px] font-bold text-white">FA</div><div><div class="font-semibold text-ink dark:text-[#ededf5]">Farzana Akter</div><div class="mt-px text-[11.8px] text-inksoft">Business plan</div></div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">$29.00</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Success</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">18m ago</td></tr>
                        <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#d2483c] text-[11px] font-bold text-white">KH</div><div><div class="font-semibold text-ink dark:text-[#ededf5]">Kabir Hossain</div><div class="mt-px text-[11.8px] text-inksoft">Pro plan</div></div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">$12.00</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-badtint px-2.5 py-1 text-[11.6px] font-bold text-bad dark:bg-[#3a1613] dark:text-[#f2685c]">Failed</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">1h ago</td></tr>
                        <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#1aa97a] text-[11px] font-bold text-white">NS</div><div><div class="font-semibold text-ink dark:text-[#ededf5]">Nusrat Sultana</div><div class="mt-px text-[11.8px] text-inksoft">Pro plan</div></div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">$12.00</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Success</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">3h ago</td></tr>
                        <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#5b4fe9] text-[11px] font-bold text-white">TI</div><div><div class="font-semibold text-ink dark:text-[#ededf5]">Tanvir Islam</div><div class="mt-px text-[11.8px] text-inksoft">Business plan</div></div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">$29.00</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-warntint px-2.5 py-1 text-[11.6px] font-bold text-warn dark:bg-[#3a2f10] dark:text-[#e8be55]">Pending</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">5h ago</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
                <div><h3 class="text-[14.5px] font-semibold">Landing page snapshot</h3><div class="mt-0.5 text-xs text-inksoft">CMS content at a glance</div></div>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-hero" class="cursor-pointer rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">View all</a>
            </div>
            <div class="flex flex-col">
                <div class="flex justify-between border-b border-line px-5 py-3 text-[13px] last:border-b-0 dark:border-[#2a2c3d]"><span class="text-inksoft">Page status</span><span class="font-semibold">🟢 Published</span></div>
                <div class="flex justify-between border-b border-line px-5 py-3 text-[13px] last:border-b-0 dark:border-[#2a2c3d]"><span class="text-inksoft">Sections</span><span class="font-semibold">7 total</span></div>
                <div class="flex justify-between border-b border-line px-5 py-3 text-[13px] last:border-b-0 dark:border-[#2a2c3d]"><span class="text-inksoft">Unpublished drafts</span><span class="font-semibold">2 sections</span></div>
                <div class="flex justify-between border-b border-line px-5 py-3 text-[13px] last:border-b-0 dark:border-[#2a2c3d]"><span class="text-inksoft">Testimonials live</span><span class="font-semibold">8 shown</span></div>
                <div class="flex justify-between border-b border-line px-5 py-3 text-[13px] last:border-b-0 dark:border-[#2a2c3d]"><span class="text-inksoft">Last published</span><span class="font-semibold">2 Sep 2026</span></div>
            </div>
        </div>
    </div>

    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            <div><h3 class="text-[14.5px] font-semibold">Recent signups</h3><div class="mt-0.5 text-xs text-inksoft">Newest users on the platform</div></div>
            <a href="{{ route('superadmin.users.index') }}" class="cursor-pointer rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px] text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">View all users</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">User</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Email</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Plan</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Country</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Joined</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Status</th></tr></thead>
                <tbody>
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#6e62f2] text-[11px] font-bold text-white">RA</div><div class="font-semibold text-ink dark:text-[#ededf5]">Rafiul Ahsan</div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">rafiul.ahsan@gmail.com</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-muted px-2.5 py-1 text-[11.6px] font-bold text-[#6b7089] dark:bg-[#242639] dark:text-[#b3b6cc]">Free</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">Bangladesh</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">3 Sep 2026</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Active</span></td></tr>
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#e9a23b] text-[11px] font-bold text-white">SJ</div><div class="font-semibold text-ink dark:text-[#ededf5]">Sadia Jahan</div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">sadia.j@outlook.com</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-brandtint px-2.5 py-1 text-[11.6px] font-bold text-branddark dark:bg-[#2a2650] dark:text-[#9089ff]">Pro</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">India</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">3 Sep 2026</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Active</span></td></tr>
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#1aa97a] text-[11px] font-bold text-white">OM</div><div class="font-semibold text-ink dark:text-[#ededf5]">Omar Faruk</div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">omar.faruk@yahoo.com</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-muted px-2.5 py-1 text-[11.6px] font-bold text-[#6b7089] dark:bg-[#242639] dark:text-[#b3b6cc]">Free</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">Pakistan</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">2 Sep 2026</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-warntint px-2.5 py-1 text-[11.6px] font-bold text-warn dark:bg-[#3a2f10] dark:text-[#e8be55]">Unverified</span></td></tr>
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex items-center gap-2.5"><div class="flex size-[30px] flex-none items-center justify-center rounded-full bg-[#d2483c] text-[11px] font-bold text-white">LC</div><div class="font-semibold text-ink dark:text-[#ededf5]">Liam Carter</div></div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">liam.carter@gmail.com</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-brandtint px-2.5 py-1 text-[11.6px] font-bold text-branddark dark:bg-[#2a2650] dark:text-[#9089ff]">Pro</span></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">United States</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">2 Sep 2026</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Active</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
