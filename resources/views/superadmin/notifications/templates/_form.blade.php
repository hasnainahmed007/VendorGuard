<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Template name</label>
        <input name="name" value="{{ old('name', $template->name ?? '') }}" placeholder="e.g. Monthly report ready" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Subject</label>
        <input name="subject" value="{{ old('subject', $template->subject ?? '') }}" placeholder="Optional short description" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
    </div>
</div>
<div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Type</label>
        <select name="type" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
            @foreach($types as $type)
            <option value="{{ $type }}" @selected(old('type', $template->type ?? '') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Channel</label>
        <select name="channel" class="w-full rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">
            @foreach($channels as $channel)
            <option value="{{ $channel }}" @selected(old('channel', $template->channel ?? '') === $channel)>{{ $channel }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4">
    <label class="mb-1.5 block text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Body <span class="text-[11.3px] font-normal text-inksoft">(supports @{{name}} placeholders)</span></label>
    <textarea name="body" placeholder="e.g. Hi @{{name}}, your monthly report is ready." class="min-h-[110px] w-full resize-y rounded-lg border border-line bg-bg px-3 py-2 text-[13px] text-ink outline-none focus:border-brand dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#ededf5]">{{ old('body', $template->body ?? '') }}</textarea>
</div>
<div class="mt-4 flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active ?? true)) class="size-4 accent-[#d4453d]">
    <label class="text-[12.6px] font-semibold text-ink dark:text-[#ededf5]">Active</label>
</div>
