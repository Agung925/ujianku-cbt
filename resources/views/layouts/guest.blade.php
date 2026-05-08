<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UJIANKU-CBT') }} - @yield('title', 'Login')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

        <!-- Logo / App Name -->
        <div class="mb-8 text-center">
            <a href="/" class="text-3xl font-bold text-primary">{{ config('app.name', 'UJIANKU-CBT') }}</a>
            <p class="mt-1 text-base-content/70 text-sm">Computer Based Test Platform</p>
        </div>

        <!-- Card -->
        <div class="card w-full max-w-md bg-base-100 shadow-xl">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-base-content/50 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

    @stack('scripts')
</body>
</html>
