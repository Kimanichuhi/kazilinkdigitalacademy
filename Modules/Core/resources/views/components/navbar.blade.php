@php
    $adminFamilyRoles = \Modules\Core\Support\RoleGroups::adminFamily();
    $user = auth()->user();
@endphp

<div x-data="{
        mobileOpen: false,
        scrolled: false,
        userMenuOpen: false,
        dark: localStorage.getItem('theme') === 'dark',
        toggleTheme() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.dark);
        },
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 }, { passive: true })"
>
    @if ($announcement)
        <div class="announcement-bar text-white text-center py-2 text-sm font-medium px-4">
            <span>{{ $announcement }}</span>
            <a href="{{ url('/booking') }}" class="ml-3 underline font-bold hover:no-underline">Enroll Now &rarr;</a>
        </div>
    @endif

    <nav
        :class="scrolled ? 'bg-white/95 dark:bg-gray-950/95 backdrop-blur-lg shadow-sm border-b border-border' : 'bg-white dark:bg-gray-950'"
        class="sticky top-0 z-50 w-full transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center group">
                    <div class="dark:bg-white dark:rounded-lg dark:px-2 dark:py-1.5">
                        <img src="{{ asset('kazilink-logo.png') }}" alt="{{ $siteName }}" class="h-14 w-auto">
                    </div>
                </a>

                <!-- Desktop nav -->
                <div class="hidden lg:flex items-center gap-1">
                    @foreach ($navItems as $item)
                        <div class="relative group">
                            <a
                                href="{{ $item['url'] ?? '#' }}"
                                class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is(ltrim($item['url'] ?? '#', '/')) ? 'text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-950' : 'text-gray-600 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                            >
                                {{ $item['label'] }}
                                @if (! empty($item['children']))
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                @endif
                                @if (! empty($item['badge']))
                                    <span class="ml-1 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                            @if (! empty($item['children']))
                                <div class="absolute top-full left-0 mt-1 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 p-1">
                                    @foreach ($item['children'] as $child)
                                        <a href="{{ $child['url'] ?? '#' }}" class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-950 rounded-lg transition-colors">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-2">
                    <button
                        @click="toggleTheme()"
                        x-cloak
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="Toggle theme"
                    >
                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    </button>

                    @if ($user)
                        <div class="relative" x-data @click.outside="userMenuOpen = false">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-7 h-7 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div x-show="userMenuOpen" x-cloak class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-border z-50 p-1">
                                <div class="px-3 py-2 border-b border-border mb-1">
                                    <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ $user->email }}</p>
                                </div>
                                @if ($user->hasAnyRole($adminFamilyRoles))
                                    <a href="{{ url('/admin') }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                                        Admin Dashboard
                                    </a>
                                @endif
                                <a href="{{ url('/student') }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    My Dashboard
                                </a>
                                <a href="{{ url('/student/notifications') }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                    Notifications
                                </a>
                                <hr class="my-1 border-border">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950 rounded-lg w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden sm:flex items-center gap-2">
                            <a href="{{ route('login') }}" class="text-sm px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Log In</a>
                            <a href="{{ url('/booking') }}" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-lg shadow-brand-500/25 transition-colors">
                                Enroll Now
                            </a>
                        </div>
                    @endif

                    <!-- Mobile menu button -->
                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16M4 6h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileOpen" x-cloak class="lg:hidden border-t border-border bg-white dark:bg-gray-950 px-4 py-4 space-y-1">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['url'] ?? '#' }}"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is(ltrim($item['url'] ?? '#', '/')) ? 'bg-brand-50 dark:bg-brand-950 text-brand-600 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
            @guest
                <div class="pt-3 border-t border-border flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="w-full text-center border border-border rounded-xl py-2 text-sm font-medium">Log In</a>
                    <a href="{{ url('/booking') }}" class="w-full text-center bg-brand-500 hover:bg-brand-600 text-white rounded-xl py-2 text-sm font-semibold">Enroll Now</a>
                </div>
            @endguest
        </div>
    </nav>
</div>
