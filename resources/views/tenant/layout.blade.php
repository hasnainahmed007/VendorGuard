<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VendorGuard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<header class="border-b border-tenantdark/70 bg-tenant dark:border-black/30 dark:bg-tenantdark">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-6 py-3.5">
        <a href="{{ route('tenant.incidents.index') }}" class="text-[15px] font-extrabold tracking-tight text-white">VendorGuard</a>
        <nav class="ml-2 flex items-center gap-1 text-[13.2px] font-medium">
            <a href="{{ route('tenant.incidents.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.incidents.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Incidents</a>
            <a href="{{ route('tenant.vendors.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.vendors.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Vendors</a>
            <a href="{{ route('tenant.team.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.team.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Team</a>
            <a href="{{ route('tenant.audit.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.audit.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Audit</a>
            <a href="{{ route('tenant.settings.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.settings.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Settings</a>
            <a href="{{ route('tenant.integrations.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.integrations.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Accounts</a>
            <a href="{{ route('tenant.billing.index') }}" class="rounded-lg px-3 py-2 text-white/85 hover:bg-white/15 hover:text-white {{ request()->routeIs('tenant.billing.*') ? 'bg-white/25 font-semibold text-white' : '' }}">Billing</a>
        </nav>
        <div class="ml-auto flex items-center gap-2">
            <button type="button" id="themeToggle" title="Toggle dark / light mode" class="rounded-lg p-2 text-white/85 hover:bg-white/15 hover:text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[17px] dark:hidden"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden size-[17px] dark:block"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            </button>
            @php
                $tenants = tenant_layout_data(Auth::user());
                $currentTenant = $tenants['currentTenant'] ?? null;
                $switcherTenants = $tenants['switcherTenants'] ?? [];
            @endphp
            @if(count($switcherTenants ?? []) > 1)
            <form method="POST" action="{{ route('tenant.tenants.switch') }}" class="flex items-center gap-2">
                @csrf
                <select name="tenant_id" onchange="this.form.submit()" class="rounded-lg border border-white/25 bg-white/10 px-2.5 py-1.5 text-[12.6px] text-white">
                    @foreach($switcherTenants as $switchTenant)
                    <option value="{{ $switchTenant->getKey() }}" @selected($currentTenant?->getKey() === $switchTenant->getKey())>{{ $switchTenant->displayName() }}</option>
                    @endforeach
                </select>
            </form>
            @endif
            <span class="text-[12.6px] text-white/80">{{ auth()->user()?->name }}</span>
        </div>
    </div>
</header>
<main class="mx-auto w-full max-w-6xl p-6">
    @if(session('success'))
    <div class="mb-4 rounded-lg border border-good bg-goodtint px-4 py-3 text-sm font-medium text-good dark:border-good/30 dark:bg-good/15 dark:text-[#3ed9a0]">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @yield('content')
</main>
</body>
</html>
