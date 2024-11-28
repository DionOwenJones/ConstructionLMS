@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Results Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Assessment Results</h1>
                <p class="text-gray-600 mt-2">{{ $assessment->title }}</p>
            </div>

            <!-- Score Summary -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Score</div>
                            <div class="mt-1 text-3xl font-semibold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($attempt->score, 1) }}%
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Required to Pass</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">
                                {{ $assessment->passing_score }}%
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Time Taken</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">
                                {{ $attempt->time_taken }} min
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <div class="inline-flex items-center px-4 py-2 rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                @if($attempt->passed)
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                @endif
                            </svg>
                            {{ $attempt->passed ? 'Assessment Passed' : 'Assessment Failed' }}
                        </div>
                    </div>
                </div>
            </div>

            @if($feedback)
            <!-- Detailed Feedback -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Question Feedback</h2>
                
                @foreach($feedback as $questionId => $questionFeedback)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    @if(isset($questionFeedback['correct']))
                        <!-- Multiple Choice Feedback -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($questionFeedback['correct'])
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                @else
                                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm">
                                    <span class="font-medium">Your Answer:</span> {{ $questionFeedback['your_answer'] }}
                                </div>
                                @if(!$questionFeedback['correct'])
                                <div class="text-sm mt-1">
                                    <span class="font-medium">Correct Answer:</span> {{ $questionFeedback['correct_answer'] }}
                                </div>
                                @endif
                                @if($questionFeedback['explanation'])
                                <div class="mt-2 text-sm text-gray-600">
                                    {{ $questionFeedback['explanation'] }}
                                </div>
                                @endif
                                <div class="mt-1 text-sm text-gray-500">
                                    Points: {{ $questionFeedback['points'] }}
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Matching Feedback -->
                        <div>
                            <div class="space-y-3">
                                @foreach($questionFeedback['pairs'] as $pair)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if($pair['correct'])
                                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        @else
                                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $pair['left'] }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-gray-600">Your Match:</span> {{ $pair['your_answer'] }}
                                        </div>
                                        @if(!$pair['correct'])
                                        <div class="text-sm">
                                            <span class="text-gray-600">Correct Match:</span> {{ $pair['correct_answer'] }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                Points: {{ $questionFeedback['points'] }} / {{ $questionFeedback['total_possible'] }}
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-between">
                <a href="{{ route('courses.show', $assessment->course) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Return to Course
                </a>
                @if(!$attempt->passed && $assessment->getRemainingAttemptsForUser(Auth::id()) > 0)
                <a href="{{ route('assessments.start', $assessment) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Retry Assessment
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
