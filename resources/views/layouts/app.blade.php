<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @auth
            @if(Auth::user()->isAdmin())
                @include('layouts.navigation.admin')
            @elseif(Auth::user()->business)
                @include('layouts.navigation.business')
            @else
                @include('layouts.navigation.user')
            @endif
        @else
            @include('layouts.navigation.guest')
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>


    @stack('scripts')
</body>
</html>
