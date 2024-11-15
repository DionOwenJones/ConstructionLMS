<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConstructionTraining {{ $title ? "- {$title}" : '' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Add Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            @apply bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-200;
        }

        .nav-link {
            @apply text-gray-700 hover:text-orange-500 px-4 py-2 text-sm font-medium transition-colors duration-200;
        }
    </style>

    {{ $styles ?? '' }}
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="px-4 mx-auto max-w-7xl">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        <svg class="h-10 w-10 text-orange-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 8L12 14L22 8L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 14L12 20L22 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 5.5L17 11.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="ml-3">
                            <span class="text-xl font-bold text-white">Construction</span>
                            <span class="text-xl font-light text-orange-500">Training</span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="/" class="nav-link">Home</a>
                    <a href="{{ route('courses.index') }}" class="nav-link">Courses</a>
                    <a href="/about" class="nav-link">About</a>
                    <a href="/contact" class="nav-link">Contact</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="nav-link">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="h-10 w-10 text-orange-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9L12 3L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 9L12 3L21 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="ml-2">
                            <span class="text-xl font-bold text-white">Construction</span>
                            <span class="text-xl font-light text-orange-500">Training</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400">Professional construction training for everyone.</p>
                </div>
                <!-- Footer content... (keeping the same as your original) -->
            </div>
        </div>
    </footer>

    {{ $scripts ?? '' }}
</body>
</html>
