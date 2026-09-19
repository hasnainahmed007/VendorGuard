@extends('tenant.layout')

@section('title', 'VendorGuard — Workspaces')

@section('content')
<section id="sec-workspaces" class="mx-auto max-w-xl">
    <h1 class="mb-1 text-xl font-bold tracking-tight">Workspaces</h1>
    <p class="mb-6 text-[13.2px] text-inksoft">Switch between the businesses you have access to.</p>
    @if(count($tenants) === 0)
    <div class="rounded-[10px] border border-line bg-panel p-6 text-center">
        <p class="text-[13.5px] font-semibold">No workspaces yet</p>
        <p class="mt-1 text-[13px] text-inksoft">Ask your workspace administrator to invite you. Invited bookkeepers appear here automatically once the invite is accepted.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($tenants as $workspace)
        <div class="flex items-center gap-3 rounded-[10px] border border-line bg-panel p-4">
            <div class="min-w-0 flex-1">
                <p class="text-[13.5px] font-bold">{{ $workspace['name'] }}</p>
                <p class="text-[12.5px] text-inksoft">Role: {{ $workspace['role'] }} · Plan: {{ $workspace['plan'] ?? 'trial' }}</p>
            </div>
            @if($workspace['is_current'])
            <span class="rounded-md bg-ink px-2.5 py-1 text-[12px] font-bold text-white">Current</span>
            @else
            <form method="POST" action="{{ route('tenant.tenants.switch') }}">
                @csrf
                <input type="hidden" name="tenant_id" value="{{ $workspace['id'] }}">
                <button type="submit" class="rounded-lg border border-line px-3 py-2 text-[12.6px] font-semibold">Switch</button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</section>
@endsection
