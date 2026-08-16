@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | Admin | '.config('app.name') : 'Admin | '.config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,900&display=swap" rel="stylesheet" />
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-core::clarity />
</head>
<body class="font-sans antialiased">
    <div x-data="{ sidebarCollapsed: false, mobileSidebarOpen: false }" class="flex h-screen bg-background overflow-hidden">
        <x-admin::sidebar />
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="lg:hidden flex items-center gap-3 h-14 px-4 border-b border-border bg-background shrink-0">
                <button @click="mobileSidebarOpen = true" class="p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" aria-label="Open menu">
                    <x-core::icon name="menu" class="w-5 h-5" />
                </button>
                <span class="font-bold text-sm">Admin</span>
            </header>
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
