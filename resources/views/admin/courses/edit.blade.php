@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form id="courseForm" action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Course Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Course Information</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (£)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">£</span>
                            </div>
                            <input type="number" name="price" id="price" required min="0" step="0.01"
                                   value="{{ $course->price }}"
                                   class="pl-7 block w-full rounded-md border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                        <div class="mt-1 flex items-center">
                            @if($course->image)
                                <div class="mr-4">
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-32 w-auto rounded-lg">
                                </div>
                            @endif
                            <div class="w-full">
                                <input type="file" name="image" accept="image/*"
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
                                <input type="checkbox" name="has_expiry" id="has_expiry" {{ $course->validity_months ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="has_expiry" class="font-medium text-gray-700">Enable Certificate Expiration</label>
                                <p class="text-gray-500 text-sm">Set an expiration period for course certificates</p>
                            </div>
                        </div>
                        
                        <div id="expiry-settings" class="mt-4 {{ $course->validity_months ? '' : 'hidden' }}">
                            <div class="flex items-center space-x-2">
                                <input type="number" name="validity_months" min="1" value="{{ old('validity_months', $course->validity_months ?? 12) }}"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">months</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('admin.courses.index') }}"
                   class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Update Course
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle certificate expiry checkbox
    const hasExpiryCheckbox = document.getElementById('has_expiry');
    const expirySettings = document.getElementById('expiry-settings');

    hasExpiryCheckbox.addEventListener('change', function() {
        expirySettings.classList.toggle('hidden', !this.checked);
    });

    // Handle image preview
    const imageInput = document.querySelector('input[name="image"]');
    const previewContainer = document.getElementById('thumbnail-preview');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.querySelector('img').src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@endsection
