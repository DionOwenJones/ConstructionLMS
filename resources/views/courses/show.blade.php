@extends('layouts.app')

@section('title', $course->title)

@section('meta_description', Str::limit(strip_tags($course->description), 160))

@section('meta_keywords', implode(', ', [
    $course->title,
    'construction training',
    'professional certification',
    'construction courses',
    'safety training',
    'construction education',
    $course->title . ' certification',
    'professional development'
]))

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 transform transition-all duration-300 ease-in-out z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2">
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-orange-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="min-h-screen bg-gradient-to-br from-orange-50/40 via-white to-orange-50/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Course Header -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg ring-1 ring-gray-100/50 mb-8 transition-all duration-300 hover:shadow-xl">
                <div class="p-6 sm:p-8">
                    <!-- Back and Progress -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4">
                        <div class="flex items-center space-x-4">
                            <button onclick="window.history.back()" 
                                    class="group p-2 -m-2 rounded-xl hover:bg-orange-50/50 transition-all duration-150">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </button>
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">{{ $course->title }}</h1>
                        </div>

                        <!-- Progress Bar -->
                        <div class="flex items-center gap-4 bg-orange-50/50 p-3 rounded-xl backdrop-blur-sm">
                            <div class="w-48 bg-orange-100/50 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-500 ease-out" 
                                     style="width: {{ $progress }}%">
                                </div>
                            </div>
                            <span class="text-sm font-medium text-orange-900/80 whitespace-nowrap">{{ number_format($progress) }}% Complete</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($sections) === 0)
                <!-- No Sections Message -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg ring-1 ring-gray-100/50 p-8">
                    <div class="text-center max-w-sm mx-auto">
                        <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">No content yet</h3>
                        <p class="mt-2 text-sm text-gray-500">This course doesn't have any sections yet. Check back later!</p>
                    </div>
                </div>
            @else
                <!-- Course Content Grid -->
                <div x-data="{ mobileMenuOpen: false }" class="grid grid-cols-12 gap-8">
                    <!-- Mobile Menu Toggle -->
                    <div class="lg:hidden col-span-12 mb-4">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                class="w-full flex items-center justify-between p-4 bg-white/80 backdrop-blur-xl rounded-xl shadow-sm ring-1 ring-gray-100/50 hover:bg-white/90 transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <span class="font-medium text-gray-900">Course Content</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-orange-600">{{ $progress }}% Complete</span>
                                <svg class="w-5 h-5 text-orange-400 transform transition-transform duration-200"
                                     :class="{ 'rotate-180': mobileMenuOpen }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Sections Sidebar -->
                    <div class="col-span-12 lg:col-span-4 space-y-8"
                         :class="{ 'block': mobileMenuOpen, 'hidden lg:block': !mobileMenuOpen }">
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden transition-all duration-300 lg:sticky lg:top-8">
                            <div class="p-6 sm:p-8">
                                <h2 class="text-xl font-semibold bg-gradient-to-r from-gray-900 to-gray-800 bg-clip-text text-transparent mb-6 hidden lg:block">Course Content</h2>
                                
                                @if(!$isEnrolled)
                                    <div class="mb-6 p-4 bg-orange-50/50 backdrop-blur-sm border border-orange-100/50 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-sm text-orange-900">
                                                <span class="font-medium">Preview Mode:</span> First 2 modules are free. 
                                                Enroll to access all content.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Sections List -->
                                <div class="space-y-3">
                                    @foreach($sections as $index => $section)
                                        @php
                                            $isLocked = !$isEnrolled && $index > 1;
                                        @endphp
                                        <a href="{{ $isLocked ? '#' : route($isEnrolled ? 'courses.show.section' : 'courses.preview.section', ['course' => $course, 'section' => $section]) }}"
                                           class="block group">
                                            <div class="relative flex items-center p-4 transition-all duration-150
                                                        {{ $currentSection && $section->id === $currentSection->id ? 'bg-orange-50/50 border border-orange-100/50 shadow-sm' : 'hover:bg-gray-50/50' }} 
                                                        {{ $isLocked ? 'opacity-60' : '' }} rounded-xl">
                                                <div class="flex-shrink-0 mr-4">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors duration-200
                                                                {{ in_array($section->id, $completedSections) 
                                                                    ? 'bg-green-100/50 text-green-600' 
                                                                    : ($currentSection && $section->id === $currentSection->id 
                                                                        ? 'bg-orange-100/50 text-orange-600'
                                                                        : 'bg-gray-100/50 text-gray-500') }}">
                                                        @if(in_array($section->id, $completedSections))
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        @elseif($isLocked)
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                        @else
                                                            {{ $index + 1 }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="text-sm font-medium text-gray-900 truncate group-hover:text-orange-600 transition-colors duration-150">
                                                        {{ $section->title }}
                                                    </h3>
                                                    @if($section->description)
                                                        <p class="mt-1 text-sm text-gray-500 truncate">{{ $section->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                @if($isEnrolled && count($completedSections) === count($sections) && !$course->completed)
                                    <div class="mt-8">
                                        <form action="{{ route('courses.complete', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-500 transform hover:scale-[1.02] transition-all duration-200 group">
                                                <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Complete Course
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if(!$isEnrolled)
                                    <div class="mt-8 p-6 bg-gradient-to-br from-orange-50/50 to-orange-100/30 rounded-xl border border-orange-200/50">
                                        <div class="text-center space-y-4">
                                            <div class="inline-flex items-center justify-center w-12 h-12 bg-white/80 rounded-xl shadow-sm mx-auto">
                                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Unlock Full Access</h3>
                                                <p class="mt-2 text-sm text-gray-600">Enroll now to access all course content and start your learning journey.</p>
                                            </div>
                                            <a href="{{ route('courses.purchase', $course) }}" 
                                               class="block w-full text-center px-6 py-3 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-500 transform hover:scale-[1.02] transition-all duration-200">
                                                Enroll Now
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($currentSection)
                        <div class="col-span-12 lg:col-span-8 space-y-8">
                            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden">
                                <!-- Section Header -->
                                <div class="p-6 sm:p-8 border-b border-gray-100">
                                    <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">{{ $currentSection->title }}</h2>
                                    @if($currentSection->description)
                                        <p class="mt-2 text-gray-600">{{ $currentSection->description }}</p>
                                    @endif
                                </div>

                                <!-- Section Content -->
                                <div class="p-6 sm:p-8 space-y-8">
                                    @forelse($currentSection->contentBlocks as $block)
                                        <div class="content-block">
                                            @switch($block->type)
                                                @case('text')
                                                    <div class="prose prose-orange max-w-none bg-white/80 rounded-xl overflow-hidden">
                                                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Reading Material
                                                        </div>
                                                        <div class="prose-sm sm:prose lg:prose-lg">
                                                            {!! $block->text_content !!}
                                                        </div>
                                                    </div>
                                                    @break

                                                @case('video')
                                                    <div class="bg-white/80 rounded-xl overflow-hidden">
                                                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 012 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span>Video Lesson</span>
                                                            @if($block->video_title)
                                                                <span class="text-gray-300">•</span>
                                                                <span class="font-medium text-gray-700">{{ $block->video_title }}</span>
                                                            @endif
                                                        </div>

                                                        @php
                                                            $videoUrl = $block->video_url;
                                                            $embedUrl = null;
                                                            
                                                            if ($videoUrl) {
                                                                // Convert any YouTube URL to embed URL
                                                                if (strpos($videoUrl, 'youtu.be/') !== false) {
                                                                    $id = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                                                                    $embedUrl = "https://www.youtube.com/embed/" . $id;
                                                                } elseif (strpos($videoUrl, 'youtube.com') !== false && strpos($videoUrl, 'watch?v=') !== false) {
                                                                    $id = substr($videoUrl, strpos($videoUrl, 'watch?v=') + 8);
                                                                    $id = explode('&', $id)[0];
                                                                    $embedUrl = "https://www.youtube.com/embed/" . $id;
                                                                } elseif (strpos($videoUrl, 'youtube.com/embed/') !== false) {
                                                                    $embedUrl = $videoUrl;
                                                                }
                                                            }
                                                        @endphp

                                                        @if($embedUrl)
                                                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 0.75rem;">
                                                                <iframe 
                                                                    src="{{ $embedUrl }}"
                                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                                                                    allowfullscreen>
                                                                </iframe>
                                                            </div>
                                                        @else
                                                            <div class="rounded-xl bg-gray-50 p-8">
                                                                <div class="text-center">
                                                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                    </svg>
                                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Video Not Available</h3>
                                                                    <p class="text-gray-500">The video could not be loaded at this time.</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @break

                                                @case('image')
                                                    <div class="bg-white/80 rounded-xl overflow-hidden">
                                                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                            </svg>
                                                            Image Content
                                                        </div>
                                                        @if($block->image_title)
                                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $block->image_title }}</h3>
                                                        @endif
                                                        <div class="rounded-xl overflow-hidden bg-gray-100">
                                                            <img src="{{ $block->image_url }}" 
                                                                 alt="{{ $block->image_title ?? 'Course image' }}"
                                                                 class="w-full h-auto">
                                                        </div>
                                                        @if($block->image_caption)
                                                            <div class="mt-4 text-sm text-gray-600">
                                                                {{ $block->image_caption }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @break

                                                @case('quiz')
                                                    <div class="bg-white/80 rounded-xl overflow-hidden">
                                                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Quiz
                                                        </div>
                                                        @if($block->quiz_title)
                                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $block->quiz_title }}</h3>
                                                        @endif
                                                        <div class="space-y-6">
                                                            @foreach($block->questions as $question)
                                                                <div class="p-4 bg-gray-50/50 rounded-xl">
                                                                    <p class="text-gray-900 font-medium mb-4">{{ $question->text }}</p>
                                                                    <div class="space-y-3">
                                                                        @foreach($question->options as $option)
                                                                            <label class="flex items-center p-3 bg-white/80 rounded-xl hover:bg-gray-50/50 transition-colors duration-150 cursor-pointer group">
                                                                                <input type="radio" 
                                                                                       name="question_{{ $question->id }}" 
                                                                                       value="{{ $option->id }}"
                                                                                       class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                                                <span class="ml-3 text-gray-900 group-hover:text-orange-600 transition-colors duration-150">
                                                                                    {{ $option->text }}
                                                                                </span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @break
                                            @endswitch
                                        </div>
                                    @empty
                                        <div class="text-center py-12">
                                            <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mx-auto mb-4">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">No Content Yet</h3>
                                            <p class="mt-2 text-sm text-gray-500">This section doesn't have any content yet. Check back later!</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Section Navigation -->
                                <div class="p-6 sm:p-8 border-t border-gray-100">
                                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                        <div class="w-full sm:w-auto order-2 sm:order-1">
                                            @if($previousSection)
                                                <a href="{{ route($isEnrolled ? 'courses.show.section' : 'courses.preview.section', ['course' => $course, 'section' => $previousSection]) }}" 
                                                   class="group w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 py-3 rounded-xl bg-white/80 hover:bg-orange-50/50 transition-all duration-150">
                                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                    Previous Section
                                                </a>
                                            @endif
                                        </div>

                                        @if($isEnrolled)
                                            <div class="w-full sm:w-auto order-1 sm:order-2">
                                                <form id="completeSectionForm" action="{{ route('courses.complete.section', ['course' => $course, 'section' => $currentSection]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 py-3 rounded-xl bg-orange-600 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 transition-all duration-150">
                                                        @if(in_array($currentSection->id, $completedSections))
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Completed
                                                        @else
                                                            Complete Section
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        <div class="w-full sm:w-auto order-3">
                                            @if($nextSection)
                                                <a href="{{ route($isEnrolled ? 'courses.show.section' : 'courses.preview.section', ['course' => $course, 'section' => $nextSection]) }}"
                                                   class="group w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 py-3 rounded-xl bg-white/80 hover:bg-orange-50/50 transition-all duration-150">
                                                    Next Section
                                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
    // No special YouTube handling needed - using direct iframes
</script>
@endpush

@endsection