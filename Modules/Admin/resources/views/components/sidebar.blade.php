@php
    $navGroups = [
        ['label' => 'Overview', 'items' => [
            ['href' => '/admin', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'exact' => true],
            ['href' => '/admin/analytics', 'label' => 'Analytics', 'icon' => 'bar-chart'],
            ['href' => '/admin/reports', 'label' => 'Reports', 'icon' => 'file-text'],
        ]],
        ['label' => 'Bookings & Students', 'items' => [
            ['href' => '/admin/bookings', 'label' => 'Bookings', 'icon' => 'book-open'],
            ['href' => '/admin/students', 'label' => 'Students', 'icon' => 'users'],
            ['href' => '/admin/cohorts', 'label' => 'Cohorts', 'icon' => 'calendar'],
        ]],
        ['label' => 'Programs', 'items' => [
            ['href' => '/admin/programs', 'label' => 'Programs', 'icon' => 'layers'],
            ['href' => '/admin/trainers', 'label' => 'Trainers', 'icon' => 'user-check'],
        ]],
        ['label' => 'Marketing & CMS', 'items' => [
            ['href' => '/admin/stats', 'label' => 'Stats', 'icon' => 'trending-up'],
            ['href' => '/admin/advertisements', 'label' => 'Advertisements', 'icon' => 'megaphone'],
            ['href' => '/admin/ctas', 'label' => 'CTAs', 'icon' => 'mouse-pointer-click'],
            ['href' => '/admin/blog', 'label' => 'Blog', 'icon' => 'file-text'],
            ['href' => '/admin/testimonials', 'label' => 'Testimonials', 'icon' => 'star'],
            ['href' => '/admin/faqs', 'label' => 'FAQs', 'icon' => 'help-circle'],
            ['href' => '/admin/resources', 'label' => 'Resources', 'icon' => 'library'],
            ['href' => '/admin/purchases', 'label' => 'Purchases', 'icon' => 'dollar-sign', 'roles' => ['admin']],
            ['href' => '/admin/pricing-plans', 'label' => 'Pricing Plans', 'icon' => 'dollar-sign'],
            ['href' => '/admin/team-members', 'label' => 'Team Members', 'icon' => 'user-check'],
            ['href' => '/admin/pages', 'label' => 'Pages', 'icon' => 'globe'],
            ['href' => '/admin/contact-submissions', 'label' => 'Contact Messages', 'icon' => 'message-square'],
        ]],
        ['label' => 'Navigation', 'items' => [
            ['href' => '/admin/menus', 'label' => 'Nav Menus', 'icon' => 'menu'],
        ]],
        ['label' => 'System', 'items' => [
            ['href' => '/admin/users', 'label' => 'Users & Roles', 'icon' => 'shield', 'roles' => ['admin']],
            ['href' => '/admin/notifications', 'label' => 'Notifications', 'icon' => 'bell'],
            ['href' => '/admin/audit-log', 'label' => 'Audit Log', 'icon' => 'history'],
            ['href' => '/admin/settings', 'label' => 'Settings', 'icon' => 'settings', 'roles' => ['admin']],
        ]],
    ];
    $isActive = function (string $href, bool $exact = false) {
        $path = '/'.ltrim(request()->path(), '/');
        return $exact ? $path === $href : ($path === $href || str_starts_with($path, $href.'/'));
    };
    // Nav items can optionally list which roles may see them — omitted
    // means every admin-family role can (matches the blanket route gate).
    // The underlying route/component enforces the real restriction; this
    // just avoids showing a link that would 403.
    $canSee = fn (array $item) => empty($item['roles']) || auth()->user()?->hasAnyRole($item['roles']);
    $navGroups = collect($navGroups)
        ->map(fn ($group) => [...$group, 'items' => array_values(array_filter($group['items'], $canSee))])
        ->filter(fn ($group) => count($group['items']) > 0)
        ->values()
        ->all();
@endphp

<!-- Mobile backdrop -->
<div x-show="mobileSidebarOpen" x-cloak @click="mobileSidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

<aside
    :class="[sidebarCollapsed && !mobileSidebarOpen ? 'lg:w-16' : 'lg:w-60', mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full']"
    class="flex flex-col h-screen w-60 bg-navy-950 text-gray-300 transition-transform duration-300 border-r border-navy-800 fixed inset-y-0 left-0 z-50 lg:sticky lg:top-0 lg:translate-x-0 lg:transition-[width] lg:duration-300"
>
    <!-- Header -->
    <div :class="(sidebarCollapsed && !mobileSidebarOpen) ? 'lg:justify-center' : 'justify-between'" class="flex items-center h-16 border-b border-navy-800 px-4">
        <a href="{{ url('/admin') }}" x-show="!(sidebarCollapsed && !mobileSidebarOpen)" class="flex items-center gap-2" @click="mobileSidebarOpen = false">
            <div class="bg-white rounded-lg px-2 py-1.5">
                <img src="{{ asset('kazilink-logo.png') }}" alt="Kazilink Digital Academy" class="h-8 w-auto">
            </div>
            <span class="font-bold text-white text-sm">Admin</span>
        </a>
        <div x-show="sidebarCollapsed && !mobileSidebarOpen" x-cloak class="hidden lg:flex w-7 h-7 bg-gradient-to-br from-brand-500 to-navy-600 rounded-lg items-center justify-center">
            <x-core::icon name="zap" class="w-3.5 h-3.5 text-white" />
        </div>
        <button
            @click="sidebarCollapsed = !sidebarCollapsed"
            :class="(sidebarCollapsed && !mobileSidebarOpen) && 'lg:hidden'"
            class="hidden lg:block p-1.5 hover:bg-navy-800 rounded-lg transition-colors text-gray-400"
        >
            <x-core::icon name="chevron-left" class="w-4 h-4" />
        </button>
        <button @click="mobileSidebarOpen = false" class="p-1.5 hover:bg-navy-800 rounded-lg transition-colors text-gray-400 lg:hidden" aria-label="Close menu">
            <x-core::icon name="x" class="w-4 h-4" />
        </button>
    </div>

    <button x-show="sidebarCollapsed && !mobileSidebarOpen" x-cloak @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex justify-center py-2 hover:bg-navy-800 transition-colors">
        <x-core::icon name="chevron-right" class="w-4 h-4 text-gray-400" />
    </button>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-6">
        @foreach ($navGroups as $group)
            <div>
                <p x-show="!(sidebarCollapsed && !mobileSidebarOpen)" class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider px-2 mb-1.5">{{ $group['label'] }}</p>
                <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php $active = $isActive($item['href'], $item['exact'] ?? false); @endphp
                        <a
                            href="{{ url($item['href']) }}"
                            @click="mobileSidebarOpen = false"
                            :class="(sidebarCollapsed && !mobileSidebarOpen) && 'lg:justify-center'"
                            class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-colors {{ $active ? 'bg-brand-500/15 text-brand-400 font-medium' : 'text-gray-400 hover:bg-navy-800 hover:text-gray-200' }}"
                        >
                            <x-core::icon :name="$item['icon']" class="w-4 h-4 flex-shrink-0" />
                            <span x-show="!(sidebarCollapsed && !mobileSidebarOpen)">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <!-- Footer -->
    <div class="border-t border-navy-800 p-3 space-y-1">
        <a href="{{ url('/') }}" :class="(sidebarCollapsed && !mobileSidebarOpen) && 'justify-center'" class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm text-gray-400 hover:bg-navy-800 transition-colors">
            <x-core::icon name="search" class="w-4 h-4" />
            <span x-show="!(sidebarCollapsed && !mobileSidebarOpen)">View Website</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                :class="(sidebarCollapsed && !mobileSidebarOpen) && 'justify-center'"
                class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm text-red-400 hover:bg-red-950/30 w-full transition-colors"
            >
                <x-core::icon name="log-out" class="w-4 h-4" />
                <span x-show="!(sidebarCollapsed && !mobileSidebarOpen)">Sign Out</span>
            </button>
        </form>
    </div>
</aside>
