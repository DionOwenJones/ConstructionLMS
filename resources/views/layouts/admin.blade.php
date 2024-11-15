<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ConstructionTraining</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-1 min-h-0 bg-gray-900">
                <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center px-4 py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/1785/1785210.png"
                             alt="Construction Training Logo"
                             class="w-10 h-10">
                        <span class="ml-3 text-xl font-semibold text-white">Construction Training</span>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-2 mt-8 space-y-2">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="flex-shrink-0 w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="flex-1">Dashboard</span>
                        </a>

                        <a href="{{ route('admin.courses.index') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.courses.*') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="flex-shrink-0 w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="flex-1">Courses</span>
                            <span class="px-2 py-0.5 ml-3 text-xs font-medium text-white bg-gray-800 rounded-full">{{ \App\Models\Course::count() }}</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.users.*') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="flex-shrink-0 w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="flex-1">Users</span>
                            <span class="px-2 py-0.5 ml-3 text-xs font-medium text-white bg-gray-800 rounded-full">{{ \App\Models\User::count() }}</span>
                        </a>
                    </nav>
                </div>

                <!-- User Menu -->
                <div class="flex items-center p-4 border-t border-gray-800">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-orange-500 rounded-full">
                            <span class="font-medium text-white">{{ auth()->user()->name[0] }}</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-white">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1">
            <!-- Top Bar -->
            <div class="sticky top-0 z-10 flex h-16 bg-white shadow">
                <button type="button" class="px-4 text-gray-500 border-r border-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex justify-between flex-1 px-4">
                    <div class="flex flex-1">
                        <h2 class="my-auto text-xl font-semibold text-gray-900">
                            @yield('title', 'Dashboard')
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
