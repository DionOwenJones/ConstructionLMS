@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 bg-gray-50">
    <!-- Page Header with Quick Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Dashboard Overview</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Course
            </a>
            <a href="{{ route('admin.reports') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                View Reports
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Stats Card -->
        <div class="relative overflow-hidden bg-white rounded-lg shadow group">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-orange-600/10 opacity-50"></div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Business Revenue</p>
                        <p class="mt-1 text-3xl font-bold text-orange-600">£{{ $stats['business_revenue'] }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center mt-4 text-sm font-medium text-orange-600 hover:text-orange-700">
                    View Details
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Users Stats Card -->
        <div class="relative overflow-hidden bg-white rounded-lg shadow group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/10 opacity-50"></div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center mt-4 text-sm font-medium text-blue-600 hover:text-blue-700">
                    Manage Users
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Courses Stats Card -->
        <div class="relative overflow-hidden bg-white rounded-lg shadow group">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-600/10 opacity-50"></div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Courses</p>
                        <p class="mt-1 text-3xl font-bold text-green-600">{{ $stats['total_courses'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center mt-4 text-sm font-medium text-green-600 hover:text-green-700">
                    Manage Courses
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="relative overflow-hidden bg-white rounded-lg shadow group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-purple-600/10 opacity-50"></div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="mt-1 text-3xl font-bold text-purple-600">£{{ number_format($stats['monthly_revenue']->last()['revenue'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center mt-4 text-sm font-medium text-purple-600 hover:text-purple-700">
                    View Analytics
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Users -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">View All</a>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($stats['recent_users'] as $user)
                <li class="p-6 transition duration-150 ease-in-out hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-full">
                            <span class="text-lg font-medium text-orange-700">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Recent Courses -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Courses</h3>
                <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">View All</a>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($stats['recent_courses'] as $course)
                <li class="p-6 transition duration-150 ease-in-out hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                            <p class="text-sm text-gray-500">£{{ number_format($course->price, 2) }}</p>
                        </div>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            Edit
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
