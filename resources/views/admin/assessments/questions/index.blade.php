@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Questions for {{ $assessment->title }}</h1>
                <p class="text-gray-600">{{ $course->title }}</p>
            </div>
            <a href="{{ route('admin.courses.assessments.questions.create', [$course, $assessment]) }}"
               class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                Add Question
            </a>
        </div>

        @if($questions->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-gray-500">No questions added yet.</p>
                <a href="{{ route('admin.courses.assessments.questions.create', [$course, $assessment]) }}"
                   class="text-orange-600 hover:text-orange-700 font-medium mt-2 inline-block">
                    Add your first question
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($questions as $question)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($question->type === 'multiple_choice') bg-blue-100 text-blue-800
                                        @elseif($question->type === 'essay') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ Str::title(str_replace('_', ' ', $question->type)) }}
                                    </span>
                                    <span class="text-gray-500 text-sm">{{ $question->points }} points</span>
                                </div>
                                <p class="text-gray-800 font-medium">{{ $question->question_text }}</p>
                                
                                @if($question->type === 'multiple_choice' && !empty($question->options))
                                    <div class="mt-2 space-y-1">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center space-x-2">
                                                <span class="w-4 h-4 inline-block rounded-full
                                                    @if(in_array($option, $question->correct_answer)) bg-green-500
                                                    @else bg-gray-200
                                                    @endif">
                                                </span>
                                                <span class="text-sm text-gray-600">{{ $option }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($question->feedback)
                                    <p class="mt-2 text-sm text-gray-500">
                                        <span class="font-medium">Feedback:</span> {{ $question->feedback }}
                                    </p>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.courses.assessments.questions.edit', [$course, $assessment, $question]) }}"
                                   class="text-gray-600 hover:text-gray-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.courses.assessments.questions.destroy', [$course, $assessment, $question]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
