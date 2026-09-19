<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CashPilot — Superadmin')</title>
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
<div class="flex min-h-screen">
    @include('superadmin.partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('superadmin.partials.topbar')

        <main id="contentRoot" class="w-full p-6">
            @if(session('success'))
            <div class="mb-4 rounded-lg border border-good bg-goodtint px-4 py-3 text-sm font-medium text-good dark:border-[#123b2c] dark:text-[#3ed9a0]">
                {{ session('success') }}
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
