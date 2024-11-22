@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Search Results for "{{ $query }}"
            </h2>
        </div>
    </div>

    <!-- Courses Section -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Courses ({{ $courses->count() }})</h3>
            <div class="mt-4 divide-y divide-gray-200">
                @forelse($courses as $course)
                    <div class="py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-medium text-gray-900">{{ $course->title }}</h4>
                                <p class="mt-1 text-sm text-gray-500">{{ Str::limit($course->description, 100) }}</p>
                            </div>
                            <a href="{{ route('admin.courses.edit', $course) }}" 
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-purple-600 rounded-md shadow-sm hover:bg-purple-700">
                                Edit Course
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-sm text-gray-500">No courses found matching your search.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Users Section -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Users ({{ $users->count() }})</h3>
            <div class="mt-4 divide-y divide-gray-200">
                @forelse($users as $user)
                    <div class="py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-medium text-gray-900">{{ $user->name }}</h4>
                                <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-purple-600 rounded-md shadow-sm hover:bg-purple-700">
                                Edit User
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-sm text-gray-500">No users found matching your search.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
