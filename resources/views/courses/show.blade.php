@extends('layouts.app')

@section('content')
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
                        <div class="space-y-2">
                            @foreach($sections as $index => $section)
                                <div class="relative">
                                    @if($index > 0)
                                        <div class="absolute left-4 -top-2 w-0.5 h-4 bg-gray-200"></div>
                                    @endif
                                    <a href="{{ route('courses.show.section', ['course' => $course, 'section' => $section]) }}"
                                       class="relative flex items-center p-4 hover:bg-gray-50 rounded-xl transition-colors
                                              {{ $currentSection && $section->id === $currentSection->id ? 'bg-orange-50 border border-orange-200' : '' }}">
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
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $section->title }}
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($currentSection)
                    <div class="col-span-12 lg:col-span-8">
                        <div class="bg-white shadow-sm rounded-2xl p-6">
                            <div class="prose max-w-none">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $currentSection->title }}</h2>
                                {!! $currentSection->content !!}
                            </div>

                            <!-- Section Navigation -->
                            <div class="mt-8 pt-8 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4">
                                        @if($previousSection)
                                            <a href="{{ route('courses.show.section', ['course' => $course, 'section' => $previousSection]) }}"
                                               class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                                Previous
                                            </a>
                                        @endif

                                        @if(!in_array($currentSection->id, $completedSections))
                                            <form id="completeSectionForm" 
                                                  action="{{ route('courses.section.complete', ['course' => $course, 'section' => $currentSection]) }}"
                                                  method="POST"
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-700 transition-colors duration-200">
                                                    Complete Section
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <div class="inline-flex items-center gap-x-2 rounded-xl bg-green-100 px-5 py-3 text-sm font-semibold text-green-700">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Section Completed
                                            </div>
                                        @endif

                                        @if($progress === 100 && !$isCompleted)
                                            <form id="completeCourseForm" 
                                                  action="{{ route('courses.complete', ['course' => $course]) }}"
                                                  method="POST"
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700 transition-colors duration-200">
                                                    Complete Course
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if($nextSection)
                                            <a href="{{ route('courses.show.section', ['course' => $course, 'section' => $nextSection]) }}"
                                               class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                Next
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle section completion
        const completeSectionForm = document.getElementById('completeSectionForm');
        if (completeSectionForm) {
            completeSectionForm.addEventListener('submit', async function(e) {
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
                        // Replace complete button with completed status
                        const completedStatus = document.createElement('div');
                        completedStatus.className = 'inline-flex items-center gap-x-2 rounded-xl bg-green-100 px-5 py-3 text-sm font-semibold text-green-700';
                        completedStatus.innerHTML = `
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Section Completed
                        `;
                        this.replaceWith(completedStatus);

                        // Update progress bar if it exists
                        const progressBar = document.querySelector('.bg-orange-600');
                        if (progressBar) {
                            progressBar.style.width = data.progress + '%';
                        }

                        // Update progress text if it exists
                        const progressText = document.querySelector('.text-sm.font-medium.text-gray-600');
                        if (progressText) {
                            progressText.textContent = data.progress + '%';
                        }

                        // Show complete course button if all sections are done
                        if (data.progress === 100 && !data.completed) {
                            // Find the button container
                            const buttonContainer = completedStatus.closest('.flex.items-center.space-x-4');
                            if (buttonContainer && !document.getElementById('completeCourseForm')) {
                                const completeButton = document.createElement('form');
                                completeButton.id = 'completeCourseForm';
                                completeButton.action = '{{ route('courses.complete', ['course' => $course]) }}';
                                completeButton.method = 'POST';
                                completeButton.className = 'inline-block ml-4';
                                completeButton.innerHTML = `
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-x-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700 transition-colors duration-200">
                                        Complete Course
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                `;
                                buttonContainer.appendChild(completeButton);

                                // Add event listener to the new complete course form
                                setupCompleteCourseHandler(completeButton);
                            }
                        }

                        // Reload page if there's a next section
                        if (data.nextSection) {
                            window.location.href = data.nextSection;
                        }
                    } else {
                        throw new Error(data.error || 'Failed to complete section');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while completing the section');
                }
            });
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
                        // Show success message and redirect to dashboard
                        alert(data.message);
                        window.location.href = '{{ route('dashboard') }}';
                    } else {
                        throw new Error(data.error || 'Failed to complete course');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while completing the course');
                }
            });
        }

        // Set up initial complete course form if it exists
        const initialCompleteCourseForm = document.getElementById('completeCourseForm');
        if (initialCompleteCourseForm) {
            setupCompleteCourseHandler(initialCompleteCourseForm);
        }
    });
</script>
@endpush

@endsection
