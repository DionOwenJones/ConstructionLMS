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
        <div class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg z-50" 
             id="success-notification"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Success!</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Course Header -->
            <div class="bg-white shadow-sm rounded-2xl mb-6">
                <div class="p-6">
                    <!-- Back and Title -->
                    <div class="flex items-center justify-between py-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </a>
                            <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $course->title }}</h1>
                        </div>

                        <!-- Progress Bar -->
                        <div class="flex items-center space-x-4">
                            <div class="w-48 bg-gray-200 rounded-full h-2.5">
                                <div class="bg-orange-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-600">{{ number_format($progress) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($noSections ?? false)
                <!-- No Sections Message -->
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No content yet</h3>
                        <p class="mt-1 text-sm text-gray-500">This course doesn't have any sections yet. Check back later!</p>
                    </div>
                </div>
            @else
                <!-- Course Content -->
                <div class="grid grid-cols-12 gap-6">
                    <!-- Sections Sidebar -->
                    <div class="col-span-12 lg:col-span-4">
                        <div class="bg-white shadow-sm rounded-2xl p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Content</h2>
                            
                            @if(!$isEnrolled)
                                <div class="mb-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                                    <p class="text-sm text-orange-800">
                                        <span class="font-semibold">Preview Mode:</span> First 2 modules are free. 
                                        Enroll to access all content.
                                    </p>
                                </div>
                            @endif

                            <div class="space-y-2">
                                @foreach($sections as $index => $section)
                                    <div class="relative">
                                        @if($index > 0)
                                            <div class="absolute left-4 -top-2 w-0.5 h-4 bg-gray-200"></div>
                                        @endif
                                        <div class="relative flex items-center p-4 {{ $currentSection && $section->id === $currentSection->id ? 'bg-orange-50 border border-orange-200' : '' }} 
                                                    {{ $index > 1 && !$isEnrolled ? 'opacity-50' : '' }} rounded-xl">
                                            <div class="flex-shrink-0 mr-4">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                            {{ in_array($section->id, $completedSections) 
                                                                ? 'bg-green-100 text-green-600' 
                                                                : 'bg-gray-100 text-gray-500' }}">
                                                    @if(in_array($section->id, $completedSections))
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($index <= 1 || $isEnrolled)
                                                    <a href="{{ $isEnrolled 
                                                        ? route('courses.show.section', ['course' => $course, 'section' => $section])
                                                        : route('courses.preview.section', ['course' => $course, 'section' => $section]) }}"
                                                       class="block">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $section->title }}
                                                        </p>
                                                    </a>
                                                @else
                                                    <p class="text-sm font-medium text-gray-500 truncate flex items-center">
                                                        {{ $section->title }}
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(!$isEnrolled)
                                <div class="mt-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Ready to Learn More?</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Enroll now to access all modules and start your learning journey!
                                    </p>
                                    <form action="{{ route('courses.purchase', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            Enroll Now
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($currentSection)
                        <div class="col-span-12 lg:col-span-8">
                            <div class="bg-white shadow-sm rounded-2xl p-6">
                                <div class="mb-8">
                                    <h2 class="text-2xl font-bold mb-4">{{ $currentSection->title }}</h2>
                                    
                                    <!-- Section Content -->
                                    @if($currentSection)
                                        <div class="space-y-8">
                                            <!-- Debug Information (only visible to admins) -->
                                            @if(auth()->check() && auth()->user()->hasRole('admin'))
                                                <div class="bg-gray-100 p-4 rounded-lg mb-8">
                                                    <h3 class="font-semibold mb-2">Debug Info:</h3>
                                                    <pre class="text-sm overflow-auto">
Section ID: {{ $currentSection->id }}
Content Blocks Count: {{ $currentSection->contentBlocks->count() }}
@foreach($currentSection->contentBlocks as $block)
Block {{ $loop->iteration }}:
- Type: {{ $block->type }}
- Content: {{ json_encode($block->content, JSON_PRETTY_PRINT) }}
@endforeach
                                                    </pre>
                                                </div>
                                            @endif

                                            <!-- Content Blocks -->
                                            @forelse($currentSection->contentBlocks as $block)
                                                <div class="content-block mb-8">
                                                    @switch($block->type)
                                                        @case('text')
                                                            <div class="prose max-w-none">
                                                                {!! $block->text_content !!}
                                                            </div>
                                                            @break

                                                        @case('video')
                                                            @if($block->video_url)
                                                                @php
                                                                    $videoData = $block->getFormattedContentAttribute();
                                                                @endphp
                                                                @if($videoData['id'])
                                                                    <div class="aspect-w-16 aspect-h-9">
                                                                        <iframe 
                                                                            src="https://www.youtube.com/embed/{{ $videoData['id'] }}" 
                                                                            frameborder="0" 
                                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                                            allowfullscreen
                                                                            class="rounded-xl"
                                                                        ></iframe>
                                                                    </div>
                                                                    @if($block->video_title)
                                                                        <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $block->video_title }}</h3>
                                                                    @endif
                                                                @else
                                                                    <div class="bg-red-50 p-4 rounded-lg">
                                                                        <p class="text-red-600">Invalid YouTube URL format</p>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                            @break

                                                        @case('image')
                                                            @if($block->image_path)
                                                                <img src="{{ Storage::url($block->image_path) }}" 
                                                                     alt="Section image" 
                                                                     class="rounded-xl max-w-full h-auto">
                                                            @endif
                                                            @break

                                                        @case('quiz')
                                                            @if($block->quiz_data)
                                                                <div class="quiz-block bg-white rounded-lg shadow-sm p-6 space-y-6">
                                                                    <h3 class="text-xl font-semibold mb-4">{{ $block->quiz_data['title'] ?? 'Quiz' }}</h3>
                                                                    @foreach($block->quiz_data['questions'] ?? [] as $index => $question)
                                                                        <div class="quiz-question mb-6">
                                                                            <p class="text-lg font-medium mb-3">{{ $question['question'] }}</p>
                                                                            <div class="space-y-2">
                                                                                @foreach($question['options'] ?? [] as $optionIndex => $option)
                                                                                    <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                                                                        <input type="radio" 
                                                                                               name="question_{{ $index }}" 
                                                                                               value="{{ $optionIndex }}"
                                                                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                                                        <span class="text-gray-700">{{ $option }}</span>
                                                                                    </label>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            @break
                                                    @endswitch
                                                </div>
                                            @empty
                                                <div class="text-gray-500 text-center py-8">
                                                    <p>No content available for this section.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>

                                <!-- Section Navigation -->
                                <div class="mt-8 pt-8 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-4">
                                            @if($previousSection)
                                                <a href="{{ route($isEnrolled ? 'courses.section' : 'courses.preview.section', ['course' => $course, 'section' => $previousSection]) }}" 
                                                   class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                    Previous
                                                </a>
                                            @endif

                                            @if($isEnrolled)
                                                <form action="{{ route('courses.complete.section', ['course' => $course, 'section' => $currentSection]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-x-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                                            {{ in_array($currentSection->id, $completedSections) ? 'disabled' : '' }}>
                                                        @if(in_array($currentSection->id, $completedSections))
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Completed
                                                        @else
                                                            Complete Section
                                                        @endif
                                                    </button>
                                                </form>

                                                @if($progress >= 100)
                                                    <form action="{{ route('courses.complete', ['course' => $course]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="inline-flex items-center gap-x-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Complete Course
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>

                                        <div>
                                            @if($nextSection)
                                                @if(!$isEnrolled && $currentIndex >= 1)
                                                    <span class="inline-flex items-center gap-x-2 rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-500">
                                                        Next Section (Requires Enrollment)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    </span>
                                                @else
                                                    <a href="{{ route($isEnrolled ? 'courses.section' : 'courses.preview.section', ['course' => $course, 'section' => $nextSection]) }}" 
                                                       class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                        Next
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </a>
                                                @endif
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle section completion
        const completeSectionForm = document.getElementById('completeSectionForm');
        if (completeSectionForm) {
            completeSectionForm.addEventListener('submit', handleSectionCompletion);
        }

        // Handle course completion
        const completeCourseForm = document.getElementById('completeCourseForm');
        if (completeCourseForm) {
            setupCompleteCourseHandler(completeCourseForm);
        }

        async function handleSectionCompletion(e) {
            e.preventDefault();
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to complete section');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Function to set up complete course form handler
        function setupCompleteCourseHandler(form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        throw new Error(data.message || 'Failed to complete course');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        }
    });
</script>
@endpush

@endsection
