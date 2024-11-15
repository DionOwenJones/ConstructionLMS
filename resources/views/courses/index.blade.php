@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="mb-4 text-4xl font-bold text-gray-900">Available Courses</h1>
            <p class="text-lg text-gray-600">Explore our wide range of courses and start learning today</p>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $course)
                <x-course-card :course="$course" />
            @empty
                <div class="py-12 text-center col-span-full">
                    <h3 class="text-lg font-medium text-gray-900">No courses available</h3>
                    <p class="mt-2 text-gray-500">Check back later for new courses.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection
