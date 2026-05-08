<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Prevent caching during exam --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>{{ config('app.name', 'UJIANKU-CBT') }} - @yield('title', 'Ujian')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-base-100 min-h-screen select-none"
    oncontextmenu="return false"
    oncopy="return false"
    oncut="return false"
    onpaste="return false">

    <!-- Exam Header Bar -->
    <div class="navbar bg-primary text-primary-content shadow-lg px-4 sticky top-0 z-50">
        <div class="navbar-start">
            <span class="font-bold text-lg">{{ config('app.name', 'UJIANKU-CBT') }}</span>
        </div>
        <div class="navbar-center">
            @yield('exam-title')
        </div>
        <div class="navbar-end">
            @yield('exam-timer')
        </div>
    </div>

    <!-- Exam Content -->
    <main class="container mx-auto max-w-4xl px-4 py-6">
        {{ $slot }}
        @yield('content')
    </main>

    @stack('scripts')

    {{-- Anti-cheat: disable keyboard shortcuts --}}
    <script>
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S, PrintScreen
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 's') ||
                e.key === 'PrintScreen'
            ) {
                e.preventDefault();
                return false;
            }
        });

        // Detect tab/window visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Report tab switch (implement via AJAX in each exam page)
                window._tabSwitchCount = (window._tabSwitchCount || 0) + 1;
            }
        });
    </script>
</body>
</html>
