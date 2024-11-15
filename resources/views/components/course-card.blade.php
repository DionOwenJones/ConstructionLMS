<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
    <!-- Course Image -->
    <div class="relative aspect-video bg-gray-100">
        @if($course->image)
            <img src="{{ Storage::disk('public')->url($course->image) }}"
                 alt="{{ $course->title }}"
                 class="w-full h-full object-cover">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
        @endif

        <!-- Course Status Badges -->
        <div class="absolute top-4 right-4 flex space-x-2">
            @if($course->isNewCourse())
                <span class="px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-full">
                    New
                </span>
            @endif
            @if($course->isPopularCourse())
                <span class="px-3 py-1 bg-orange-500 text-white text-sm font-medium rounded-full">
                    Popular
                </span>
            @endif
        </div>
    </div>

    <div class="p-6">
        <!-- Course Header -->
        <div class="flex items-center justify-between mb-4">
            @if($course->user)
                <div class="flex items-center">
                    <img src="{{ $course->user->profile_photo_url }}"
                         alt="{{ $course->user->name }}"
                         class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $course->user->name }}</p>
                        <p class="text-xs text-gray-500">Instructor</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Info -->
        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>

        <!-- Course Stats -->
        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
            <div class="flex items-center space-x-4">
                <span>{{ $course->sections->count() }} sections</span>
                <span>{{ $course->estimated_hours ?? '2' }} hours</span>
            </div>
            <span>{{ $course->getEnrollmentCount() }} enrolled</span>
        </div>

        <!-- Price and Action -->
        <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-orange-600">${{ number_format($course->price, 2) }}</span>
            @auth
                @if(isset($enrolledCourseIds) && in_array($course->id, $enrolledCourseIds))
                    <a href="{{ route('courses.view', $course) }}"
                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition-colors">
                        Continue Learning
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition-colors">
                            Enroll Now
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </form>
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