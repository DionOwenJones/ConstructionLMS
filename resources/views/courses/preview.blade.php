@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Course Header -->
    <div class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">{{ $course->title }}</h1>
                <div class="flex items-center space-x-4">
                    <span class="px-4 py-2 bg-orange-100 text-orange-800 rounded-full font-medium">
                        Preview Mode
                    </span>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.courses.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Back to Admin
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="col-span-2">
                <!-- Course Description -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">About This Course</h2>
                    <p class="text-gray-600">{{ $course->description }}</p>
                </div>

                <!-- Course Sections -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold mb-4">Course Content</h2>
                    <div class="space-y-4">
                        @foreach($course->sections as $section)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-500 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $section->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $section->content }}</p>
                                    </div>
                                    @if($section->image)
                                        <img src="{{ Storage::url($section->image) }}"
                                             alt="Section image"
                                             class="w-24 h-24 object-cover rounded-lg">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                    <!-- Course Info -->
                    <div class="mb-6">
                        <div class="aspect-video rounded-lg overflow-hidden mb-4">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}"
                                     alt="{{ $course->title }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="text-3xl font-bold text-orange-500 mb-4">
                            ${{ number_format($course->price, 2) }}
                        </div>
                        <button class="w-full bg-orange-500 text-white rounded-lg px-4 py-2 font-medium hover:bg-orange-600 transition-colors">
                            Enroll Now
                        </button>
                    </div>

                    <!-- Course Stats -->
                    <div class="border-t pt-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span class="text-gray-600">{{ $course->sections->count() }} sections</span>
                            </div>
                            <div class="flex items-center">
                                @if($course->user)
                                    <img src="{{ $course->user->profile_photo_url }}" alt="{{ $course->user->name }}" class="w-10 h-10 rounded-full">
                                    <span class="ml-3 text-gray-700">{{ $course->user->name }}</span>
                                @else
                                    <span class="text-gray-700">Unknown Author</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
