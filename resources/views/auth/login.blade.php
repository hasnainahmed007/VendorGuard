@extends('layouts.auth')

@section('title', 'CashPilot — Sign in')

@section('content')
<div class="w-full max-w-md">
    <div class="mb-6 flex items-center justify-center gap-2.5">
        <div class="flex size-[38px] items-center justify-center rounded-[10px] bg-gradient-to-br from-[#6e62f2] to-branddark">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[20px]"><rect x="2" y="5" width="20" height="14" rx="3"/><circle cx="16" cy="12" r="2"/><path d="M2 9h20"/></svg>
        </div>
        <span class="text-[18px] font-bold tracking-tight">CashPilot</span>
    </div>

    <div class="rounded-2xl border border-line bg-panel p-7 shadow-sm sm:p-8 dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <h1 class="text-[20px] font-bold tracking-tight">Welcome back</h1>
        <p class="mt-1 text-[13.5px] text-inksoft dark:text-[#a5a8c2]">Sign in to your account to continue.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-line bg-brandtint2 px-3.5 py-2.5 text-[13.5px] font-medium text-branddark dark:border-[#2a2650] dark:bg-[#211f3c] dark:text-[#9089ff]" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 flex flex-col gap-4" novalidate>
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-[13px] font-semibold">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0] @error('email') border-bad focus:border-bad focus:ring-bad/20 @else border-line dark:border-[#2a2c3d] @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-[12.5px] font-medium text-bad" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="block text-[13px] font-semibold">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[12.5px] font-semibold text-brand hover:text-branddark dark:text-[#9089ff]">Forgot password?</a>
                    @endif
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0] @error('password') border-bad focus:border-bad focus:ring-bad/20 @else border-line dark:border-[#2a2c3d] @enderror"
                >
                @error('password')
                    <p class="mt-1.5 text-[12.5px] font-medium text-bad" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex cursor-pointer items-center gap-2 text-[13px] font-medium text-inksoft dark:text-[#a5a8c2]">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    {{ old('remember') ? 'checked' : '' }}
                    class="size-4 rounded border-line text-brand focus:ring-brand/30 dark:border-[#2a2c3d] dark:bg-[#12131c]"
                >
                Remember me
            </label>

            <button type="submit" class="mt-1 w-full rounded-lg bg-brand px-4 py-2.5 text-[14px] font-semibold text-white transition hover:bg-branddark focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2 dark:focus:ring-offset-[#1b1d2a]">
                Sign in
            </button>
        </form>
    </div>

    @if (Route::has('register'))
        <p class="mt-5 text-center text-[13.5px] text-inksoft dark:text-[#a5a8c2]">
            Don’t have an account?
            <a href="{{ route('register') }}" class="font-semibold text-brand hover:text-branddark dark:text-[#9089ff]">Create one</a>
        </p>
    @endif
</div>
@endsection
