@extends('layouts.superadmin')

@section('title', 'CashPilot — Invite Staff')

@section('content')
<section id="sec-staff-create">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Invite staff</h1>
            <p class="text-[13.2px] text-inksoft">Add a new team member to the admin panel.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.staff.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Back to staffs</a>
        </div>
    </div>
    <form method="POST" action="{{ route('superadmin.staff.store') }}" class="max-w-4xl rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        @csrf
        <div class="px-5 py-4 border-b border-line dark:border-[#2a2c3d]">
            @include('superadmin.staff._form', ['staff' => null])
        </div>
        <div class="flex items-center justify-end gap-2.5 px-5 py-4 border-t border-line dark:border-[#2a2c3d]">
            <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2.5 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#7c72ff] dark:bg-[#7c72ff] dark:hover:bg-[#9089ff]">Invite staff</button>
            <a href="{{ route('superadmin.staff.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2.5 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Cancel</a>
        </div>
    </form>
</section>
@endsection
