@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gray-900">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80"
            alt="Construction Site" class="object-cover w-full h-full opacity-30">
    </div>
    <div class="relative px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8 lg:py-32">
        <div class="text-center lg:text-left">
            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
                Professional Construction Training
            </h1>
            <p class="mt-4 text-base text-gray-300 sm:text-lg md:text-xl lg:max-w-3xl">
                Expert-led training programs to advance your construction career.
            </p>
            <div class="mt-8 flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4 justify-center lg:justify-start">
                <a href="{{ route('courses.index') }}" class="text-orange-500 hover:text-orange-600 font-medium">
                    View All Courses →
                </a>
                <a href="/register" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border-2 border-white text-white hover:bg-white hover:text-gray-900 font-medium rounded-lg transition-all">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Featured Courses -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Featured Courses</h2>
                <p class="mt-2 text-gray-600">Browse our available training programs</p>
            </div>
            <a href="/courses" class="text-orange-500 hover:text-orange-600 font-medium">
                View All Courses →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredCourses as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white">Ready to Start Learning?</h2>
        <p class="mt-4 text-xl text-gray-300 max-w-2xl mx-auto">
            Advance your career with our professional training courses.
        </p>
        <a href="/register" class="mt-8 inline-flex items-center px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
            Get Started Today
        </a>
    </div>
</section>
@endsection
