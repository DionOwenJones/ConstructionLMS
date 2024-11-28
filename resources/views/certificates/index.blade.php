@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="md:flex md:items-center md:justify-between mb-12">
            <div class="flex-1 min-w-0">
                <h2 class="text-4xl font-extrabold leading-tight text-gray-900 sm:text-5xl sm:tracking-tight">
                    My Certificates
                </h2>
                <p class="mt-3 text-xl text-gray-500">
                    View and download your course completion certificates
                </p>
            </div>
        </div>

        @if($completedCourses->isEmpty())
            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-xl p-12 text-center transform transition-all duration-300 hover:scale-[1.01]">
                <div class="max-w-md mx-auto">
                    <div class="rounded-full bg-orange-100 p-3 w-16 h-16 mx-auto">
                        <svg class="w-10 h-10 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-2xl font-bold text-gray-900">No certificates yet</h3>
                    <p class="mt-3 text-lg text-gray-500">Get started by completing a course to earn your first certificate.</p>
                    <div class="mt-8">
                        <a href="{{ route('courses.index') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transform transition-all duration-300 hover:scale-105">
                            Browse Available Courses
                            <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($completedCourses as $course)
                    <div class="group bg-white shadow-md ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-start space-x-4 mb-6">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-14 w-14 rounded-xl bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-orange-600 transition-colors duration-300">
                                        {{ $course->title }}
                                    </h3>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-6">
                                <div class="flex items-center text-sm text-gray-500 mb-6">
                                    <svg class="flex-shrink-0 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Completed {{ \Carbon\Carbon::parse($course->completed_at)->format('F j, Y') }}
                                </div>
                                <a href="{{ route('certificates.download', $course->id) }}" 
                                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 w-full justify-center transform transition-all duration-300 hover:scale-[1.02] shadow-sm hover:shadow">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
