@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="py-12">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Course Creation Progress</span>
                    <span class="text-sm font-medium text-orange-600" id="progress-text">Step 1 of 3</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full">
                    <div class="h-2 bg-orange-500 rounded-full transition-all duration-500" style="width: 33%;" id="progress-bar"></div>
                </div>
            </div>

            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm" class="space-y-6">
                @csrf

                <!-- Add this right after the form opening tag -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 text-lg font-semibold">1</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                       placeholder="Enter an engaging title">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" rows="4" required
                                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                          placeholder="Describe what students will learn">{{ old('description') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                                <div class="mt-1 relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                                           class="pl-7 block w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                                           placeholder="29.99">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 text-lg font-semibold">2</span>
                            </div>
                            <h3 class="text-lg font-medium">Course Thumbnail</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="drop-zone" class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-orange-500 transition-colors cursor-pointer @error('image') border-red-500 @enderror">
                            <input type="file" id="course-image" name="image" accept="image/*" class="hidden" required>
                            <div id="image-preview" class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">Drag and drop an image here, or click to select</p>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Course Sections -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 text-lg font-semibold">3</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Course Content</h3>
                            </div>
                            <button type="button" id="addSection"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Section
                            </button>
                        </div>
                    </div>
                    <div id="sections-container" class="space-y-6">
                        <!-- Sections will be dynamically added here -->
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/course-thumbnail.js') }}"></script>
<script src="{{ asset('js/course-sections.js') }}"></script>
@endpush

@push('styles')
<link href="{{ asset('css/course-sections.css') }}" rel="stylesheet">
@endpush
@endsection
