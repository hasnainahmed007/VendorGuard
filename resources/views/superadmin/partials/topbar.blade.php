<div class="sticky top-0 z-10 flex h-16 flex-none items-center gap-4 border-b border-line bg-panel px-6 dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
    <div class="flex w-full max-w-[360px] flex-1 items-center gap-2 rounded-lg border border-line bg-bg px-3 py-2 text-[#9497ab] dark:border-[#2a2c3d] dark:bg-[#12131c] dark:text-[#8d90ac]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[15px] flex-none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search anything…" class="w-full bg-transparent text-[13px] text-ink outline-none placeholder:text-[#9497ab] dark:text-[#ededf5] dark:placeholder:text-[#8d90ac]">
    </div>
    <div class="ml-auto flex items-center gap-3.5">
        <button type="button" id="themeToggle" title="Toggle dark / light mode" class="relative flex size-9 items-center justify-center rounded-lg border border-line bg-panel text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden size-4 dark:block"><circle cx="12" cy="12" r="4"/><line x1="12" y1="1.5" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22.5"/><line x1="4.2" y1="4.2" x2="5.9" y2="5.9"/><line x1="18.1" y1="18.1" x2="19.8" y2="19.8"/><line x1="1.5" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22.5" y2="12"/><line x1="4.2" y1="19.8" x2="5.9" y2="18.1"/><line x1="18.1" y1="5.9" x2="19.8" y2="4.2"/></svg>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block size-4 dark:hidden"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <button type="button" class="relative flex size-9 items-center justify-center rounded-lg border border-line bg-panel text-[#565a73] dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:text-[#c7c9de]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="absolute right-[7px] top-[7px] size-[7px] rounded-full border-[1.5px] border-panel bg-bad dark:border-[#1b1d2a]"></span>
        </button>
        @php
            $topbarUser = auth()->user();
            $topbarName = $topbarUser->name ?? 'Super Admin';
            $topbarEmail = $topbarUser->email ?? '';
            $topbarInitials = collect(explode(' ', trim($topbarName)))
                ->filter()
                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                ->take(2)
                ->implode('') ?: 'SA';
        @endphp
        <div class="relative">
            <button
                type="button"
                id="profileMenuButton"
                aria-haspopup="menu"
                aria-expanded="false"
                aria-controls="profileDropdown"
                class="flex items-center gap-1 rounded-full border border-transparent p-0.5 pr-1 transition hover:border-line hover:bg-muted focus:outline-none focus-visible:ring-2 focus-visible:ring-brand/30 dark:hover:border-[#2a2c3d] dark:hover:bg-[#242639]"
            >
                <span class="flex size-[34px] items-center justify-center rounded-full bg-gradient-to-br from-[#f2c94c] to-[#e9a23b] text-[12.5px] font-bold text-[#5a3b00]">{{ $topbarInitials }}</span>
                <svg id="profileMenuChevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="size-[14px] text-[#9497ab] transition-transform duration-200 dark:text-[#7b7ea0]"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div
                id="profileDropdown"
                role="menu"
                aria-labelledby="profileMenuButton"
                class="absolute right-0 top-full z-20 mt-2 hidden w-60 overflow-hidden rounded-xl border border-line bg-panel shadow-lg shadow-black/5 dark:border-[#2a2c3d] dark:bg-[#1b1d2a] dark:shadow-black/20"
            >
                <div class="px-4 py-3">
                    <p class="truncate text-[13.5px] font-semibold leading-none text-ink dark:text-[#ededf5]">{{ $topbarName }}</p>
                    @if ($topbarEmail !== '')
                        <p class="mt-1 truncate text-[12.5px] text-inksoft dark:text-[#a5a8c2]">{{ $topbarEmail }}</p>
                    @endif
                    <p class="mt-1 inline-flex rounded-full bg-brandtint px-2 py-0.5 text-[10.5px] font-bold tracking-wide text-branddark dark:bg-[#3d2020] dark:text-[#f09690]">Super Admin</p>
                </div>
                <div class="border-t border-line dark:border-[#2a2c3d]"></div>
                <div class="p-1.5">
                    <a href="{{ route('superadmin.settings.index') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13.5px] font-medium text-ink hover:bg-muted dark:text-[#c7c9de] dark:hover:bg-[#242639]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[16px] text-[#9497ab] dark:text-[#7b7ea0]"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Settings
                    </a>
                    <div class="my-1 border-t border-line dark:border-[#2a2c3d]"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-[13.5px] font-medium text-bad transition hover:bg-[#fef2f2] dark:hover:bg-[#2a1a1a]">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[16px]"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
