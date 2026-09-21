@extends('layouts.superadmin')

@section('title', 'CashPilot — Send Notification')

@section('content')
<section id="sec-send-notification">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Send Notification</h1>
            <p class="text-[13.2px] text-inksoft">Push a message to one, some, or all CashPilot users.</p>
        </div>
    </div>
    @if($errors->any())
    <div class="mb-4 rounded-lg border border-bad bg-badtint px-4 py-3 text-sm font-medium text-bad dark:border-[#3a1613] dark:text-[#f2685c]">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-[1.4fr_1fr]">
        <form method="POST" action="{{ route('superadmin.notifications.send.store') }}" class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            @csrf
            <div class="flex items-center justify-between border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
                <div><h3 class="text-[14.5px] font-semibold">Compose notification</h3><div class="mt-0.5 text-xs text-inksoft">This is sent as an in-app + push notification</div></div>
            </div>
            <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-2">
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">Audience</label>
                    <select name="audience" class="w-full rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">
                        <option value="all" @selected(old('audience', 'all') === 'all')>All users</option>
                        <option value="specific" @selected(old('audience') === 'specific')>Specific user…</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">User <span class="text-[11.3px] font-normal text-inksoft">(required for specific audience)</span></label>
                    <select name="user_id" class="w-full rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">
                        <option value="">Select a user</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">Template <span class="text-[11.3px] font-normal text-inksoft">(optional)</span></label>
                    <select name="template_id" class="w-full rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">
                        <option value="">No template</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" @selected((string) old('template_id') === (string) $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Your monthly report is ready" class="w-full rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">
                </div>
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">Message <span class="text-[11.3px] font-normal text-inksoft">(max 160 characters)</span></label>
                    <textarea name="message" maxlength="160" class="min-h-[90px] w-full resize-y rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">{{ old('message') }}</textarea>
                </div>
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <label class="text-[12.6px] font-semibold text-[#3e4159] dark:text-[#d6d8e8]">Channel</label>
                    <select name="channel" class="w-full rounded-lg border border-line bg-panel px-3 py-2.5 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5]">
                        <option value="push_in_app" @selected(old('channel', 'push_in_app') === 'push_in_app')>Push + In-app</option>
                        <option value="push" @selected(old('channel') === 'push')>Push only</option>
                        <option value="in_app" @selected(old('channel') === 'in_app')>In-app only</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2.5 border-t border-line px-5 py-4 dark:border-[#2a2c3d]">
                <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#e0655e] dark:bg-[#e0655e] dark:hover:bg-[#f09690] [&>svg]:size-[14px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Send notification</button>
            </div>
        </form>

        <div class="rounded-[10px] border border-line bg-panel p-5 dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
            <div class="flex justify-center">
                <div class="w-[230px] flex-none rounded-[26px] bg-[#15162b] p-2.5">
                    <div class="min-h-[300px] rounded-[18px] bg-white px-3.5 py-4">
                        <div class="mb-2.5 text-[11px] text-[#9497ab]">Preview</div>
                        <div class="mt-3.5 rounded-[10px] border border-[#e1ddfc] bg-brandtint2 p-[11px]">
                            <div class="mb-[3px] text-[12.5px] font-bold text-ink">{{ old('title', 'Your notification title') }}</div>
                            <div class="text-[11.5px] text-[#5b5e76]">{{ old('message', 'Your notification message will appear here.') }}</div>
                            <div class="mt-1.5 text-[10px] text-[#9497ab]">CashPilot · now</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
