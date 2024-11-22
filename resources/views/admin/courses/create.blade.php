@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form id="courseForm" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Course Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Course Information</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                        <input type="text" name="title" id="title" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" required min="0" step="0.01"
                                   class="pl-7 block w-full rounded-md border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <input type="file" name="image" accept="image/*" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            </div>
                        </div>
                        <div id="thumbnail-preview" class="mt-2 hidden">
                            <img src="" alt="Thumbnail preview" class="h-32 w-auto rounded-lg">
                        </div>
                    </div>

                    <!-- Certificate Expiry -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="has_expiry" id="has_expiry"
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="has_expiry" class="font-medium text-gray-700">Enable Certificate Expiration</label>
                                <p class="text-gray-500 text-sm">Set an expiration period for course certificates</p>
                            </div>
                        </div>
                        
                        <div id="expiry-settings" class="mt-4 hidden">
                            <div class="flex items-center space-x-2">
                                <input type="number" name="validity_months" min="1" value="12"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">months</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Course Content</h2>
                    <p class="mt-1 text-sm text-gray-500">Organize your course content into sections</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-12 gap-6">
                        <!-- Content Blocks Library -->
                        <div class="col-span-3">
                            <div class="bg-gray-50 rounded-lg p-4 sticky top-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-4">Content Blocks</h3>
                                <div id="blocks-library" class="space-y-2">
                                    <!-- Text Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="text">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Text Block</span>
                                        </div>
                                    </div>

                                    <!-- Video Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="video">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Video Block</span>
                                        </div>
                                    </div>

                                    <!-- Image Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="image">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Image Block</span>
                                        </div>
                                    </div>

                                    <!-- Quiz Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="quiz">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Quiz Block</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Sections -->
                        <div class="col-span-9">
                            <div id="sections-container" class="space-y-6">
                                <!-- Sections will be added here -->
                            </div>

                            <button type="button" id="add-section"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Section
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Section Template -->
<template id="section-template">
    <div class="course-section bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <input type="text" name="sections[{index}][title]" placeholder="Section Title" required
                       class="text-lg font-medium text-gray-900 border-none focus:ring-0 w-full">
                <button type="button" class="remove-section text-gray-400 hover:text-red-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="section-content">
            <input type="hidden" name="sections[{index}][content]" value="">
            <div class="content-blocks min-h-[100px] p-4" data-section-index="{index}">
                <!-- Content blocks will be dropped here -->
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/course-builder.js') }}"></script>
@endpush

@endsection
