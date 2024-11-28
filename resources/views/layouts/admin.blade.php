<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Construction LMS') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gray-100">
    <!-- Navigation -->
    @include('layouts.admin-navigation')
    
    <!-- Page Content -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
        @yield('content')
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>
