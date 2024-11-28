<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'Construction Training')) - Professional Construction Training Courses</title>
    <meta name="description" content="@yield('meta_description', 'Professional construction training courses for industry professionals. Get certified and advance your career with our comprehensive training programs.')">
    <meta name="keywords" content="@yield('meta_keywords', 'construction training, professional certification, construction courses, safety training, construction education')">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.1/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        @auth
            @if(Auth::user()->isAdmin())
                @include('layouts.navigation.admin')
            @elseif(Auth::user()->isBusinessOwner())
                @include('layouts.navigation.business')
            @else
                @include('layouts.navigation.user')
            @endif
        @else
            @include('layouts.navigation.guest')
        @endauth

        <!-- Notifications -->
        @if (session('status'))
            <x-notification type="success" :message="session('status')" />
        @endif

        @if (session('success'))
            <x-notification type="success" :message="session('success')" />
        @endif

        @if (session('error'))
            <x-notification type="error" :message="session('error')" />
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <x-notification type="error" :message="$error" />
            @endforeach
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
