@extends('tenant.layout')

@section('title', 'VendorGuard — Settings')

@section('content')
<section id="sec-settings" class="mx-auto max-w-xl">
    <div class="mb-6">
        <h1 class="mb-1 text-xl font-bold tracking-tight">Settings</h1>
        <p class="text-[13.2px] text-inksoft">How your workspace gets alerted when an incident fires.</p>
    </div>
    <form method="POST" action="{{ route('tenant.settings.notifications.update') }}" class="space-y-4 rounded-[10px] border border-line bg-panel p-6">
        @csrf
        @method('PUT')
        <label class="flex items-start gap-3">
            <input type="checkbox" name="email_enabled" value="1" @checked(old('email_enabled', $settings->email_enabled)) class="mt-1">
            <span><strong class="text-[13.5px]">Email alerts</strong><br><span class="text-[12.8px] text-inksoft">Mail every actionable team member the moment an incident is created.</span></span>
        </label>
        <label class="flex items-start gap-3">
            <input type="checkbox" name="whatsapp_enabled" value="1" @checked(old('whatsapp_enabled', $settings->whatsapp_enabled)) class="mt-1">
            <span><strong class="text-[13.5px]">WhatsApp alerts</strong><br><span class="text-[12.8px] text-inksoft">Optional second channel to a single number.</span></span>
        </label>
        <div>
            <label for="whatsapp_to" class="mb-1 block text-[12.8px] font-semibold">WhatsApp number (E.164, required when enabled)</label>
            <input id="whatsapp_to" name="whatsapp_to" value="{{ old('whatsapp_to', $settings->whatsapp_to) }}" maxlength="20" placeholder="+15551234567" class="w-full rounded-lg border border-line bg-bg px-3 py-2 font-mono text-[13.2px]">
        </div>
        <button type="submit" class="w-full rounded-lg bg-tenant hover:bg-tenantdark px-4 py-2.5 text-[13.5px] font-bold text-white">Save preferences</button>
    </form>
</section>
@endsection
