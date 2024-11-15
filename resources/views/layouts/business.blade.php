<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Business Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation.business')

        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
