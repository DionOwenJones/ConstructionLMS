@extends('layouts.app')

@section('title', 'Welcome - Construction Training Platform')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-orange-50/50 to-white">
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute w-[500px] h-[500px] -right-32 -top-32 rounded-full bg-gradient-to-br from-orange-500/5 to-blue-500/5"></div>
            <div class="absolute w-[300px] h-[300px] -left-20 top-20 rounded-full bg-gradient-to-br from-blue-500/5 to-purple-500/5"></div>
        </div>
        
        <div class="container relative mx-auto px-4 sm:px-6 py-16 sm:py-24 md:py-32">
            <div class="max-w-4xl mx-auto text-center">
                <span class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-sm font-medium bg-orange-500/10 text-orange-600 mb-6 sm:mb-8">
                    Professional Construction Training
                </span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 tracking-tight leading-tight mb-6 sm:mb-8">
                    Master Construction Skills
                    <span class="block mt-2 sm:mt-4 bg-gradient-to-r from-orange-600 via-orange-500 to-yellow-500 bg-clip-text text-transparent">
                        With Expert Guidance
                    </span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed mb-8 sm:mb-12 px-4 sm:px-0">
                    Transform your career with industry-leading construction training. Learn from experts and earn recognized certifications.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center max-w-xl mx-auto px-4 sm:px-0">
                    <a href="{{ route('register') }}" 
                       class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-orange-600 to-orange-500 text-white text-lg font-semibold rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-0.5 overflow-hidden">
                        <span class="relative z-10">Start Learning Free</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-yellow-500 transition-transform duration-300 translate-x-full group-hover:translate-x-0"></div>
                    </a>
                    <a href="{{ route('courses.index') }}" 
                       class="group w-full sm:w-auto px-8 py-4 bg-white text-gray-800 text-lg font-semibold rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 border border-gray-100 hover:border-orange-100">
                        Explore Courses
                        <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-2">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Why Choose Us</h2>
                <p class="text-lg text-gray-600">Experience professional construction training with industry experts and earn recognized certifications.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                <div class="group p-6 sm:p-8 rounded-2xl bg-white border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="mb-6 inline-flex p-4 bg-gradient-to-br from-orange-500/10 to-orange-600/10 rounded-xl text-orange-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Learn Anywhere</h3>
                    <p class="text-gray-600 leading-relaxed">Access professional training content from any device. Perfect for busy professionals who need flexibility.</p>
                </div>
                <div class="group p-6 sm:p-8 rounded-2xl bg-white border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="mb-6 inline-flex p-4 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl text-blue-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Get Certified</h3>
                    <p class="text-gray-600 leading-relaxed">Earn industry-recognized certifications that validate your skills and boost your career prospects.</p>
                </div>
                <div class="group p-6 sm:p-8 rounded-2xl bg-white border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="mb-6 inline-flex p-4 bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-xl text-green-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Expert Training</h3>
                    <p class="text-gray-600 leading-relaxed">Learn directly from industry professionals with years of real-world construction experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Featured Courses</h2>
                <p class="text-lg text-gray-600">Start your journey with our most popular construction training courses.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-7xl mx-auto px-4 sm:px-0">
                @forelse($featuredCourses as $course)
                    <x-course-card :course="$course" :enrolledCourseIds="$enrolledCourseIds" />
                @empty
                    @foreach(range(1, 3) as $i)
                    <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                        <div class="aspect-video bg-gradient-to-br from-orange-500/5 via-blue-500/5 to-purple-500/5 relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-black/5 to-black/30"></div>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-wrap items-center gap-2 mb-6">
                                <span class="px-3 py-1.5 text-sm font-medium bg-orange-500/10 text-orange-600 rounded-full">Popular</span>
                                <span class="px-3 py-1.5 text-sm font-medium bg-blue-500/10 text-blue-600 rounded-full">Beginner</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Professional Construction Skills</h3>
                            <p class="text-gray-600 mb-6">Master essential construction techniques with our comprehensive training program.</p>
                            <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                                <span class="inline-flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    12 Hours
                                </span>
                                <a href="#" class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-500">
                                    Learn More
                                    <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-2">→</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforelse
            </div>
            <div class="mt-16 text-center">
                <a href="{{ route('courses.index') }}" 
                   class="group inline-block px-8 py-4 bg-gradient-to-r from-orange-600 to-orange-500 text-white text-lg font-semibold rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-0.5">
                    Browse All Courses
                    <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-2">→</span>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute w-[600px] h-[600px] right-1/4 -bottom-48 rounded-full bg-gradient-to-br from-orange-500/5 to-purple-500/5"></div>
            <div class="absolute w-[400px] h-[400px] left-1/4 -top-32 rounded-full bg-gradient-to-br from-blue-500/5 to-green-500/5"></div>
        </div>
        
        <div class="container relative mx-auto px-6">
            <div class="max-w-2xl mx-auto text-center">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-orange-500/10 text-orange-600 mb-8">
                    Start Your Journey Today
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Ready to Transform Your Career?</h2>
                <p class="text-xl text-gray-600 mb-10">Join our growing community of construction professionals and take your skills to the next level.</p>
                <a href="{{ route('register') }}" 
                   class="group relative inline-block px-8 py-4 bg-gradient-to-r from-orange-600 to-orange-500 text-white text-lg font-semibold rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-0.5 overflow-hidden">
                    <span class="relative z-10">Create Free Account</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-yellow-500 transition-transform duration-300 translate-x-full group-hover:translate-x-0"></div>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
