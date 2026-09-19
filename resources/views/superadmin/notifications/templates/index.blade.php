@extends('layouts.superadmin')

@section('title', 'CashPilot — Notification Templates')

@section('content')
<section id="sec-templates">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Templates</h1>
            <p class="text-[13.2px] text-inksoft">Reusable notification templates for recurring messages.</p>
        </div>
        <div class="flex gap-2.5">
            @can('notifications.create')
            <a href="{{ route('superadmin.notifications.templates.create') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#7c72ff] dark:bg-[#7c72ff] dark:hover:bg-[#9089ff] [&>svg]:size-[14px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>New template</a>
            @endcan
        </div>
    </div>
    <div class="rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Template name</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Type</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Channel</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Last updated</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Status</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]"></th></tr></thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]"><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="font-semibold text-ink dark:text-[#ededf5]">{{ $template->name }}</div><div class="mt-px text-[11.8px] text-inksoft">{{ $template->subject }}</div></td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $template->type }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $template->channel }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $template->updated_at->format('j M Y') }}</td><td class="whitespace-nowrap px-5 py-[13px] align-middle">@if($template->is_active)<span class="inline-flex items-center gap-[5px] rounded-full bg-goodtint px-2.5 py-1 text-[11.6px] font-bold text-good dark:bg-[#123b2c] dark:text-[#3ed9a0]">Active</span>@else<span class="inline-flex items-center gap-[5px] rounded-full bg-muted px-2.5 py-1 text-[11.6px] font-bold text-[#6b7089] dark:bg-[#242639] dark:text-[#b3b6cc]">Paused</span>@endif</td><td class="whitespace-nowrap px-5 py-[13px] align-middle"><div class="flex gap-1.5">@can('notifications.edit')<a href="{{ route('superadmin.notifications.templates.edit', $template) }}" title="Edit" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>@endcan @can('notifications.delete')<form method="POST" action="{{ route('superadmin.notifications.templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">@csrf @method('DELETE')<button type="submit" title="Delete" class="flex size-7 items-center justify-center rounded-[7px] text-[#8b8fa3] hover:bg-muted hover:text-ink dark:text-[#9497b8] dark:hover:bg-[#242639] dark:hover:text-[#ededf5] [&>svg]:size-[14.5px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></form>@endcan</div></td></tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-[13px] text-inksoft">No templates yet. Create your first template to reuse it when sending notifications.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $templates->firstItem() }}–{{ $templates->lastItem() }} of {{ $templates->total() }} templates</span>
            <div>{{ $templates->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
