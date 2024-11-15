@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-gray-900">
        <div class="absolute inset-0 overflow-hidden">
            @if($course->image)
                <img src="{{ asset('storage/' . $course->image) }}"
                     alt="{{ $course->title }}"
                     class="w-full h-full object-cover opacity-40">
            @endif
            <div class="absolute inset-0 bg-gray-900/60"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $course->title }}
                </h1>
                <p class="mt-6 text-xl text-gray-300 max-w-3xl mx-auto">
                    {{ $course->description }}
                </p>
            </div>
        </div>
    </div>

    <!-- Course Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-8">
                <div class="space-y-8">
                    <!-- Course Overview -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Overview</h2>
                        <div class="prose max-w-none">
                            {!! $course->description !!}
                        </div>
                    </div>

                    <!-- Preview Sections -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Content Preview</h2>
                        <div class="space-y-4">
                            @foreach($previewSections as $section)
                                <div class="flex items-center p-4 border rounded-lg">
                                    <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-gray-900">{{ $section->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 mt-8 lg:mt-0">
                <div class="sticky top-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="text-center">
                            <p class="text-4xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</p>
                            <div class="mt-6">
                                @auth
                                    @if($isEnrolled)
                                        <a href="{{ route('courses.view', $course) }}"
                                           class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            Continue Learning
                                        </a>
                                    @else
                                        <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                                Enroll Now
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                       class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                        Login to Enroll
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Sections</span>
                                    <span class="text-gray-900 font-medium">{{ $totalSections }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Instructor</span>
                                    <span class="text-gray-900 font-medium">{{ $course->user->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
