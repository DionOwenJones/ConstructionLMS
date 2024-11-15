@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- Course Header -->
    <div class="sticky top-0 z-50 bg-white border-b shadow-sm backdrop-blur-lg bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">{{ $course->title }}</h1>
                </div>

                <!-- Progress Bar -->
                @if($sections->count() > 0)
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:block">
                            <div class="flex items-center gap-3">
                                <div class="w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-500 rounded-full" 
                                         style="width: {{ ($progress->completed_sections_count / $sections->count()) * 100 }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-600">
                                    {{ $progress->completed_sections_count }}/{{ $sections->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-orange-100 rounded-full px-4 py-1.5">
                            <span class="text-sm font-medium text-orange-800">
                                {{ number_format(($progress->completed_sections_count / $sections->count()) * 100) }}% Complete
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-12 gap-8">
            <!-- Section Navigation -->
            <div class="col-span-12 lg:col-span-3 space-y-6">
                <!-- Course Progress Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-600 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="mb-2 text-2xl font-bold text-gray-900">
                            {{ number_format(($progress->completed_sections_count / $sections->count()) * 100) }}%
                        </div>
                        <div class="text-sm text-gray-600">Course Progress</div>
                    </div>
                </div>

                <!-- Sections List -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Sections</h3>
                    <nav class="space-y-3">
                        @foreach($sections as $section)
                            <button
                                onclick="loadSection({{ $section->id }})"
                                class="w-full flex items-center p-3 rounded-xl transition-all duration-200
                                    {{ $section->id == $progress->current_section_id ? 'bg-orange-50 border-orange-200' : 'hover:bg-gray-50' }}
                                    {{ in_array($section->id, $progress->completed_sections ?? []) ? 'border-green-200' : 'border-gray-200' }}
                                    border"
                            >
                                <div class="flex-shrink-0 mr-3">
                                    @if(in_array($section->id, $progress->completed_sections ?? []))
                                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 border-2 {{ $section->id == $progress->current_section_id ? 'border-orange-500' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                            @if($section->id == $progress->current_section_id)
                                                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium {{ $section->id == $progress->current_section_id ? 'text-orange-600' : 'text-gray-900' }}">
                                    {{ $section->title }}
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-span-12 lg:col-span-9">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @foreach($sections as $section)
                        <div id="section-{{ $section->id }}"
                             class="section-content {{ $section->id == $progress->current_section_id ? '' : 'hidden' }}">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $section->title }}</h2>
                            </div>
                            <div class="p-6">
                                @php
                                    $content = json_decode($section->content, true);
                                @endphp

                                @if($content && isset($content['type']))
                                    @if($content['type'] === 'video')
                                        <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden mb-6">
                                            <iframe src="{{ $content['video_url'] ?? '' }}"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen
                                                    class="w-full h-full"></iframe>
                                        </div>
                                    @elseif($content['type'] === 'text')
                                        <div class="prose max-w-none">
                                            {!! $content['text'] ?? '' !!}
                                        </div>
                                    @elseif($content['type'] === 'image')
                                        <div class="rounded-xl overflow-hidden mb-6">
                                            <img src="{{ asset('storage/' . ($content['image_path'] ?? '')) }}"
                                                 alt="Section image"
                                                 class="w-full h-auto">
                                        </div>
                                    @elseif($content['type'] === 'quiz')
                                        <div class="space-y-6">
                                            @foreach($content['questions'] ?? [] as $index => $question)
                                                <div class="bg-gray-50 rounded-xl p-6">
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $question }}</h3>
                                                    <div class="space-y-3">
                                                        @foreach($content['answers'][$index] ?? [] as $answerIndex => $answer)
                                                            <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 cursor-pointer">
                                                                <input type="radio" 
                                                                       name="question_{{ $index }}" 
                                                                       value="{{ $answerIndex }}"
                                                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                                                <span class="text-gray-700">{{ $answer }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="text-gray-500 italic">No content available for this section.</div>
                                @endif
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="p-6 bg-gray-50 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4">
                                        @if(!$loop->first)
                                            <form action="{{ route('courses.previous-section', ['id' => $course->id, 'sectionId' => $section->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                    Previous Section
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        @if(!in_array($section->id, $progress->completed_sections ?? []))
                                            <form action="{{ route('courses.complete-section', ['id' => $course->id, 'sectionId' => $section->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    Mark as Complete
                                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if($progress->completed_sections_count === $sections->count())
                                            <form action="{{ route('courses.complete', ['id' => $course->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Complete Course
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        @if(!$loop->last)
                                            <form action="{{ route('courses.next-section', ['id' => $course->id, 'sectionId' => $section->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    Next Section
                                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.add('hidden');
    });

    // Show selected section
    document.getElementById(`section-${sectionId}`).classList.remove('hidden');

    // Update progress via AJAX
    fetch(`/api/sections/${sectionId}/mark-current`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    // Update active section styling
    document.querySelectorAll('[onclick^="loadSection"]').forEach(button => {
        button.classList.remove('bg-orange-50', 'border-orange-200');
        button.classList.add('hover:bg-gray-50');
    });
    
    const clickedButton = document.querySelector(`[onclick="loadSection(${sectionId})"]`);
    clickedButton.classList.add('bg-orange-50', 'border-orange-200');
    clickedButton.classList.remove('hover:bg-gray-50');
}
</script>
@endpush
@endsection
