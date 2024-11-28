@extends('layouts.app')

@section('title', 'Construction Training Courses')

@section('meta_description', 'Browse our comprehensive selection of professional construction training courses. From safety certifications to specialized skills, advance your construction career with our expert-led programs.')

@section('meta_keywords', 'construction courses, professional training, construction certification, safety training, construction skills, construction education, professional development')

@section('content')
<!-- Hero Section -->
<div class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 md:py-16">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900">
                Construction Training <span class="block sm:inline text-orange-600">Courses</span>
            </h1>
            <p class="mt-3 mx-auto text-sm sm:text-base md:text-lg text-gray-500 max-w-md sm:max-w-xl md:max-w-2xl lg:max-w-3xl px-4">
                Advance your career with our professional construction training programs. From safety certifications to specialized skills.
            </p>
            <div class="mt-6 sm:mt-8 max-w-xl sm:max-w-2xl mx-auto px-4">
                <div class="relative">
                    <input type="text" 
                           placeholder="Search courses..." 
                           class="w-full px-4 sm:px-6 py-3 sm:py-4 rounded-xl border-2 border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-gray-900 text-base sm:text-lg"
                           id="courseSearch">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="py-8 sm:py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Course Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            @forelse($courses as $course)
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden border border-gray-100 transform hover:-translate-y-1 transition-all duration-200 course-card">
                @if($course->image_url)
                    <div class="relative">
                        <img src="{{ $course->image_url }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-48 sm:h-52 md:h-56 object-cover transition-transform duration-300 hover:scale-105"
                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'; this.classList.add('error-image');">
                        <div class="absolute top-3 sm:top-4 right-3 sm:right-4">
                            <span class="px-2 sm:px-3 py-1 bg-orange-600 text-white text-xs sm:text-sm font-semibold rounded-full">
                                {{ $course->price > 0 ? '£' . number_format($course->price, 2) : 'Free' }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="w-full h-48 sm:h-52 md:h-56 bg-gray-100 flex items-center justify-center">
                        <svg class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                @endif

                <div class="p-4 sm:p-5 md:p-6">
                    <div class="flex items-center gap-2 mb-2 sm:mb-3">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-xs sm:text-sm font-medium text-gray-600">{{ $course->sections->count() }} sections</span>
                    </div>

                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 line-clamp-3">{{ $course->description }}</p>
                    
                    <div class="mt-auto">
                        @auth
                            @if(in_array($course->id, $purchasedCourseIds))
                                <a href="{{ route('courses.show', $course) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-green-600 text-white rounded-lg sm:rounded-xl hover:bg-green-700 transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm sm:text-base">Continue Learning</span>
                                </a>
                            @else
                                <a href="{{ route('courses.purchase', $course) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-orange-600 text-white rounded-lg sm:rounded-xl hover:bg-orange-700 transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-sm sm:text-base">Purchase Course</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('courses.preview', $course) }}" 
                               class="w-full inline-flex justify-center items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-orange-600 text-white rounded-lg sm:rounded-xl hover:bg-orange-700 transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="text-sm sm:text-base">Preview Course</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-8 sm:p-12 text-center">
                        <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-4 text-lg sm:text-xl font-semibold text-gray-900">No courses available</h3>
                        <p class="mt-2 text-sm sm:text-base text-gray-500">Check back later for new courses.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple course search functionality
    document.getElementById('courseSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const courseCards = document.querySelectorAll('.course-card');
        
        courseCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush

@endsection