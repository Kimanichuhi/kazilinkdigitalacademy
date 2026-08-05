<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trainer Portal | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-black">Trainer Portal</h1>
        <p class="text-muted-foreground mt-1">Welcome, {{ $user->name }}</p>
        <p class="text-sm text-muted-foreground mt-4">
            This area did not exist in the source app — scaffolded empty and guarded per the migration brief.
        </p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="btn-outline">Sign Out</button>
        </form>
    </div>
</body>
</html>
