@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <!-- Course Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">{{ $course->title }}</h2>
            <a href="{{ route('admin.courses.edit', $course->id) }}" 
               class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                Edit Course
            </a>
        </div>

        <!-- Course Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Course Details</h3>
                <p class="text-gray-600"><span class="font-medium">Duration:</span> {{ $course->duration }}</p>
                <p class="text-gray-600"><span class="font-medium">Level:</span> {{ $course->level }}</p>
                <p class="text-gray-600"><span class="font-medium">Category:</span> {{ $course->category }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Course Description</h3>
                <p class="text-gray-600">{{ $course->description }}</p>
            </div>
        </div>

        <!-- Course Sections -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Course Content</h3>
            @if(isset($course->sections) && count($course->sections) > 0)
                <div class="space-y-4">
                    @foreach($course->sections as $section)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                                <h4 class="font-medium text-gray-700">{{ $section->title }}</h4>
                                <span class="text-sm text-gray-500">{{ count($section->lessons) }} lessons</span>
                            </div>
                            <div class="divide-y divide-gray-200">
                                @foreach($section->lessons as $lesson)
                                    <div class="px-4 py-3 hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <span class="mr-3">
                                                @if($lesson->type === 'video')
                                                    <i class="fas fa-play-circle text-orange-500"></i>
                                                @elseif($lesson->type === 'quiz')
                                                    <i class="fas fa-question-circle text-orange-500"></i>
                                                @else
                                                    <i class="fas fa-file-alt text-orange-500"></i>
                                                @endif
                                            </span>
                                            <div>
                                                <h5 class="text-gray-700">{{ $lesson->title }}</h5>
                                                <p class="text-sm text-gray-500">{{ $lesson->duration }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No sections available for this course.</p>
                    <a href="{{ route('admin.courses.sections.create', $course->id) }}" 
                       class="inline-block mt-4 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        Add Section
                    </a>
                </div>
            @endif
        </div>

        <!-- Course Resources -->
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Course Resources</h3>
            @if(isset($course->resources) && count($course->resources) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($course->resources as $resource)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <i class="fas fa-file-download text-orange-500 mr-3"></i>
                                <div>
                                    <h5 class="text-gray-700">{{ $resource->title }}</h5>
                                    <p class="text-sm text-gray-500">{{ $resource->type }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No resources available for this course.</p>
            @endif
        </div>
    </div>
</div>
@endsection
