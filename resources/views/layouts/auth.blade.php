<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CashPilot — Sign in')</title>
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
<div class="flex min-h-screen w-full items-center justify-center px-4 py-10 sm:px-6">

@yield('content')

</div>
@stack('scripts')
</body>
</html>
