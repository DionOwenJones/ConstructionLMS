@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Left Sidebar Navigation -->
    <div class="w-64 bg-white shadow-lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Edit Course</h2>
            <p class="text-sm text-gray-600">Update course information</p>
        </div>
        <nav class="px-4 pb-4">
            <a href="#basic-info" class="flex items-center px-4 py-3 text-sm font-medium text-orange-600 rounded-lg bg-orange-50">
                <span class="flex items-center justify-center w-6 h-6 mr-3 text-sm text-white bg-orange-600 rounded-full">1</span>
                Basic Info
            </a>
            <a href="#content" class="flex items-center px-4 py-3 mt-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                <span class="flex items-center justify-center w-6 h-6 mr-3 text-sm text-gray-400 bg-gray-100 rounded-full">2</span>
                Content
            </a>
            <a href="#pricing" class="flex items-center px-4 py-3 mt-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                <span class="flex items-center justify-center w-6 h-6 mr-3 text-sm text-gray-400 bg-gray-100 rounded-full">3</span>
                Pricing & Status
            </a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 px-8 py-6">
        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Info Section -->
            <div id="basic-info" class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Title</label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="4"
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Image</label>
                        <div class="flex items-center mt-2 space-x-4">
                            @if($course->image)
                                <div class="w-32 h-32">
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}"
                                         class="object-cover w-full h-full rounded-lg">
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div id="image-preview" class="hidden mb-4">
                                            <img src="" alt="Preview" class="object-cover h-32 mx-auto rounded-lg">
                                        </div>
                                        <div class="flex text-sm text-gray-600">
                                            <label class="relative font-medium text-orange-600 bg-white rounded-md cursor-pointer hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                                <span>Upload a new image</span>
                                                <input type="file" name="image" class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing & Status Section -->
            <div id="pricing" class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pricing & Status</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                        <input type="number" name="price" value="{{ old('price', $course->price) }}" step="0.01"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.courses.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Update Course
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.querySelector('input[name="image"]');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="object-cover h-32 mx-auto rounded-lg">
                `;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
