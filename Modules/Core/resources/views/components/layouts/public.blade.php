@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | '.config('app.name') : config('app.name').' - Learn. Earn. Thrive Online.' }}</title>
    <meta name="description" content="Kenya's #1 online income skills academy. Learn practical digital skills and start earning from anywhere.">
    <link rel="icon" href="{{ asset('favicon.jpeg') }}">
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-core::clarity />
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    <x-core::navbar />
    <main class="flex-1">
        {{ $slot }}
    </main>
    <x-core::footer />
    @livewireScripts
</body>
</html>
