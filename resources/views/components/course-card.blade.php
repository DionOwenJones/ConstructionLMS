@props(['course', 'purchasedCourseIds' => []])

<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
    <div class="relative">
        @if($course->image)
            <img src="{{ asset('storage/' . $course->image) }}" 
                 alt="{{ $course->title }}" 
                 class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-orange-100 flex items-center justify-center">
                <svg class="w-16 h-16 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif
    </div>

    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
        <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $course->description }}</p>

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="ml-1 text-sm text-gray-600">{{ $course->estimated_hours ?? '2' }} hours</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="ml-1 text-sm text-gray-600">{{ $course->sections->count() }} sections</span>
                </div>
            </div>
            <span class="text-lg font-bold text-orange-600">{{ $course->formatted_price }}</span>
        </div>

        <div class="flex justify-end">
            @auth
                @if(in_array($course->id, $purchasedCourseIds))
                    <a href="{{ route('courses.show', $course) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors">
                        Access Course
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('courses.purchase', $course) }}"
                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition-colors">
                        Purchase Course
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>
                @endif
            @else
                <a href="{{ route('courses.preview', $course) }}"
                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition-colors">
                    Preview Course
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</div>