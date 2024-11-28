@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Course Header Section -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            @if($course->image)
                <div class="relative h-72">
                    <img src="{{ asset('storage/' . $course->image) }}" 
                         alt="{{ $course->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                            <span class="text-white/90">{{ $course->sections->count() }} {{ Str::plural('Section', $course->sections->count()) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                        <span class="text-gray-600">{{ $course->sections->count() }} {{ Str::plural('Section', $course->sections->count()) }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Description Section -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Course</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! $course->description !!}
                </div>
            </div>
        </div>

        <!-- Course Sections Preview -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Course Content</h2>
                    <span class="text-sm text-gray-600">{{ $course->sections->count() }} {{ Str::plural('section', $course->sections->count()) }}</span>
                </div>
                
                <div class="space-y-4">
                    @foreach($previewSections as $section)
                        <div class="border rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $section->title }}</h3>
                            @if($section->description)
                                <p class="text-gray-600 text-sm mb-2">{{ $section->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($course->sections->count() > 3)
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">And {{ $course->sections->count() - 3 }} more sections...</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.courses.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Courses
            </a>
            
            <a href="{{ route('admin.courses.edit', $course->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Course
            </a>
        </div>
    </div>
@endsection