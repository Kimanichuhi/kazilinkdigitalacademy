<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password | {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-black text-white">Forgot Password?</h1>
                <p class="text-gray-400 mt-1">We'll email you a link to reset it</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-brand-600">{{ session('status') }}</div>
                @endif

                <p class="text-sm text-muted-foreground mb-4">
                    Enter your email address and we'll send you a link to reset your password.
                </p>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-medium mb-1.5 block">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@email.com"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white gap-2 inline-flex items-center justify-center font-semibold px-6 py-2.5 rounded-xl transition-colors">
                        Email Password Reset Link
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        <a href="{{ route('login') }}" class="text-brand-600 hover:underline font-medium">&larr; Back to Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
