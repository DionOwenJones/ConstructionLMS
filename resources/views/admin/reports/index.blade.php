@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Reports Dashboard</h2>
        <p class="mt-1 text-sm text-gray-600">Comprehensive analytics and insights for your learning management system.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Revenue -->
        <div class="p-6 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                    <p class="text-lg font-semibold text-gray-900">£{{ number_format(\App\Models\Order::sum('total_amount'), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="p-6 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format(\App\Models\User::count()) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Courses -->
        <div class="p-6 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active Courses</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format(\App\Models\Course::where('status', 'published')->count()) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="p-6 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Enrollments</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format(DB::table('course_user')->count()) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Revenue Report Card -->
        <div class="overflow-hidden transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Revenue Report</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-medium text-orange-800 bg-orange-100 rounded-full">Real-time</span>
                </div>
                <p class="mb-6 text-sm text-gray-600">Track your revenue performance with detailed insights into sales, trends, and business metrics.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.reports.revenue') }}" class="inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-700">
                        View Report
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <span class="text-xs text-gray-500">Updated daily</span>
                </div>
            </div>
        </div>

        <!-- Users Report Card -->
        <div class="overflow-hidden transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Users Report</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">Analytics</span>
                </div>
                <p class="mb-6 text-sm text-gray-600">Monitor user growth, engagement metrics, and analyze user behavior patterns.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.reports.users') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                        View Report
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <span class="text-xs text-gray-500">Live data</span>
                </div>
            </div>
        </div>

        <!-- Courses Report Card -->
        <div class="overflow-hidden transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Courses Report</h3>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full">Insights</span>
                </div>
                <p class="mb-6 text-sm text-gray-600">Analyze course performance, completion rates, and student engagement metrics.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.reports.courses') }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-700">
                        View Report
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <span class="text-xs text-gray-500">Updated hourly</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
