@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-orange-50/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Course Header -->
        <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100/50 backdrop-blur-xl mb-8 overflow-hidden transition-all duration-300 hover:shadow-xl">
            @if($course->image)
                <div class="relative h-80">
                    <img src="{{ asset('storage/' . $course->image) }}" 
                         alt="{{ $course->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <h1 class="text-4xl font-bold text-white mb-3 leading-tight">{{ $course->title }}</h1>
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 shadow-sm">
                                Preview Mode
                            </span>
                            <span class="text-white/90 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ $course->sections->count() }} {{ Str::plural('Section', $course->sections->count()) }}
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 bg-gradient-to-br from-orange-50 to-white">
                    <h1 class="text-4xl font-bold text-gray-900 mb-3 leading-tight">{{ $course->title }}</h1>
                    <div class="flex items-center gap-4 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 shadow-sm">
                            Preview Mode
                        </span>
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            {{ $course->sections->count() }} {{ Str::plural('Section', $course->sections->count()) }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Content Grid -->
        <div class="grid grid-cols-12 gap-8">
            <!-- Main Content Area -->
            <div class="col-span-12 lg:col-span-8 space-y-8">
                <!-- Course Description -->
                <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <div class="p-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">About This Course</h2>
                        <div class="prose prose-orange max-w-none text-gray-600">
                            {!! $course->description !!}
                        </div>
                    </div>
                </div>

                <!-- Preview Sections -->
                <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <div class="p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900">Course Content</h2>
                            <span class="text-gray-600 bg-gray-50 px-3 py-1 rounded-full text-sm">
                                {{ $course->sections->count() }} {{ Str::plural('section', $course->sections->count()) }}
                            </span>
                        </div>

                        <!-- Available Preview Sections -->
                        <div class="space-y-4">
                            @foreach($previewSections as $section)
                                <a href="{{ route('courses.preview.section', ['course' => $course, 'section' => $section]) }}" 
                                   class="block group">
                                    <div class="p-5 border border-gray-100 rounded-xl bg-white hover:border-orange-200 hover:bg-orange-50/30 transition-all duration-200 transform hover:-translate-y-0.5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-orange-50 text-orange-600 font-medium flex items-center justify-center shadow-sm group-hover:bg-orange-100 transition-colors duration-200">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-gray-900 group-hover:text-orange-600 font-medium truncate transition-colors duration-200">
                                                    {{ $section->title }}
                                                </h3>
                                                @if($section->description)
                                                    <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ $section->description }}</p>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            <!-- Upcoming Sections -->
                            @foreach($upcomingSections as $section)
                                <div class="p-5 border border-gray-100 rounded-xl bg-gray-50/70">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-100 text-gray-400 font-medium flex items-center justify-center">
                                            {{ $loop->iteration + count($previewSections) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-gray-500 font-medium truncate flex items-center gap-2">
                                                {{ $section->title }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 lg:col-span-4 space-y-8">
                <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden sticky top-8 transition-all duration-300 hover:shadow-xl">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="text-3xl font-bold text-orange-600">{{ $course->formatted_price }}</div>
                            <span class="text-sm font-medium px-3 py-1 bg-orange-50 text-orange-800 rounded-full">Preview Mode</span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 text-gray-600 bg-gray-50/70 p-3 rounded-xl">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $course->estimated_hours ?? '2' }} hours of content</span>
                            </div>
                            <div class="flex items-center gap-3 text-gray-600 bg-gray-50/70 p-3 rounded-xl">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $course->sections->count() }} comprehensive sections</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('courses.purchase', $course) }}" 
                               class="block w-full text-center px-6 py-4 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
                                Purchase Full Course
                            </a>
                        </div>

                        <div class="mt-6 p-4 bg-orange-50 border border-orange-100 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-orange-800">
                                    <span class="font-medium">Preview Access:</span> You can explore the first 2 sections for free. 
                                    Purchase the full course to unlock all content and features.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
