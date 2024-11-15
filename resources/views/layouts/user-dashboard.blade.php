<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - ConstructionTraining</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div x-data="{ mobileMenu: false, profileMenu: false }" class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <svg class="h-8 w-8 text-orange-500" viewBox="0 0 24 24" fill="none">
                                <!-- Your SVG path -->
                            </svg>
                            <span class="ml-2 text-lg font-bold text-gray-900">Construction<span class="text-orange-500">Training</span></span>
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-md text-gray-700 hover:text-orange-500">
                            <span class="sr-only">Open menu</span>
                            <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop profile -->
                    <div class="hidden md:flex md:items-center">
                        <div class="relative" @click.away="profileMenu = false">
                            <button @click="profileMenu = !profileMenu" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                <span class="font-medium">{{ Auth::user()->name }}</span>
                            </button>

                            <!-- Profile dropdown -->
                            <div x-show="profileMenu"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenu" class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 border-t">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50">
                        Dashboard
                    </a>
                    <a href="{{ route('courses.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50">
                        My Courses
                    </a>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 hidden md:block w-64">
            <div class="h-full bg-white shadow-lg pt-16">
                @include('layouts.partials.user-sidebar')
            </div>
        </div>

        <!-- Main content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1 py-6 px-4 sm:px-6 md:px-8 pt-16">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
