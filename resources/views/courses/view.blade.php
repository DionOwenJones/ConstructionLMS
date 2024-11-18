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
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $course->title }}</h1>
                </div>

                <!-- Progress Bar -->
                @if($sections->count() > 0)
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:block">
                            <div class="flex items-center gap-3">
                                <div class="w-24 sm:w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-500 rounded-full transition-all duration-300" 
                                         style="width: {{ ($progress->completed_sections_count / $sections->count()) * 100 }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-600">
                                    {{ $progress->completed_sections_count }}/{{ $sections->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-orange-100 rounded-full px-3 sm:px-4 py-1.5">
                            <span class="text-xs sm:text-sm font-medium text-orange-800">
                                {{ number_format(($progress->completed_sections_count / $sections->count()) * 100) }}%
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Section Navigation - Shown as a bottom sheet on mobile -->
            <div x-data="{ showSections: false }" class="lg:col-span-3">
                <!-- Mobile Toggle Button -->
                <div class="fixed bottom-4 right-4 lg:hidden z-50">
                    <button @click="showSections = !showSections" 
                            class="bg-orange-600 text-white rounded-full p-4 shadow-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Section List -->
                <div x-show="showSections" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40 lg:hidden">
                    <div class="absolute inset-x-0 bottom-0 max-h-[80vh] bg-white rounded-t-2xl shadow-xl overflow-y-auto">
                        <div class="sticky top-0 bg-white px-4 py-3 border-b">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Course Sections</h3>
                                <button @click="showSections = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="px-4 py-3">
                            @include('courses._sections_list', ['sections' => $sections, 'progress' => $progress])
                        </div>
                    </div>
                </div>

                <!-- Desktop Section List -->
                <div class="hidden lg:block space-y-6">
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

                    <!-- Desktop Sections List -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Sections</h3>
                        @include('courses._sections_list', ['sections' => $sections, 'progress' => $progress])
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-9">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @php
                        $activeSection = $sections->firstWhere('id', $progress->current_section_id);
                        if (!$activeSection) {
                            $activeSection = $sections->first();
                        }
                        $isFirst = $activeSection->id === $sections->first()->id;
                        $isLast = $activeSection->id === $sections->last()->id;
                    @endphp

                    @if($activeSection)
                        <div id="section-{{ $activeSection->id }}" class="section-content">
                            <div class="p-4 sm:p-6 border-b border-gray-200">
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $activeSection->title }}</h2>
                            </div>
                            <div class="p-4 sm:p-6">
                                {!! $activeSection->content !!}
                            </div>

                            <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                    <div class="flex items-center gap-4">
                                        @if(!$isFirst)
                                            <form action="{{ route('courses.previous-section', ['id' => $course->id, 'sectionId' => $activeSection->id]) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                    Previous
                                                </button>
                                            </form>
                                        @endif

                                        @if(!in_array($activeSection->id, $progress->completed_sections ?? []))
                                            <form action="{{ route('courses.complete-section', ['id' => $course->id, 'sectionId' => $activeSection->id]) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 active:bg-orange-700 transition-colors duration-200">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Complete Section
                                                </button>
                                            </form>
                                        @else
                                            <div class="inline-flex items-center gap-x-2 rounded-xl bg-green-50 px-5 py-3 text-sm font-medium text-green-700">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Section Completed
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-4">
                                        @if($progress->completed_sections_count === $sections->count())
                                            <form action="{{ route('courses.complete', ['id' => $course->id]) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-500 active:bg-green-700 transition-colors duration-200">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Complete Course
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$isLast)
                                            <form action="{{ route('courses.next-section', ['id' => $course->id, 'sectionId' => $activeSection->id]) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                                                    Next
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadSection(sectionId) {
    // Your existing loadSection function
}
</script>
@endpush
@endsection
