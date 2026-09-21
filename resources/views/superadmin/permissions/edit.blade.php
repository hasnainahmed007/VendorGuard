@extends('layouts.superadmin')

@section('title', 'CashPilot — Edit Permission')

@section('content')
<section id="sec-permissions-edit">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Edit Permission</h1>
            <p class="text-[13.2px] text-inksoft">Update permission details.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.permissions.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Back to permissions</a>
        </div>
    </div>
    <form method="POST" action="{{ route('superadmin.permissions.update', $permission) }}" class="max-w-2xl rounded-[10px] border border-line bg-panel p-[22px] dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Permission Name</label>
                <input name="name" value="{{ old('name', $permission->name) }}" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
                <p class="mt-1 text-[11.8px] text-inksoft">Use dot notation: module.action (e.g., staff.index, dashboard.view)</p>
            </div>
        </div>
        <div class="mt-6 flex gap-2.5">
            <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#e0655e] dark:bg-[#e0655e] dark:hover:bg-[#f09690]">Save changes</button>
            <a href="{{ route('superadmin.permissions.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Cancel</a>
        </div>
    </form>
</section>
@endsection
