@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Available Courses</h1>
            <p class="mt-2 text-gray-600">Browse our selection of professional construction courses</p>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($courses as $course)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-200">
                    <!-- Course Image -->
                    <div class="relative aspect-video bg-gray-100">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}"
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

                        <!-- Course Status Badge -->
                        @auth
                            @if(in_array($course->id, $enrolledCourseIds))
                                <div class="absolute top-4 right-4 px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-full">
                                    Enrolled
                                </div>
                            @endif
                        @endauth
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                            @if($course->price > 0)
                                <span class="text-lg font-bold text-orange-600">${{ number_format($course->price, 2) }}</span>
                            @else
                                <span class="text-lg font-medium text-green-600">Free</span>
                            @endif
                        </div>

                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>

                        <!-- Course Stats -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>{{ $course->sections->count() }} sections</span>
                            <span>{{ $course->estimated_hours ?? '2' }} hours</span>
                        </div>

                        <!-- Action Button -->
                        @auth
                            @if(in_array($course->id, $enrolledCourseIds))
                                <a href="{{ route('courses.view', $course) }}"
                                   class="block w-full text-center px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                    Continue Learning
                                </a>
                            @else
                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                        Enroll Now
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('courses.preview', $course) }}"
                               class="block w-full text-center px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                Preview Course
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                        <p class="mt-1 text-sm text-gray-500">Check back later for new courses.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection
