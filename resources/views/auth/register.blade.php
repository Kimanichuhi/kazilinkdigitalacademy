<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,900&display=swap" rel="stylesheet" />
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
                <h1 class="text-3xl font-black text-white">Create Your Account</h1>
                <p class="text-gray-400 mt-1">Start your journey to earning online</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ show: false }">
                    @csrf
                    <div>
                        <label for="full_name" class="text-sm font-medium mb-1.5 block">Full Name</label>
                        <input
                            id="full_name"
                            type="text"
                            name="full_name"
                            value="{{ old('full_name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Jane Wanjiku"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('full_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-medium mb-1.5 block">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="you@email.com"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="text-sm font-medium mb-1.5 block">Phone Number</label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            placeholder="+254 700 123 456"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="text-sm font-medium mb-1.5 block">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background pr-10"
                            >
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.26 9.26 0 0 0 5.39-1.61M2 2l20 20"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @else
                            <p class="text-muted-foreground text-xs mt-1">At least 12 characters, with upper &amp; lowercase letters, a number, and a symbol.</p>
                        @enderror
                    </div>
                    <div>
                        <label for="confirm_password" class="text-sm font-medium mb-1.5 block">Confirm Password</label>
                        <input
                            id="confirm_password"
                            :type="show ? 'text' : 'password'"
                            name="confirm_password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background"
                        >
                        @error('confirm_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2.5 pt-1">
                        <label class="flex items-start gap-2 text-sm text-muted-foreground">
                            <input type="checkbox" name="email_notifications_opt_in" value="1" {{ old('email_notifications_opt_in') ? 'checked' : '' }} class="mt-0.5 rounded border-border text-brand-500 focus:ring-brand-500">
                            <span>Send me email notifications about my courses and account</span>
                        </label>
                        <label class="flex items-start gap-2 text-sm text-muted-foreground">
                            <input type="checkbox" name="not_a_robot" value="1" required class="mt-0.5 rounded border-border text-brand-500 focus:ring-brand-500">
                            <span>I'm not a robot</span>
                        </label>
                        @error('not_a_robot')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                        <label class="flex items-start gap-2 text-sm text-muted-foreground">
                            <input type="checkbox" name="accept_terms" value="1" required class="mt-0.5 rounded border-border text-brand-500 focus:ring-brand-500">
                            <span>I accept the <a href="{{ url('/terms') }}" target="_blank" class="text-brand-600 hover:underline">Terms &amp; Conditions</a></span>
                        </label>
                        @error('accept_terms')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white gap-2 inline-flex items-center justify-center font-semibold px-6 py-2.5 rounded-xl transition-colors">
                        Create Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-brand-600 hover:underline font-medium">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
