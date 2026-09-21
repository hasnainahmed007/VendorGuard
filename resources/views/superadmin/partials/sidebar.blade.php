<aside class="sticky top-0 flex h-screen w-[250px] flex-none flex-col border-r border-line bg-panel dark:border-[#2a2c3d] dark:bg-[#1b1d2a]">
    <div class="flex items-center gap-2.5 px-5 pb-4 pt-5">
        <div class="flex size-[34px] flex-none items-center justify-center rounded-[9px] bg-gradient-to-br from-[#d4453d] to-branddark">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-[18px]"><rect x="2" y="5" width="20" height="14" rx="3"/><circle cx="16" cy="12" r="2"/><path d="M2 9h20"/></svg>
        </div>
        <div class="text-[16.5px] font-bold tracking-tight">CashPilot</div>
    </div>

    <div class="flex-1 overflow-y-auto px-3 pb-5 pt-1">
        @can('dashboard.read')
        <a href="{{ route('superadmin.dashboard.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.dashboard.index') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 9v11h14V9"/></svg></span>
            Dashboard
        </a>
        @endcan
        @canany(['users.read', 'plans.read', 'subscriptions.read', 'notifications.read', 'cms.read', 'payments.read'])
        <div class="px-2.5 pb-1.5 pt-4 text-[10.5px] font-bold tracking-[0.06em] text-[#a6a9be] dark:text-[#7b7ea0]">MANAGEMENT</div>
        @endcanany

        @can('users.read')
        <a href="{{ route('superadmin.users.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.users.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            Users
        </a>
        @endcan

        @can('plans.read')
        <a href="{{ route('superadmin.plans.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.plans.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></span>
            Plans
        </a>
        @endcan
        @can('subscriptions.read')
        <a href="{{ route('superadmin.subscriptions.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.subscriptions.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="6" y1="15" x2="10" y2="15"/></svg></span>
            Subscriptions
        </a>
        @endcan

        @can('notifications.read')
        @php($notificationsActive = request()->routeIs('superadmin.notifications.*'))
        <div data-nav-parent data-parent="notif" data-open="{{ $notificationsActive ? 'true' : 'false' }}">
            <button type="button" data-nav-toggle class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ $notificationsActive ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
                <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span>
                Notification
                <span data-nav-chev class="ml-auto text-[#a6a9be] transition-transform dark:text-[#7b7ea0] {{ $notificationsActive ? 'rotate-180' : '' }}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </button>
            <div data-nav-children class="{{ $notificationsActive ? '' : 'hidden ' }}pl-[27px]">
                <a href="{{ route('superadmin.notifications.send.index') }}" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ request()->routeIs('superadmin.notifications.send.index') ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">Send Notification</a>
                <a href="{{ route('superadmin.notifications.templates.index') }}" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ request()->routeIs('superadmin.notifications.templates.index') ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">Templates</a>
                <a href="{{ route('superadmin.notifications.logs.index') }}" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ request()->routeIs('superadmin.notifications.logs.index') ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">Logs</a>
            </div>
        </div>
        @endcan

        @can('cms.read')
        @php($cmsActive = request()->routeIs('superadmin.cms.*'))
        <div data-nav-parent data-parent="cms" data-open="{{ $cmsActive ? 'true' : 'false' }}">
            <button type="button" data-nav-toggle class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ $cmsActive ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
                <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg></span>
                CMS
                <span data-nav-chev class="ml-auto text-[#a6a9be] transition-transform dark:text-[#7b7ea0] {{ $cmsActive ? 'rotate-180' : '' }}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </button>
            <div data-nav-children class="{{ $cmsActive ? '' : 'hidden ' }}pl-[27px]">
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-hero" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ $cmsActive ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">Hero Section</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-features" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Features</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-testimonials" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Testimonials</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-pricing" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Pricing Section</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-faq" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">FAQ</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-footer" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Footer</a>
                <a href="{{ route('superadmin.cms.index') }}#sec-cms-seo" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">SEO &amp; Meta</a>
            </div>
        </div>
        @endcan

        @can('payments.read')
        @php($paymentsActive = request()->routeIs('superadmin.payments.*'))
        <div data-nav-parent data-parent="payments" data-open="{{ $paymentsActive ? 'true' : 'false' }}">
            <button type="button" data-nav-toggle class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ $paymentsActive ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
                <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
                Payments
                <span data-nav-chev class="ml-auto text-[#a6a9be] transition-transform dark:text-[#7b7ea0] {{ $paymentsActive ? 'rotate-180' : '' }}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </button>
            <div data-nav-children class="{{ $paymentsActive ? '' : 'hidden ' }}pl-[27px]">
                <a href="{{ route('superadmin.payments.index') }}#sec-all-payments" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ $paymentsActive ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">All Payments</a>
                <a href="{{ route('superadmin.payments.index') }}#sec-refunds" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Refunds</a>
            </div>
        </div>
        @endcan

        @canany(['staff.read', 'roles.read', 'permissions.read'])
        <div class="px-2.5 pb-1.5 pt-4 text-[10.5px] font-bold tracking-[0.06em] text-[#a6a9be] dark:text-[#7b7ea0]">STAFF MANAGEMENT</div>
        @endcanany

        @can('staff.read')
        <a href="{{ route('superadmin.staff.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.staff.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg></span>
            Staffs
        </a>
        @endcan
        @can('roles.read')
        <a href="{{ route('superadmin.roles.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.roles.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
            Roles
        </a>
        @endcan
        @can('permissions.read')
        <a href="{{ route('superadmin.permissions.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.permissions.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
            Permissions
        </a>
        @endcan

        @canany(['audit-logs.read', 'login-activities.read', 'settings.read'])
        <div class="px-2.5 pb-1.5 pt-4 text-[10.5px] font-bold tracking-[0.06em] text-[#a6a9be] dark:text-[#7b7ea0]">SYSTEM</div>
        @endcanany

        @can('audit-logs.read')
        <a href="{{ route('superadmin.system.audit-logs.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.system.audit-logs.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg></span>
            Audit Logs
        </a>
        @endcan
        @can('login-activities.read')
        <a href="{{ route('superadmin.system.login-activities.index') }}" class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ request()->routeIs('superadmin.system.login-activities.*') ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
            <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg></span>
            Login Activity
        </a>
        @endcan
        @can('settings.read')
        @php($settingsActive = request()->routeIs('superadmin.settings.*'))
        <div data-nav-parent data-parent="settings" data-open="{{ $settingsActive ? 'true' : 'false' }}">
            <button type="button" data-nav-toggle class="mb-px flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13.6px] hover:bg-muted dark:hover:bg-[#242639] {{ $settingsActive ? 'bg-brandtint font-semibold text-branddark dark:bg-[#3d2020] dark:text-[#f09690]' : 'font-medium text-[#4b4e63] dark:text-[#c7c9de]' }}">
                <span class="flex size-[17px] flex-none items-center justify-center [&>svg]:size-[17px] [&>svg]:stroke-current"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
                Settings
                <span data-nav-chev class="ml-auto text-[#a6a9be] transition-transform dark:text-[#7b7ea0] {{ $settingsActive ? 'rotate-180' : '' }}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </button>
            <div data-nav-children class="{{ $settingsActive ? '' : 'hidden ' }}pl-[27px]">
                <a href="{{ route('superadmin.settings.index') }}#sec-general-settings" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] hover:bg-muted hover:text-ink dark:hover:bg-[#242639] dark:hover:text-[#ededf5] {{ $settingsActive ? 'bg-brandtint2 font-bold text-brand dark:bg-[#331b1a] dark:text-[#e0655e]' : 'font-medium text-inksoft dark:text-[#a5a8c2]' }}">General Settings</a>
                <a href="{{ route('superadmin.settings.index') }}#sec-settings-notification" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Notification</a>
                <a href="{{ route('superadmin.settings.index') }}#sec-languages" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Languages</a>
                <a href="{{ route('superadmin.settings.index') }}#sec-currencies" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Currencies</a>
                <a href="{{ route('superadmin.settings.index') }}#sec-privacy-policy" class="mb-px block w-full rounded-[7px] px-2.5 py-[7px] text-left text-[13.3px] font-medium text-inksoft hover:bg-muted hover:text-ink dark:text-[#a5a8c2] dark:hover:bg-[#242639] dark:hover:text-[#ededf5]">Privacy policy</a>
            </div>
        </div>
        @endcan
    </div>
</aside>
