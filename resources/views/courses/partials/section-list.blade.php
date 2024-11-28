@if(!$isEnrolled)
    <div class="mb-6 p-4 bg-orange-50 border border-orange-100 rounded-xl">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm text-orange-800">
                <span class="font-medium">Preview Mode:</span> First 2 modules are free. 
                Enroll to access all content.
            </p>
        </div>
    </div>
@endif

<div class="space-y-3">
    @foreach($sections as $index => $section)
        <div class="relative">
            @if($index > 0)
                <div class="absolute left-4 -top-2 w-0.5 h-4 bg-gray-100"></div>
            @endif
            <div class="relative flex items-center p-4 transition-all duration-150
                        {{ $currentSection && $section->id === $currentSection->id ? 'bg-orange-50 border border-orange-100 shadow-sm' : 'hover:bg-gray-50' }} 
                        {{ $index > 1 && !$isEnrolled ? 'opacity-60' : '' }} rounded-xl">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors duration-200
                                {{ in_array($section->id, $completedSections) 
                                    ? 'bg-green-100 text-green-600' 
                                    : ($currentSection && $section->id === $currentSection->id 
                                        ? 'bg-orange-100 text-orange-600'
                                        : 'bg-gray-100 text-gray-500') }}">
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
                           class="block group">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-orange-600 transition-colors duration-150 truncate">
                                {{ $section->title }}
                            </p>
                        </a>
                    @else
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 truncate">
                                {{ $section->title }}
                            </p>
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

@if(!$isEnrolled)
    <div class="mt-8 p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
        <div class="space-y-4">
            <div class="flex items-center justify-center w-12 h-12 bg-white rounded-xl shadow-sm mx-auto">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900">Ready to Learn More?</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Enroll now to access all modules and start your learning journey!
                </p>
            </div>
            <form action="{{ route('courses.purchase', $course) }}" method="POST">
                @csrf
                <button type="submit" 
                   class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Enroll Now
                </button>
            </form>
        </div>
    </div>
@endif
