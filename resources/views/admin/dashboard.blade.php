@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-100">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
        <!-- Courses Card -->
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-500 bg-opacity-75 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm text-gray-600">Total Courses</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 bg-opacity-75 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm text-gray-600">Total Users</h2>
                    <p class="text-2xl font-semibold text-gray-700">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 bg-opacity-75 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm text-gray-600">Total Revenue</h2>
                    <p class="text-2xl font-semibold text-gray-700">${{ number_format($stats['total_revenue'] / 100, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Recent Courses -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Courses</h3>
            </div>
            <div class="p-6">
                @foreach($stats['recent_courses'] as $course)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="object-cover w-12 h-12 rounded">
                            @else
                                <div class="flex items-center justify-center w-12 h-12 bg-gray-200 rounded">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $course->title }}</h4>
                                <p class="text-sm text-gray-500">
                                    by {{ $course->teacher?->name ?? 'Unknown Teacher' }}
                                </p>
                            </div>
                            <div class="ml-auto">
                                <span class="text-sm font-medium text-orange-600">${{ number_format($course->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
            </div>
            <div class="p-6">
                @foreach($stats['recent_users'] as $user)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full">
                                <span class="text-lg font-medium text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <div class="ml-auto">
                                <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
