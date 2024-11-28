@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Assessments for {{ $course->title }}</h1>
        <a href="{{ route('admin.courses.assessments.create', $course) }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Assessment
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($assessments->isEmpty())
        <div class="bg-gray-100 p-6 rounded text-center">
            <p class="text-gray-600">No assessments have been created for this course yet.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Limit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assessments as $assessment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $assessment->title }}</div>
                                @if($assessment->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($assessment->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $assessment->questions->count() }} questions</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $assessment->time_limit ? $assessment->time_limit . ' minutes' : 'No limit' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $assessment->passing_score }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.courses.assessments.questions.index', [$course, $assessment]) }}"
                                       class="text-orange-600 hover:text-orange-900">
                                        Questions
                                    </a>
                                    <a href="{{ route('admin.courses.assessments.edit', [$course, $assessment]) }}"
                                       class="text-gray-600 hover:text-gray-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.courses.assessments.destroy', [$course, $assessment]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this assessment?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
