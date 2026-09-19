@extends('tenant.layout')

@section('title', 'VendorGuard — Vendors')

@section('content')
<section id="sec-vendors">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Vendor directory</h1>
            <p class="text-[13.2px] text-inksoft">Every vendor with their verified callback number and risk state.</p>
        </div>
        <a href="{{ route('tenant.vendors.create') }}" class="rounded-lg bg-ink px-4 py-2.5 text-[13px] font-bold text-white">Add vendor</a>
    </div>
    <div class="rounded-[10px] border border-line bg-panel">
        <form method="GET" action="{{ route('tenant.vendors.index') }}" class="flex flex-wrap items-center gap-2.5 border-b border-line px-5 py-4">
            <input name="search" value="{{ $search }}" placeholder="Search vendors" class="min-w-[200px] rounded-lg border border-line bg-bg px-3 py-2 text-[12.8px] outline-none">
            <select name="verified" onchange="this.form.submit()" class="rounded-lg border border-line bg-panel px-3 py-2 text-[12.6px]">
                <option value="">Verified: All</option>
                <option value="1" @selected($verifiedFilter === '1')>Verified only</option>
                <option value="0" @selected($verifiedFilter === '0')>Unverified only</option>
            </select>
            @if($search !== '' || $verifiedFilter !== '')
            <a href="{{ route('tenant.vendors.index') }}" class="rounded-lg border border-line px-3 py-2 text-[12.6px]">Clear</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Vendor</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Callback number</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Last verified</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Risk</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Hold</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]"></th></tr></thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd]">
                        <td class="px-5 py-[13px] align-middle font-semibold">{{ $vendor->name }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle font-mono">{{ $vendor->verified_phone ?? '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $vendor->verified_at?->format('j M Y') ?? 'never' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $vendor->risk_score }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">@if($vendor->payment_hold)<span class="rounded bg-red-100 px-1.5 py-0.5 text-[11px] font-bold text-red-700">HELD</span>@else<span class="text-inksoft">—</span>@endif</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">@unless($vendor->isVerified())<a href="{{ route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]) }}" class="font-semibold underline">Verify</a>@endunless
                        @can('delete', $vendor)
                            <form method="POST" action="{{ route('tenant.vendors.destroy', ['vendor' => $vendor->getKey()]) }}" class="inline" onsubmit="return confirm('Archive this vendor? History is kept for audit.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 underline">Archive</button>
                            </form>
                        @endcan</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-[13px] text-inksoft">No vendors yet. Add your first vendor to start verification.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendors->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 text-[12.3px] text-inksoft">
            <span>Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }}</span>
            <div>{{ $vendors->links() }}</div>
        </div>
        @endif
    </div>
</section>
@endsection
