@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6">Available Courses</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">{{ $course->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 100) }}</p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500">{{ $course->sections->count() }} sections</span>
                                <span class="text-orange-600 font-semibold">{{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}</span>
                            </div>

                            <div class="mt-4">
                                @auth
                                    @if(in_array($course->id, $purchasedCourseIds))
                                        <a href="{{ route('courses.show', $course) }}" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <span>Access Course</span>
                                        </a>
                                    @else
                                        <a href="{{ route('courses.purchase', $course) }}" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <span>Purchase Course</span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('courses.preview', $course) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                        <span>Preview Course</span>
                                    </a>
                                @endauth
                            </div>
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

                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
