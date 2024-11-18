@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">My Certificates</h1>

        @if($completedCourses->isEmpty())
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <p class="text-gray-600">You haven't completed any courses yet.</p>
                <a href="{{ route('courses.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($completedCourses as $course)
                    <div class="bg-gray-50 rounded-lg p-6 flex flex-col">
                        <h3 class="text-lg font-semibold mb-2">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Completed on {{ \Carbon\Carbon::parse($course->completed_at)->format('F j, Y') }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('certificates.download', $course->id) }}" 
                               class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Download Certificate
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
