@extends('tenant.layout')

@section('title', 'VendorGuard — Team')

@section('content')
<section id="sec-team" class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="mb-1 text-xl font-bold tracking-tight">Team</h1>
        <p class="text-[13.2px] text-inksoft">Invite accountants, bookkeepers and office managers. One account can work across many workspaces.</p>
    </div>
    <div class="mb-4 rounded-[10px] border border-line bg-panel">
        <div class="border-b border-line px-5 py-4 text-[13.5px] font-bold">Members</div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13.2px]">
                <thead><tr><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Name</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Email</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]">Role</th><th class="whitespace-nowrap border-b border-line px-5 py-[11px] text-left text-[11.3px] font-bold tracking-wide text-[#9497ab]"></th></tr></thead>
                <tbody>
                    @foreach($members as $member)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd]">
                        <td class="px-5 py-[13px] align-middle font-semibold">{{ $member->name }}</td>
                        <td class="px-5 py-[13px] align-middle">{{ $member->email }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $member->role ?? 'member' }} <span class="text-inksoft">(home)</span></td>
                        <td class="px-5 py-[13px] align-middle"></td>
                    </tr>
                    @endforeach
                    @foreach($grants as $grant)
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd]">
                        <td class="px-5 py-[13px] align-middle font-semibold">{{ $grant->user?->name ?? '—' }}</td>
                        <td class="px-5 py-[13px] align-middle">{{ $grant->user?->email ?? '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle">{{ $grant->role }}</td>
                        <td class="whitespace-nowrap px-5 py-[13px] align-middle text-right">
                            <form method="POST" action="{{ route('tenant.team.members.remove', ['access' => $grant->getKey()]) }}" onsubmit="return confirm('Remove this member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-semibold text-red-600 underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mb-4 rounded-[10px] border border-line bg-panel p-6">
        <h2 class="mb-3 text-[14px] font-bold">Invite a team member</h2>
        <form method="POST" action="{{ route('tenant.team.invitations.store') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="min-w-[220px] flex-1">
                <label for="email" class="mb-1 block text-[12.8px] font-semibold">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" placeholder="bookkeeper@example.com" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13.2px]">
            </div>
            <div>
                <label for="role" class="mb-1 block text-[12.8px] font-semibold">Role</label>
                <select id="role" name="role" class="rounded-lg border border-line bg-bg px-3 py-2 text-[13.2px]">
                    <option value="bookkeeper">Bookkeeper</option>
                    <option value="admin">Admin</option>
                    <option value="viewer">Viewer</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-ink px-4 py-2 text-[13px] font-bold text-white">Send invite</button>
        </form>
    </div>
    @if(count($invitations) > 0)
    <div class="rounded-[10px] border border-line bg-panel">
        <div class="border-b border-line px-5 py-4 text-[13.5px] font-bold">Pending invitations</div>
        <ul class="divide-y divide-line">
            @foreach($invitations as $invitation)
            <li class="flex items-center gap-3 px-5 py-3 text-[13px]">
                <span class="font-semibold">{{ $invitation->email }}</span>
                <span class="text-inksoft">{{ $invitation->role }} · expires {{ $invitation->expires_at?->format('j M Y') }}</span>
                <form method="POST" action="{{ route('tenant.team.invitations.revoke', ['invitation' => $invitation->getKey()]) }}" class="ml-auto" onsubmit="return confirm('Revoke this invitation?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="font-semibold text-red-600 underline">Revoke</button>
                </form>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</section>
@endsection
