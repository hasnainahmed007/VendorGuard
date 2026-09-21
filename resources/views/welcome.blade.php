<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gasket — Vendor payment fraud protection for QuickBooks SMBs</title>
    <meta name="description" content="Gasket blocks vendor payment detail changes until a human verifies them by callback. QuickBooks + email-layer fraud detection for 5–50 employee businesses.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        try {
            if (localStorage.getItem('cashpilot-theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        } catch (_) {}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-sm leading-relaxed text-ink antialiased dark:bg-[#12131c] dark:text-[#ededf5]">

<!-- ======================= NAVBAR ======================= -->
<header class="sticky top-0 z-50 border-b border-white/10 bg-[#171224]/85 backdrop-blur-xl dark:border-white/10 dark:bg-[#0e0c16]/85">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-6 py-3.5">
        <a href="/" class="flex items-center gap-2.5">
            <span class="flex size-[34px] items-center justify-center rounded-[10px] bg-gradient-to-br from-tenant via-[#e0655e] to-tenantdark shadow-lg shadow-tenant/30">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="size-[19px]"><path d="M12 22s8-3.6 8-10V5l-8-3-8 3v7c0 6.4 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            </span>
            <span class="bg-gradient-to-r from-white via-white to-white/70 bg-clip-text text-[17px] font-extrabold tracking-tight text-transparent">Gasket</span>
        </a>
        <nav class="ml-4 hidden items-center gap-1 text-[13.2px] font-medium text-white/75 md:flex">
            <a href="#features" class="rounded-lg px-3 py-2 hover:bg-white/10 hover:text-white">Features</a>
            <a href="#how" class="rounded-lg px-3 py-2 hover:bg-white/10 hover:text-white">How it works</a>
            <a href="#pricing" class="rounded-lg px-3 py-2 hover:bg-white/10 hover:text-white">Pricing</a>
            <a href="#docs" class="rounded-lg px-3 py-2 hover:bg-white/10 hover:text-white">Docs</a>
        </nav>
        <div class="ml-auto flex items-center gap-2">
            <button type="button" id="themeToggle" title="Toggle dark / light mode" class="rounded-lg p-2 text-white/75 hover:bg-white/10 hover:text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[17px] dark:hidden"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden size-[17px] dark:block"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            </button>
            @auth
                <a href="{{ url('/app/incidents') }}" class="rounded-lg bg-white/10 px-4 py-2 text-[13px] font-bold text-white hover:bg-white/20">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-[13px] font-semibold text-white/85 hover:bg-white/10 hover:text-white">Log in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-gradient-to-r from-tenant to-[#e0655e] px-4 py-2 text-[13px] font-bold text-white shadow-lg shadow-tenant/30 hover:brightness-110">Start free</a>
            @endauth
        </div>
    </div>
</header>

<!-- ======================= HERO ======================= -->
<section class="relative overflow-hidden bg-[#171224] dark:bg-[#0e0c16]">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-32 left-1/4 size-[480px] rounded-full bg-tenant/35 blur-[130px]"></div>
        <div class="absolute -bottom-40 right-[8%] size-[420px] rounded-full bg-[#5b4fe9]/25 blur-[130px]"></div>
        <div class="absolute left-[6%] top-1/2 size-[280px] rounded-full bg-[#e0655e]/20 blur-[110px]"></div>
    </div>
    <div class="relative mx-auto max-w-6xl px-6 pb-20 pt-16 text-center sm:pt-24">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[12.5px] font-semibold text-white/90 backdrop-blur">
            <span class="size-2 rounded-full bg-gradient-to-r from-tenant to-[#e0655e]"></span>
            Vendor payment fraud protection for QuickBooks SMBs
        </span>
        <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-black leading-[1.08] tracking-tight text-white sm:text-[54px]">
            Stop the <span class="bg-gradient-to-r from-[#ff8a80] via-tenant to-[#e0655e] bg-clip-text text-transparent">"banking details changed"</span> scam before money moves
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-[15.5px] leading-relaxed text-white/70">
            Fraudsters hijack real vendor email threads, then send a perfectly-worded bank-change request timed to a real invoice.
            Gasket watches QuickBooks <em class="text-white/90 not-italic">and</em> your inbox — and holds every payment-detail change until a human confirms it by calling the trusted number on file.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-tenant to-[#e0655e] px-7 py-3.5 text-[14.5px] font-bold text-white shadow-xl shadow-tenant/40 hover:brightness-110">Start 14-day free trial</a>
            <a href="#how" class="rounded-xl border border-white/20 bg-white/10 px-7 py-3.5 text-[14.5px] font-semibold text-white backdrop-blur hover:bg-white/20">See how it works</a>
        </div>
        <p class="mt-4 text-[12.5px] text-white/50">No credit card required · $29/mo after trial · Cancel anytime</p>
        <dl class="mx-auto mt-12 grid max-w-3xl grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/12 bg-white/[0.07] px-5 py-5 backdrop-blur-xl">
                <dt class="text-[32px] font-black text-white">76%</dt>
                <dd class="mt-1 text-[12.8px] text-white/60">of organisations hit by payment fraud in 2025</dd>
            </div>
            <div class="rounded-2xl border border-white/12 bg-white/[0.07] px-5 py-5 backdrop-blur-xl">
                <dt class="text-[32px] font-black text-white">$30K+</dt>
                <dd class="mt-1 text-[12.8px] text-white/60">average SMB loss per vendor-impersonation incident</dd>
            </div>
            <div class="rounded-2xl border border-white/12 bg-white/[0.07] px-5 py-5 backdrop-blur-xl">
                <dt class="text-[32px] font-black text-white">2 min</dt>
                <dd class="mt-1 text-[12.8px] text-white/60">from flagged change to held payment + team alert</dd>
            </div>
        </dl>
    </div>
</section>

<!-- ======================= TRUST STRIP ======================= -->
<section class="border-b border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-8 gap-y-2 px-6 py-4 text-[12.8px] font-semibold text-inksoft">
        <span>✓ Bank numbers stored hashed-only, never raw</span>
        <span>✓ Full audit trail your auditors will accept</span>
        <span>✓ OAuth tokens encrypted at rest</span>
        <span>✓ QuickBooks-first, Xero supported</span>
    </div>
</section>

<!-- ======================= FEATURES ======================= -->
<section id="features" class="mx-auto max-w-6xl scroll-mt-20 px-6 py-16">
    <p class="text-[12.5px] font-bold uppercase tracking-[0.14em] text-tenant">Features</p>
    <h2 class="mt-2 max-w-xl text-[28px] font-extrabold tracking-tight">One mechanical control, done perfectly</h2>
    <p class="mt-2 max-w-2xl text-[14px] text-inksoft">Not a general AI fraud judge. A narrow rule: no payment-detail change takes effect until someone calls the number on file.</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
            <h3 class="text-[15px] font-bold">Verified callback numbers</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Every vendor gets a trusted phone number captured at onboarding. Every future incident puts that number front and center — never the one from the email.</p>
        </div>
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg></div>
            <h3 class="text-[15px] font-bold">Email-layer detection</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Gmail and Outlook inboxes are watched for banking-change language and urgency markers — catching fraud <em>before</em> it ever reaches your books.</p>
        </div>
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M3 3v18h18"/><path d="M7 15v3M12 10v8M17 6v12"/></svg></div>
            <h3 class="text-[15px] font-bold">QuickBooks + Xero watchers</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Vendor records are polled for bank, routing, and identity changes. First sync sets a silent baseline; real divergence opens an incident.</p>
        </div>
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
            <h3 class="text-[15px] font-bold">Payment hold, one click</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Flagged vendors are held instantly. Release with "Verified", or block on "Confirmed fraud" — each action stamped into the audit trail.</p>
        </div>
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg></div>
            <h3 class="text-[15px] font-bold">Email + WhatsApp alerts</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">The moment an incident fires, actionable members get an email with the callback number — plus optional WhatsApp for urgent cases.</p>
        </div>
        <div class="rounded-2xl border border-line bg-panel/80 p-6 shadow-sm backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <div class="mb-3 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-tenant to-tenantdark text-white shadow-md shadow-tenant/25"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15l2 2 4-4"/></svg></div>
            <h3 class="text-[15px] font-bold">Auditor-ready reports</h3>
            <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Who reviewed what, when, and what they decided — exportable to CSV for your accountant or auditor in one click.</p>
        </div>
    </div>
</section>

<!-- ======================= HOW IT WORKS ======================= -->
<section id="how" class="border-y border-line bg-panel/60 scroll-mt-20 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.02]">
    <div class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-[12.5px] font-bold uppercase tracking-[0.14em] text-tenant">How it works</p>
        <h2 class="mt-2 max-w-xl text-[28px] font-extrabold tracking-tight">Live in an afternoon, protecting every payment</h2>
        <ol class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <li class="relative rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <span class="bg-gradient-to-br from-tenant to-tenantdark bg-clip-text text-[34px] font-black text-transparent">01</span>
                <h3 class="mt-2 text-[14.5px] font-bold">Connect</h3>
                <p class="mt-1 text-[13px] text-inksoft">Link QuickBooks and your inbox with OAuth. Tokens are encrypted; polling starts automatically.</p>
            </li>
            <li class="relative rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <span class="bg-gradient-to-br from-tenant to-tenantdark bg-clip-text text-[34px] font-black text-transparent">02</span>
                <h3 class="mt-2 text-[14.5px] font-bold">Verify vendors</h3>
                <p class="mt-1 text-[13px] text-inksoft">Walk the wizard: confirm each vendor's trusted callback number. This becomes the permanent record.</p>
            </li>
            <li class="relative rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <span class="bg-gradient-to-br from-tenant to-tenantdark bg-clip-text text-[34px] font-black text-transparent">03</span>
                <h3 class="mt-2 text-[14.5px] font-bold">Detect &amp; hold</h3>
                <p class="mt-1 text-[13px] text-inksoft">A suspicious email or record change opens an incident and holds the vendor instantly. Team alerted.</p>
            </li>
            <li class="relative rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <span class="bg-gradient-to-br from-tenant to-tenantdark bg-clip-text text-[34px] font-black text-transparent">04</span>
                <h3 class="mt-2 text-[14.5px] font-bold">Call &amp; clear</h3>
                <p class="mt-1 text-[13px] text-inksoft">One tap dials the verified number. Confirmed? Release. Fraud? Block — with a full audit trail.</p>
            </li>
        </ol>
    </div>
</section>

<!-- ======================= PRICING ======================= -->
<section id="pricing" class="mx-auto max-w-6xl scroll-mt-20 px-6 py-16 text-center">
    <p class="text-[12.5px] font-bold uppercase tracking-[0.14em] text-tenant">Pricing</p>
    <h2 class="mt-2 text-[28px] font-extrabold tracking-tight">One plan. Less than 0.1% of one incident.</h2>
    <p class="mx-auto mt-2 max-w-xl text-[14px] text-inksoft">A single vendor-impersonation loss averages $30,000+. Gasket costs less than a team lunch.</p>
    <div class="mx-auto mt-8 max-w-md rounded-3xl border border-tenant/30 bg-panel/80 p-8 text-left shadow-2xl shadow-tenant/15 backdrop-blur dark:border-tenant/40 dark:bg-white/[0.04]">
        <div class="flex items-baseline justify-between">
            <h3 class="text-[16px] font-extrabold">Guardian</h3>
            <span class="rounded-full bg-tenant/10 px-3 py-1 text-[11.5px] font-bold text-tenant dark:bg-tenant/20">14-day free trial</span>
        </div>
        <p class="mt-3"><span class="text-[42px] font-black tracking-tight">$29</span><span class="text-[14px] text-inksoft">/month</span></p>
        <ul class="mt-5 space-y-2.5 text-[13.5px]">
            <li>✓ Up to 2 connected accounting orgs</li>
            <li>✓ Unlimited vendors &amp; incidents</li>
            <li>✓ Email-layer + QuickBooks + Xero watchers</li>
            <li>✓ Email &amp; WhatsApp alerts</li>
            <li>✓ Audit-trail CSV exports</li>
            <li>✓ Unlimited team seats with roles</li>
        </ul>
        <a href="{{ route('register') }}" class="mt-6 block rounded-xl bg-gradient-to-r from-tenant to-[#e0655e] px-4 py-3 text-center text-[14.5px] font-bold text-white shadow-lg shadow-tenant/30 hover:brightness-110">Start free trial</a>
        <p class="mt-3 text-center text-[12px] text-inksoft">No credit card required · Cancel anytime</p>
    </div>
</section>

<!-- ======================= DOCS ======================= -->
<section id="docs" class="border-y border-line bg-panel/60 scroll-mt-20 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.02]">
    <div class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-[12.5px] font-bold uppercase tracking-[0.14em] text-tenant">Documentation</p>
        <h2 class="mt-2 max-w-xl text-[28px] font-extrabold tracking-tight">Everything, on one page</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">1 · Create your workspace</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Register with name, email, and password. A workspace (tenant) is provisioned automatically and you become its owner with a 14-day trial — no card required. Log in any time; owners land on the incident queue, staff land on the central panel.</p>
            </div>
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">2 · Connect QuickBooks</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Accounts → Connect QuickBooks → authorize your company at Intuit. Requires <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-[12px]">QB_CLIENT_ID / QB_CLIENT_SECRET / QB_REDIRECT_URI</code> in <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-[12px]">.env</code>. First sync imports vendors silently as baselines; later bank/identity divergence opens incidents. Up to 2 orgs per workspace.</p>
            </div>
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">3 · Connect Gmail or Outlook</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Same Accounts page, read-only OAuth. Needs <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-[12px]">GMAIL_*</code> (enable the Gmail API + add test users while unverified) or Entra ID app credentials for Outlook. Polls every 15 minutes; flagged messages mentioning a vendor open email-source incidents.</p>
            </div>
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">4 · Verify vendors, then work incidents</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Vendors → Verify: save each trusted callback number (E.164). When an incident fires, call that number — <em>never</em> one from the message — then mark <strong>Verified</strong> (releases + adopts new details), <strong>Blocked</strong> (stays held), <strong>Dismissed</strong>, or <strong>Needs info</strong>. Every step is audit-logged and exportable.</p>
            </div>
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">5 · Team, alerts &amp; billing</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Team page invites bookkeepers by role; one account can span many client workspaces via the header switcher. Settings toggles email/WhatsApp alerts per workspace. Billing shows trial status; checkout is $29/mo via Stripe, webhooks activate automatically.</p>
            </div>
            <div class="rounded-2xl border border-line bg-bg/70 p-6 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.03]">
                <h3 class="text-[15px] font-bold">6 · Security model</h3>
                <p class="mt-1.5 text-[13.2px] leading-relaxed text-inksoft">Bank numbers are stored hashed-only (last-4 for display). OAuth tokens are encrypted at rest. Every query is tenant-scoped — cross-tenant IDs return 404. Raw email bodies are never persisted, only their hashes.</p>
            </div>
        </div>
        <div class="mt-8 rounded-2xl border border-line bg-[#171224] p-6 text-[13px] leading-relaxed text-white/75 backdrop-blur dark:border-[#2a2c3d] dark:bg-black/40">
            <h3 class="text-[14px] font-bold text-white">Operator checklist (self-hosting)</h3>
            <pre class="mt-3 overflow-x-auto rounded-xl bg-black/40 p-4 font-mono text-[12.5px] leading-relaxed text-emerald-200">vendor/bin/sail up -d                  # app + MySQL
vendor/bin/sail artisan migrate --force  # schema
vendor/bin/sail artisan queue:work       # alerts + sync jobs
vendor/bin/sail artisan schedule:run     # pollers (or cron every minute)
vendor/bin/sail npm run build            # frontend assets</pre>
        </div>
    </div>
</section>

<!-- ======================= FAQ ======================= -->
<section class="mx-auto max-w-3xl px-6 py-16">
    <h2 class="text-center text-[24px] font-extrabold tracking-tight">Questions, answered</h2>
    <div class="mt-6 space-y-3">
        <details class="rounded-2xl border border-line bg-panel/80 px-5 py-4 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <summary class="cursor-pointer text-[14px] font-bold">Do you move or touch my money?</summary>
            <p class="mt-2 text-[13.2px] text-inksoft">No. Gasket holds the <em>vendor record</em> and alerts your team — it never integrates with bank rails. A human always makes the payment decision after the callback.</p>
        </details>
        <details class="rounded-2xl border border-line bg-panel/80 px-5 py-4 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <summary class="cursor-pointer text-[14px] font-bold">What if the fraud email never touches QuickBooks?</summary>
            <p class="mt-2 text-[13.2px] text-inksoft">That's exactly why the email layer exists — it catches the attempt at the inbox stage, before anything is paid or recorded.</p>
        </details>
        <details class="rounded-2xl border border-line bg-panel/80 px-5 py-4 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <summary class="cursor-pointer text-[14px] font-bold">Can one bookkeeper protect many clients?</summary>
            <p class="mt-2 text-[13.2px] text-inksoft">Yes — invitations grant one account a role across many workspaces, with a header switcher to move between them.</p>
        </details>
        <details class="rounded-2xl border border-line bg-panel/80 px-5 py-4 backdrop-blur dark:border-[#2a2c3d] dark:bg-white/[0.04]">
            <summary class="cursor-pointer text-[14px] font-bold">What happens after the 14-day trial?</summary>
            <p class="mt-2 text-[13.2px] text-inksoft">Subscribe for $29/month from the Billing page. Until then, everything works — trial is fully featured, no card required.</p>
        </details>
    </div>
    <div class="mt-10 rounded-3xl bg-gradient-to-r from-tenant via-[#e0655e] to-tenantdark p-8 text-center shadow-2xl shadow-tenant/25">
        <h3 class="text-[22px] font-extrabold text-white">One blocked wire pays for 100 years of Gasket.</h3>
        <p class="mt-2 text-[13.5px] text-white/80">Join the beta for US QuickBooks businesses today.</p>
        <a href="{{ route('register') }}" class="mt-5 inline-block rounded-xl bg-white px-7 py-3 text-[14px] font-bold text-tenantdark hover:bg-white/90">Start free trial</a>
    </div>
</section>

<!-- ======================= FOOTER ======================= -->
<footer class="border-t border-line dark:border-[#2a2c3d]">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-4 px-6 py-8 text-[12.8px] text-inksoft">
        <span class="flex items-center gap-2 font-extrabold text-ink dark:text-white">
            <span class="flex size-[26px] items-center justify-center rounded-lg bg-gradient-to-br from-tenant to-tenantdark">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="size-[15px]"><path d="M12 22s8-3.6 8-10V5l-8-3-8 3v7c0 6.4 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            </span>
            Gasket
        </span>
        <span>© {{ date('Y') }} Gasket. Vendor payment fraud protection.</span>
        <span class="ml-auto flex gap-4">
            <a href="#features" class="hover:text-ink dark:hover:text-white">Features</a>
            <a href="#pricing" class="hover:text-ink dark:hover:text-white">Pricing</a>
            <a href="#docs" class="hover:text-ink dark:hover:text-white">Docs</a>
            <a href="{{ route('login') }}" class="hover:text-ink dark:hover:text-white">Log in</a>
        </span>
    </div>
</footer>
</body>
</html>
