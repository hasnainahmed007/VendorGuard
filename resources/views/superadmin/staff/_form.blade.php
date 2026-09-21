<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Name</label>
        <input name="name" value="{{ old('name', $staff->name ?? '') }}" placeholder="e.g. Sarah Ahmed" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Email</label>
        <input name="email" value="{{ old('email', $staff->email ?? '') }}" placeholder="e.g. sarah@cashpilot.app" type="email" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
</div>
<div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Password</label>
        <input name="password" type="password" placeholder="Min 8 characters" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Confirm password</label>
        <input name="password_confirmation" type="password" placeholder="Confirm password" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
</div>
<div class="mt-4">
    <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Role</label>
    <select name="role" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-inksoft dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
        <option value="">Select a role</option>
        @foreach($roles as $role)
        <option value="{{ $role->name }}" @selected(old('role', $staff?->role ?? '') === $role->name)>{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
        @endforeach
    </select>
</div>
