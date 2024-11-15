<div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
    <div class="relative aspect-video">
        <img src="{{ asset('storage/' . $course->image) }}"
             alt="{{ $course->title }}"
             class="w-full h-full object-cover">
        @if($course->isNew())
            <div class="absolute top-4 right-4 px-3 py-1 text-xs sm:text-sm font-semibold text-white bg-green-500 rounded-full">
                New
            </div>
        @endif
    </div>
    <div class="p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
            <div class="flex items-center mb-2 sm:mb-0">
                <img src="{{ $course->user->profile_photo_url }}"
                     alt="{{ $course->user->name }}"
                     class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 border-white shadow-md">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ $course->user->name }}</p>
                    <p class="text-xs text-gray-500">Instructor</p>
                </div>
            </div>
        </div>
        <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-orange-500">${{ number_format($course->price, 2) }}</span>
            <a href="{{ route('courses.show', $course) }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors">
                View Course
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
