@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Available Courses</h2>
            <a href="{{ route('business.courses.purchases') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                View Purchased Courses
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            @if($course->image)
                                <div class="relative aspect-video">
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                         alt="{{ $course->name }}"
                                         class="w-full h-48 object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($course->description, 100) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                                    <form action="{{ route('business.courses.purchase', $course) }}" method="POST">
                                        @csrf
                                        <div class="flex items-center space-x-2">
                                            <input type="number" name="seats" min="1" value="1"
                                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                                Purchase
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">No courses available at the moment.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
