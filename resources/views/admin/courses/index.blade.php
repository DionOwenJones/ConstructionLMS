@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Course Management</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your courses, track their status, and make updates.</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New Course
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700 flex items-center shadow-sm" 
                 x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="courses-table-body" class="bg-white divide-y divide-gray-200">
                        @include('admin.courses.partials.course-list')
                    </tbody>
                </table>
            </div>
            
            @if($courses->hasMorePages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button id="load-more" 
                            data-next-page="{{ $courses->currentPage() + 1 }}"
                            class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2 animate-spin" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Show More Courses</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', async function() {
            const spinner = loadMoreBtn.querySelector('svg');
            const btnText = loadMoreBtn.querySelector('span');
            const nextPage = loadMoreBtn.dataset.nextPage;
            
            // Show loading state
            spinner.style.display = 'block';
            btnText.textContent = 'Loading...';
            loadMoreBtn.disabled = true;

            try {
                const response = await fetch(`{{ route('admin.courses.index') }}?page=${nextPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const html = await response.text();
                const tbody = document.getElementById('courses-table-body');
                
                // Append new courses
                tbody.insertAdjacentHTML('beforeend', html);
                
                // Update button state
                loadMoreBtn.dataset.nextPage = parseInt(nextPage) + 1;
                
                // Check if we've loaded all courses
                const totalPages = {{ $courses->lastPage() }};
                if (parseInt(nextPage) >= totalPages) {
                    loadMoreBtn.parentElement.remove();
                }
            } catch (error) {
                console.error('Error:', error);
                btnText.textContent = 'Error loading courses. Try again.';
            } finally {
                // Reset loading state
                spinner.style.display = 'none';
                btnText.textContent = 'Show More Courses';
                loadMoreBtn.disabled = false;
            }
        });
    }
});
</script>
@endpush

@endsection
