<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UJIANKU-CBT') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">

    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            @include('components.navbar')

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4">
                        <x-success-alert>{{ session('success') }}</x-success-alert>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4">
                        <x-error-alert>{{ session('error') }}</x-error-alert>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4">
                        <x-warning-alert>{{ session('warning') }}</x-warning-alert>
                    </div>
                @endif

                <!-- Page Header -->
                @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
                @endisset

                {{ $slot }}
            </main>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side z-20">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            @include('components.sidebar')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
