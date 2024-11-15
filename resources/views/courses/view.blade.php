@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Course Header -->
    <div class="sticky top-0 z-10 bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h1>
                </div>

                <!-- Progress indicator -->
                @if($sections->count() > 0)
                    <div class="bg-orange-100 rounded-full px-4 py-1.5">
                        <span class="text-sm font-medium text-orange-800">
                            {{ number_format(($progress->completed_sections_count / $sections->count()) * 100) }}% Complete
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-12 gap-8">
            <!-- Section Navigation -->
            <div class="col-span-12 lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Sections</h3>
                    <nav class="space-y-3">
                        @foreach($sections as $section)
                            <button
                                onclick="loadSection({{ $section->id }})"
                                class="w-full flex items-center p-3 rounded-xl transition-all duration-200
                                    {{ $section->id == $progress->current_section_id ? 'bg-orange-50 border-orange-200' : 'hover:bg-gray-50' }}
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
                                        <div class="w-6 h-6 border-2 border-gray-300 rounded-full"></div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $section->title }}</span>
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-span-12 lg:col-span-9">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    @foreach($sections as $section)
                        <div id="section-{{ $section->id }}"
                             class="section-content {{ $section->id == $progress->current_section_id ? '' : 'hidden' }}">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $section->title }}</h2>
                            </div>
                            <div class="p-6">
                                @php
                                    $content = json_decode($section->content);
                                @endphp

                                @if($content && $content->type === 'video')
                                    <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden">
                                        <iframe src="{{ $content->url }}"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                @elseif($content && $content->type === 'text')
                                    <div class="prose max-w-none">
                                        {!! $content->text !!}
                                    </div>
                                @endif
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
    fetch(`/courses/sections/${sectionId}/current`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });
}
</script>
@endpush
@endsection
