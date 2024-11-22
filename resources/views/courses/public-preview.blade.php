@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Course Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Course Image -->
            @if($course->image)
                <div class="aspect-video w-full">
                    <img src="{{ asset('storage/' . $course->image) }}" 
                         alt="{{ $course->title }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Course Content -->
            <div class="p-6 sm:p-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-orange-50 text-orange-700 text-sm font-medium rounded-full">
                            {{ $course->sections->count() }} {{ Str::plural('Module', $course->sections->count()) }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>
                    <p class="text-gray-600">{{ $course->description }}</p>
                </div>

                <!-- Preview Sections -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Content</h2>
                    
                    <!-- Free Preview Modules -->
                    <div class="space-y-3 mb-6">
                        @foreach($previewSections as $section)
                            <a href="{{ route('courses.preview.section', ['course' => $course, 'section' => $section]) }}" 
                               class="group block p-4 border border-gray-100 rounded-xl hover:border-orange-100 hover:bg-orange-50 transition-all duration-200">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-orange-100 text-orange-700 font-medium flex items-center justify-center">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-gray-900 group-hover:text-orange-700 transition-colors duration-200 font-medium">
                                            {{ $section->title }}
                                        </h3>
                                        <p class="text-sm text-green-600 mt-1">Free Preview Available</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Locked Modules -->
                    <div class="space-y-3">
                        @foreach($upcomingSections as $section)
                            <div class="group p-4 border border-gray-100 rounded-xl bg-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 text-gray-500 font-medium flex items-center justify-center">
                                        {{ $loop->iteration + 2 }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-gray-500">
                                            {{ $section->title }}
                                        </h3>
                                        <p class="text-sm text-gray-400 mt-1">Available after enrollment</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($course->sections->count() > 5)
                        <p class="mt-4 text-sm text-gray-500 text-center">
                            Plus {{ $course->sections->count() - 5 }} more modules available after enrollment
                        </p>
                    @endif
                </div>

                <!-- Call to Action -->
                <div class="flex flex-col items-center border-t border-gray-100 pt-8">
                    @auth
                        @if(auth()->user()->courses->contains($course->id))
                            <a href="{{ route('courses.show', $course) }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl bg-orange-600 text-white font-medium hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                Continue Learning
                            </a>
                        @else
                            <form action="{{ route('courses.enroll', $course) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl bg-orange-600 text-white font-medium hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                    Enroll Now
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="w-full flex flex-col items-center gap-4">
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl bg-orange-600 text-white font-medium hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                Sign in to Enroll
                            </a>
                            <span class="text-sm text-gray-500">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700">
                                    Create one now
                                </a>
                            </span>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
