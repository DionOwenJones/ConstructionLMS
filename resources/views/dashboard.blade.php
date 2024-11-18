@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Str::title(Auth::user()->name) }}!</h1>
                <p class="mt-1 text-sm text-gray-600">Pick up where you left off</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-4">
                <a href="{{ route('certificates.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    My Certificates
                </a>
                <a href="{{ route('courses.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-xl hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Explore Courses
                </a>
            </div>
        </div>

        @php
            // Get enrolled courses
            $enrolledCourses = $enrolledCourses ?? collect();
            
            // Split into in-progress and completed
            $inProgressCourses = $enrolledCourses->filter(function($course) {
                return !$course->completed;
            });
            
            $completedCourses = $enrolledCourses->filter(function($course) {
                return $course->completed;
            });
        @endphp

        @if($enrolledCourses->count() > 0)
            <!-- Course Grid -->
            <div class="grid grid-cols-1 gap-8">
                <!-- In Progress Courses -->
                @if($inProgressCourses->count() > 0)
                    <section>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Continue Learning</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($inProgressCourses as $course)
                                <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
                                    <div class="p-6">
                                        <div class="flex items-start space-x-4">
                                            <!-- Course Icon/Image -->
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-16 rounded-xl bg-orange-100 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Course Info -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $course->title }}</h3>
                                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($course->description, 100) }}</p>
                                                
                                                <!-- Progress Bar -->
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                                    @php
                                                        $progress = ($course->completed_sections_count / $course->sections_count) * 100;
                                                    @endphp
                                                    <div class="bg-orange-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <p class="text-xs text-gray-600">{{ $course->completed_sections_count }} of {{ $course->sections_count }} sections completed</p>
                                                
                                                <!-- Continue Button -->
                                                <div class="mt-4">
                                                    <a href="{{ route('courses.show', $course->id) }}" 
                                                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700">
                                                        Continue Learning
                                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Completed Courses -->
                @if($completedCourses->count() > 0)
                    <section>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Completed Courses</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($completedCourses as $course)
                                <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
                                    <div class="p-6">
                                        <div class="flex items-start space-x-4">
                                            <!-- Course Icon/Image -->
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Course Info -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $course->title }}</h3>
                                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($course->description, 100) }}</p>
                                                <p class="text-sm text-gray-500">
                                                    Completed on {{ Carbon::parse($course->completed_at)->format('F j, Y') }}
                                                </p>
                                                
                                                <!-- Certificate Button -->
                                                <div class="mt-4">
                                                    <a href="{{ route('certificates.download', $course->id) }}" 
                                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                                                        Download Certificate
                                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        @else
            <!-- No Courses State -->
            <div class="bg-white rounded-2xl shadow-sm p-12">
                <div class="max-w-sm mx-auto text-center">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Courses Yet</h3>
                    <p class="text-gray-500 mb-6">Start your learning journey by exploring our available courses.</p>
                    <a href="{{ route('courses.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-xl hover:bg-orange-700">
                        Browse Courses
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
