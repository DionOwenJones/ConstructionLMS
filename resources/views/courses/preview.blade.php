@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-orange-600 to-orange-500 overflow-hidden" style="height: 500px;">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        @if($course->image)
            <img src="{{ asset('storage/' . $course->image) }}" 
                 alt="{{ $course->title }}" 
                 class="absolute inset-0 w-full h-full object-cover">
        @endif
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="max-w-3xl">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-200 text-orange-800">
                    Course Preview
                </span>
                <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">
                    {{ $course->title }}
                </h1>
                <p class="mt-6 text-xl text-orange-50">
                    {{ $course->description }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Course Overview -->
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">What You'll Learn</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600">Industry-standard best practices</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600">Practical, hands-on exercises</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600">Real-world applications</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600">Professional certification prep</span>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Content</h2>
                    <div class="space-y-4">
                        @foreach($course->sections as $index => $section)
                            <div class="group">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-orange-50 transition-all duration-200">
                                    <div class="flex items-center space-x-4">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-semibold">
                                            {{ $index + 1 }}
                                        </span>
                                        <h3 class="font-medium text-gray-900 group-hover:text-orange-600 transition-colors">
                                            {{ $section->title }}
                                        </h3>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-8">
                            <div class="flex items-baseline mb-6">
                                <span class="text-5xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                                <span class="ml-2 text-gray-500">/lifetime access</span>
                            </div>

                            @auth
                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-center px-8 py-4 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                        Enroll Now
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="block w-full text-center px-8 py-4 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                    Login to Enroll
                                </a>
                            @endauth

                            <!-- Course Stats -->
                            <div class="mt-8 space-y-4">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    {{ $course->sections->count() }} sections
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Lifetime Access
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Certificate of Completion
                                </div>
                            </div>
                        </div>

                        <!-- Instructor -->
                        @if($course->user)
                            <div class="border-t border-gray-100 p-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Instructor</h3>
                                <div class="flex items-center">
                                    <img src="{{ $course->user->profile_photo_url }}" 
                                         alt="{{ $course->user->name }}" 
                                         class="w-12 h-12 rounded-full">
                                    <div class="ml-4">
                                        <h4 class="font-medium text-gray-900">{{ $course->user->name }}</h4>
                                        <p class="text-sm text-gray-500">Professional Instructor</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
