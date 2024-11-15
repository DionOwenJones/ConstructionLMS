@extends('layouts.app')

@section('content')
<!-- Hero Section with Gradient Background -->
<div class="relative overflow-hidden bg-gradient-to-br from-orange-600 to-orange-800">
    <!-- Decorative blob shapes -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-orange-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Hero Content -->
            <div class="text-center lg:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    Master Construction Skills
                    <span class="block text-orange-200">Transform Your Career</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl text-orange-100 max-w-2xl mx-auto lg:mx-0">
                    Expert-led training programs designed to advance your construction career. Learn from industry professionals and gain practical skills.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('courses.index') }}"
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-orange-400 bg-orange-400 text-white rounded-xl text-lg font-semibold hover:bg-orange-500 hover:border-orange-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        Explore Courses
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white rounded-xl text-lg font-semibold hover:bg-white hover:text-orange-600 transition-all duration-200 transform hover:-translate-y-0.5">
                        Get Started
                    </a>
                </div>
                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">{{ $totalCourses ?? '20+' }}</div>
                        <div class="text-sm text-orange-200">Courses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">{{ $totalStudents ?? '1000+' }}</div>
                        <div class="text-sm text-orange-200">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">{{ $totalInstructors ?? '15+' }}</div>
                        <div class="text-sm text-orange-200">Instructors</div>
                    </div>
                </div>
            </div>
            <!-- Hero Image -->
            <div class="relative lg:block">
                <img src="https://images.unsplash.com/photo-1581094794329-c8112c37e5ab?q=80&w=2070"
                     alt="Construction Training"
                     class="w-full rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-600/30 to-orange-800/30"></div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Courses Section -->
<div class="bg-gray-50 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Featured Courses</h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                Start your journey with our most popular construction training programs
            </p>
        </div>

        <!-- Course Grid -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredCourses as $course)
                <x-course-card :course="$course" :enrolledCourseIds="$enrolledCourseIds" />
            @empty
                <div class="col-span-3">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                        <p class="mt-1 text-sm text-gray-500">Check back later for new courses.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center justify-center px-8 py-4 border border-transparent rounded-xl text-lg font-semibold text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                View All Courses
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Why Choose Us</h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                We provide comprehensive construction training with industry-leading features
            </p>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-orange-400 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                <div class="relative p-8 bg-white ring-1 ring-gray-900/5 rounded-2xl leading-none flex items-top justify-start space-x-6">
                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <div class="space-y-2">
                        <p class="text-xl font-semibold text-gray-900">Expert Instructors</p>
                        <p class="text-gray-600">Learn from industry professionals with years of experience</p>
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-orange-400 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                <div class="relative p-8 bg-white ring-1 ring-gray-900/5 rounded-2xl leading-none flex items-top justify-start space-x-6">
                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div class="space-y-2">
                        <p class="text-xl font-semibold text-gray-900">Practical Learning</p>
                        <p class="text-gray-600">Hands-on training with real-world applications</p>
                    </div>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-orange-400 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                <div class="relative p-8 bg-white ring-1 ring-gray-900/5 rounded-2xl leading-none flex items-top justify-start space-x-6">
                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="space-y-2">
                        <p class="text-xl font-semibold text-gray-900">Certification</p>
                        <p class="text-gray-600">Earn industry-recognized certificates upon completion</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-br from-orange-600 to-orange-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white">Ready to Start Your Journey?</h2>
            <p class="mt-4 text-lg text-orange-100 max-w-2xl mx-auto">
                Join thousands of professionals who have transformed their careers through our training programs
            </p>
            <div class="mt-8">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white rounded-xl text-lg font-semibold hover:bg-white hover:text-orange-600 transition-all duration-200 transform hover:-translate-y-0.5">
                    Get Started Today
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add custom styles for animations -->
<style>
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
@endsection