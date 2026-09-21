@extends('layouts.superadmin')

@section('title', 'CashPilot — Edit Template')

@section('content')
<section id="sec-template-edit">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Edit template</h1>
            <p class="text-[13.2px] text-inksoft">Update the reusable notification template.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.notifications.templates.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Back to templates</a>
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
    <form method="POST" action="{{ route('superadmin.notifications.templates.update', $template) }}" class="max-w-4xl rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        @csrf
        @method('PUT')
        <div class="border-b border-line px-5 py-4 dark:border-[#2a2c3d]">
            @include('superadmin.notifications.templates._form', ['template' => $template])
        </div>
        <div class="flex items-center justify-end gap-2.5 border-t border-line px-5 py-4 dark:border-[#2a2c3d]">
            <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2.5 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#e0655e] dark:bg-[#e0655e] dark:hover:bg-[#f09690]">Update template</button>
            <a href="{{ route('superadmin.notifications.templates.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2.5 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Cancel</a>
        </div>
    </form>
</section>
@endsection
