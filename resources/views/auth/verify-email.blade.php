<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email | {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-black text-white">Verify Your Email</h1>
                <p class="text-gray-400 mt-1">One more step to secure your account</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 text-sm font-medium text-brand-600">
                        A new verification link has been sent to the email address you provided during registration.
                    </div>
                @endif

                <p class="text-sm text-muted-foreground mb-6">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>

                <div class="flex items-center justify-between gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white gap-2 inline-flex items-center justify-center font-semibold px-4 py-2.5 rounded-xl transition-colors text-sm">
                            Resend Verification Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-muted-foreground hover:text-foreground hover:underline">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
