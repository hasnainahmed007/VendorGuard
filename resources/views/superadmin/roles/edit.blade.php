@extends('layouts.superadmin')

@section('title', 'CashPilot — Edit Role')

@section('content')
<section id="sec-roles-edit">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="mb-1 text-xl font-bold tracking-tight">Edit Role</h1>
            <p class="text-[13.2px] text-inksoft">Update permissions for {{ ucfirst(str_replace('-', ' ', $role->name)) }}.</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.roles.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Back to roles</a>
        </div>
    </div>
    <form method="POST" action="{{ route('superadmin.roles.update', $role) }}" class="max-w-4xl rounded-[10px] border border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
        @csrf
        @method('PUT')
        <div class="flex flex-wrap items-end gap-3 px-5 py-4 border-b border-line dark:border-[#2a2c3d]">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Role Name</label>
                <input name="name" value="{{ old('name', ucfirst(str_replace('-', ' ', $role->name))) }}" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
            </div>
        </div>
        <div class="overflow-x-auto px-5 py-4">
            <table class="w-full border-collapse text-[13.2px]">
                <thead>
                    <tr class="border-b border-line dark:border-[#2a2c3d]">
                        <th class="whitespace-nowrap border-b border-line px-4 py-3 text-left text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Module</th>
                        <th class="whitespace-nowrap border-b border-line px-4 py-3 text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Read</th>
                        <th class="whitespace-nowrap border-b border-line px-4 py-3 text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Create</th>
                        <th class="whitespace-nowrap border-b border-line px-4 py-3 text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Update</th>
                        <th class="whitespace-nowrap border-b border-line px-4 py-3 text-center text-[11.3px] font-bold tracking-wide text-[#9497ab] dark:border-[#2a2c3d] dark:text-[#8d90ac]">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(config('permissions.modules') as $module => $actions)
                    @php
                        $readName = $module . '.read';
                        $createName = $module . '.create';
                        $updateName = $module . '.edit';
                        $deleteName = $module . '.delete';
                        $hasRead = in_array($readName, $actions);
                        $hasCreate = in_array($createName, $actions);
                        $hasUpdate = in_array($updateName, $actions);
                        $hasDelete = in_array($deleteName, $actions);
                        $readChecked = $hasRead && in_array($readName, $rolePermissions) ? 'checked' : '';
                        $createChecked = $hasCreate && in_array($createName, $rolePermissions) ? 'checked' : '';
                        $updateChecked = $hasUpdate && in_array($updateName, $rolePermissions) ? 'checked' : '';
                        $deleteChecked = $hasDelete && in_array($deleteName, $rolePermissions) ? 'checked' : '';
                    @endphp
                    <tr class="border-b border-line last:border-b-0 hover:bg-[#fbfbfd] dark:border-[#2a2c3d] dark:hover:bg-[#242639]">
                        <td class="whitespace-nowrap px-4 py-3 align-middle font-semibold text-ink dark:text-[#ededf5]">{{ ucfirst(str_replace('-', ' ', $module)) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 align-middle text-center">
                            @if($hasRead)
                                <input type="checkbox" name="permissions[]" value="{{ $readName }}" {{ $readChecked }} class="size-4 accent-[#d4453d]">
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 align-middle text-center">
                            @if($hasCreate)
                                <input type="checkbox" name="permissions[]" value="{{ $createName }}" {{ $createChecked }} class="size-4 accent-[#d4453d]">
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 align-middle text-center">
                            @if($hasUpdate)
                                <input type="checkbox" name="permissions[]" value="{{ $updateName }}" {{ $updateChecked }} class="size-4 accent-[#d4453d]">
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 align-middle text-center">
                            @if($hasDelete)
                                <input type="checkbox" name="permissions[]" value="{{ $deleteName }}" {{ $deleteChecked }} class="size-4 accent-[#d4453d]">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end gap-2.5 px-5 py-4 border-t border-line dark:border-[#2a2c3d]">
            <button type="submit" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-brand bg-brand px-[15px] py-2.5 text-[13.3px] font-semibold text-white hover:bg-branddark dark:border-[#e0655e] dark:bg-[#e0655e] dark:hover:bg-[#f09690]">Save changes</button>
            <a href="{{ route('superadmin.roles.index') }}" class="inline-flex items-center gap-[7px] whitespace-nowrap rounded-lg border border-line bg-panel px-[15px] py-2.5 text-[13.3px] font-semibold text-ink hover:bg-muted dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#ededf5] dark:hover:bg-[#242639]">Cancel</a>
        </div>
    </form>
</section>
@endsection
