@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-4">{{ $course->title }}</h2>
                    
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
                    @endif

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Course Overview</h3>
                                <p class="text-gray-600 mt-1">{{ $course->description }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-orange-600 mb-2">{{ $course->formatted_price }}</div>
                                <a href="{{ route('courses.purchase', $course) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                    Purchase Course
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-lg font-semibold text-gray-900">{{ $course->sections->count() }}</div>
                                <div class="text-sm text-gray-600">Sections</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-lg font-semibold text-gray-900">{{ $course->estimated_hours ?? '2' }}</div>
                                <div class="text-sm text-gray-600">Hours</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-lg font-semibold text-gray-900">{{ $course->difficulty ?? 'Beginner' }}</div>
                                <div class="text-sm text-gray-600">Level</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-lg font-semibold text-gray-900">{{ $course->language ?? 'English' }}</div>
                                <div class="text-sm text-gray-600">Language</div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Content Preview -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">Course Content Preview</h3>
                        <div class="space-y-4">
                            @foreach($previewSections as $section)
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                                    <span class="text-orange-600 font-semibold">{{ $loop->iteration }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">{{ $section->title }}</h4>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Preview</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($course->sections->count() > 3)
                                <div class="text-center py-4">
                                    <p class="text-gray-600">Plus {{ $course->sections->count() - 3 }} more sections</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Purchase Call to Action -->
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Ready to Start Learning?</h3>
                        <p class="text-gray-600 mb-6">Gain access to the full course content and start your learning journey today.</p>
                        <a href="{{ route('courses.purchase', $course) }}" 
                           class="inline-flex items-center px-6 py-3 bg-orange-600 text-white text-lg font-medium rounded-xl hover:bg-orange-700 transition-colors">
                            Purchase Now for {{ $course->formatted_price }}
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
