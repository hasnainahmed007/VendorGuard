@extends('layouts.auth')

@section('title', 'CashPilot — Create account')

@section('content')
<div class="w-full max-w-md">
    <div class="mb-6 flex items-center justify-center gap-2.5">
        <div class="flex size-[38px] items-center justify-center rounded-[10px] bg-gradient-to-br from-[#d4453d] to-branddark">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[20px]"><rect x="2" y="5" width="20" height="14" rx="3"/><circle cx="16" cy="12" r="2"/><path d="M2 9h20"/></svg>
        </div>
        <span class="text-[18px] font-bold tracking-tight">CashPilot</span>
    </div>

    <div class="rounded-2xl border border-line bg-panel p-7 shadow-sm sm:p-8 dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <h1 class="text-[20px] font-bold tracking-tight">Create your account</h1>
        <p class="mt-1 text-[13.5px] text-inksoft dark:text-[#a5a8c2]">Start tracking cash flow in minutes.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 flex flex-col gap-4" novalidate>
            @csrf

            <div>
                <label for="name" class="mb-1.5 block text-[13px] font-semibold">Full name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Jane Doe"
                    class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-inksoft focus:outline-none focus:ring-2 focus:ring-inksoft/20 dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0] @error('name') border-bad focus:border-bad focus:ring-bad/20 @else border-line dark:border-[#2a2c3d] @enderror"
                >
                @error('name')
                    <p class="mt-1.5 text-[12.5px] font-medium text-bad" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-[13px] font-semibold">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-inksoft focus:outline-none focus:ring-2 focus:ring-inksoft/20 dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0] @error('email') border-bad focus:border-bad focus:ring-bad/20 @else border-line dark:border-[#2a2c3d] @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-[12.5px] font-medium text-bad" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-[13px] font-semibold">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-inksoft focus:outline-none focus:ring-2 focus:ring-inksoft/20 dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0] @error('password') border-bad focus:border-bad focus:ring-bad/20 @else border-line dark:border-[#2a2c3d] @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-[12.5px] font-medium text-bad" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-[13px] font-semibold">Confirm password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[14px] text-ink placeholder:text-[#a6a9be] focus:border-inksoft focus:outline-none focus:ring-2 focus:ring-inksoft/20 dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5] dark:placeholder:text-[#7b7ea0]"
                    >
                </div>
            </div>

            <button type="submit" class="mt-1 w-full rounded-lg bg-brand px-4 py-2.5 text-[14px] font-semibold text-white transition hover:bg-branddark focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2 dark:focus:ring-offset-[#1b1d2a]">
                Create account
            </button>
        </form>
    </div>

    <p class="mt-5 text-center text-[13.5px] text-inksoft dark:text-[#a5a8c2]">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-branddark dark:text-[#f09690]">Sign in</a>
    </p>
</div>
@endsection
