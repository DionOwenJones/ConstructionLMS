@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-900">My Enrolled Courses</h1>
        </div>

        <div class="p-6">
            @auth
                @php
                    $enrolledCourses = Auth::user()->courses;
                @endphp

                @if($enrolledCourses && $enrolledCourses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($enrolledCourses as $course)
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold">{{ $course->title }}</h3>
                                    <p class="text-gray-600 mt-2">{{ Str::limit($course->description, 100) }}</p>
                                    <div class="mt-4">
                                        <a href="{{ route('courses.view', $course) }}"
                                           class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                            Continue Learning
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-600 mb-4">You haven't enrolled in any courses yet.</p>
                        <a href="{{ route('courses.index') }}"
                           class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Browse Courses
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 mb-4">Please log in to view your enrolled courses.</p>
                    <a href="{{ route('login') }}"
                       class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Login
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection
