<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VendorGuard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-sm leading-relaxed text-ink antialiased dark:bg-[#12131c] dark:text-[#ededf5]">
<header class="border-b border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-6 py-3.5">
        <a href="{{ route('tenant.incidents.index') }}" class="text-[15px] font-extrabold tracking-tight">VendorGuard</a>
        <span class="rounded-md bg-bg px-2.5 py-1 text-[12px] font-semibold text-inksoft">{{ $currentTenant?->displayName() ?? '—' }}</span>
        <nav class="ml-2 flex items-center gap-1 text-[13.2px] font-medium">
            <a href="{{ route('tenant.incidents.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Incidents</a>
            <a href="{{ route('tenant.vendors.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Vendors</a>
            <a href="{{ route('tenant.team.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Team</a>
            <a href="{{ route('tenant.audit.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Audit</a>
            <a href="{{ route('tenant.settings.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Settings</a>
            <a href="{{ route('tenant.integrations.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Accounts</a>
            <a href="{{ route('tenant.billing.index') }}" class="rounded-lg px-3 py-2 hover:bg-bg">Billing</a>
        </nav>
        <div class="ml-auto flex items-center gap-2">
            @if(count($switcherTenants ?? []) > 1)
            <form method="POST" action="{{ route('tenant.tenants.switch') }}" class="flex items-center gap-2">
                @csrf
                <select name="tenant_id" onchange="this.form.submit()" class="rounded-lg border border-line bg-bg px-2.5 py-1.5 text-[12.6px]">
                    @foreach($switcherTenants as $switchTenant)
                    <option value="{{ $switchTenant->getKey() }}" @selected($currentTenant?->getKey() === $switchTenant->getKey())>{{ $switchTenant->displayName() }}</option>
                    @endforeach
                </select>
            </form>
            @endif
            <span class="text-[12.6px] text-inksoft">{{ auth()->user()?->name }}</span>
        </div>
    </div>
</header>
<main class="mx-auto w-full max-w-6xl p-6">
    @if(session('success'))
    <div class="mb-4 rounded-lg border border-good bg-goodtint px-4 py-3 text-sm font-medium text-good">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
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
