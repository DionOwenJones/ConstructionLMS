@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Welcome Section -->
        <div class="mb-12">
            @auth
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
            @else
                <h1 class="text-3xl font-bold text-gray-900">Welcome to Our Learning Platform</h1>
            @endauth
            <p class="mt-2 text-gray-600">Continue your learning journey</p>
        </div>

        @auth
            @php
                $enrolledCourses = Auth::user()->courses()->withCount('sections')->get();
            @endphp

            @if($enrolledCourses->count() > 0)
                <!-- In Progress Courses -->
                @php
                    $inProgressCourses = $enrolledCourses->where('pivot.completed', false);
                    $completedCourses = $enrolledCourses->where('pivot.completed', true);
                @endphp

                @if($inProgressCourses->count() > 0)
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">In Progress</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($inProgressCourses as $course)
                                <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
                                    <!-- Course Image -->
                                    <div class="relative aspect-video bg-gray-100">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}"
                                                 alt="{{ $course->title }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <!-- Progress Bar -->
                                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-200">
                                            @php
                                                $totalSections = $course->sections_count;
                                                $completedSections = $course->pivot->completed_sections_count ?? 0;
                                                $progress = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;
                                            @endphp
                                            <div class="h-full bg-orange-500" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-500">
                                                {{ $course->pivot->completed_sections_count }} of {{ $course->sections->count() }} sections completed
                                            </div>
                                            <a href="{{ route('courses.view', ['id' => $course->id]) }}"
                                               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700">
                                                Continue Learning
                                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($completedCourses->count() > 0)
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Completed Courses</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($completedCourses as $course)
                                <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
                                    <!-- Course Image -->
                                    <div class="relative aspect-video bg-gray-100">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}"
                                                 alt="{{ $course->title }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <!-- Completed Badge -->
                                        <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            Completed
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-500">
                                                @if($course->pivot->completed)
                                                    Completed {{ Carbon::parse($course->pivot->completed_at)->diffForHumans() }}
                                                @else
                                                    {{ $course->pivot->completed_sections_count }} of {{ $course->sections->count() }} sections completed
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                @if($course->pivot->completed)
                                                    <a href="{{ route('certificates.download', ['id' => $course->id]) }}"
                                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Download Certificate
                                                    </a>
                                                @endif
                                                <a href="{{ route('courses.view', ['id' => $course->id]) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    @if($course->pivot->completed)
                                                        Review Course
                                                    @else
                                                        Continue Course
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- No Courses State -->
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Start Your Learning Journey</h2>
                        <p class="text-gray-600 mb-8">Explore our courses and begin learning new skills today.</p>
                        <a href="{{ route('courses.index') }}"
                           class="inline-block px-8 py-4 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                            Browse Courses
                        </a>
                    </div>
                </div>
            @endif
        @else
            <!-- Not Authenticated State -->
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to Our Learning Platform</h2>
                    <p class="text-gray-600 mb-8">Please log in to access your courses and track your progress.</p>
                    <a href="{{ route('login') }}"
                       class="inline-block px-8 py-4 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                        Login
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection
