<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirm Password | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-core::clarity />
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center mb-6">
                    <div class="bg-white rounded-lg px-3 py-2">
                        <img src="{{ asset('kazilink-logo.png') }}" alt="Kazilink Digital Academy" class="h-12 w-auto">
                    </div>
                </a>
                <h1 class="text-3xl font-black text-white">Confirm Password</h1>
                <p class="text-gray-400 mt-1">This is a secure area — please confirm it's you</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="password" class="text-sm font-medium mb-1.5 block">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autofocus
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white gap-2 inline-flex items-center justify-center font-semibold px-6 py-2.5 rounded-xl transition-colors">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
