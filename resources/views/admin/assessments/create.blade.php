@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Assessment for {{ $course->title }}</h1>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.courses.assessments.store', $course) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            
            <!-- Assessment Details -->
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                        <input type="number" name="time_limit" id="time_limit" min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="passing_score" class="block text-sm font-medium text-gray-700">Passing Score (%)</label>
                        <input type="number" name="passing_score" id="passing_score" required min="0" max="100" value="70"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700">Maximum Attempts</label>
                        <input type="number" name="max_attempts" id="max_attempts" required min="1" value="3"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="randomize_questions" id="randomize_questions" value="1"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="randomize_questions" class="ml-2 block text-sm text-gray-700">Randomize Questions</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="show_feedback" id="show_feedback" value="1" checked
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="show_feedback" class="ml-2 block text-sm text-gray-700">Show Feedback</label>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Questions</h2>
                <div id="questions-container" class="space-y-6">
                    <!-- Question template will be added here -->
                </div>

                <button type="button" onclick="addQuestion()" 
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Question
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Assessment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let questionCount = 0;

function addQuestion() {
    const container = document.getElementById('questions-container');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-block bg-gray-50 p-4 rounded-lg';
    questionDiv.dataset.questionId = questionCount;

    questionDiv.innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-md font-medium">Question ${questionCount + 1}</h3>
            <button type="button" onclick="removeQuestion(${questionCount})" 
                    class="text-red-600 hover:text-red-900">Remove</button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Question Type</label>
                <select name="questions[${questionCount}][type]" 
                        onchange="handleQuestionTypeChange(${questionCount})"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="essay">Essay</option>
                    <option value="matching">Matching</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Question Text</label>
                <textarea name="questions[${questionCount}][question_text]" required rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <div class="options-container">
                <!-- Options will be added here based on question type -->
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Feedback</label>
                <textarea name="questions[${questionCount}][feedback]" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Points</label>
                <input type="number" name="questions[${questionCount}][points]" required min="1" value="1"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    `;

    container.appendChild(questionDiv);
    handleQuestionTypeChange(questionCount);
    questionCount++;
}

function removeQuestion(id) {
    const questionDiv = document.querySelector(`[data-question-id="${id}"]`);
    if (questionDiv) {
        questionDiv.remove();
    }
}

function handleQuestionTypeChange(id) {
    const questionDiv = document.querySelector(`[data-question-id="${id}"]`);
    const type = questionDiv.querySelector('select').value;
    const optionsContainer = questionDiv.querySelector('.options-container');

    switch (type) {
        case 'multiple_choice':
            optionsContainer.innerHTML = `
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Options</label>
                    <div class="space-y-2" id="options-${id}">
                        <div class="flex items-center space-x-2">
                            <input type="text" name="questions[${id}][options][]" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="radio" name="questions[${id}][correct_answer][answer]" value="0" required>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="questions[${id}][options][]" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="radio" name="questions[${id}][correct_answer][answer]" value="1" required>
                        </div>
                    </div>
                    <button type="button" onclick="addOption(${id})"
                            class="text-sm text-indigo-600 hover:text-indigo-900">Add Option</button>
                </div>
            `;
            break;

        case 'matching':
            optionsContainer.innerHTML = `
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Matching Pairs</label>
                    <div class="space-y-2" id="pairs-${id}">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="questions[${id}][options][left][]" placeholder="Left item" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="text" name="questions[${id}][options][right][]" placeholder="Right item" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="hidden" name="questions[${id}][correct_answer][pairs][]" value="0">
                        </div>
                    </div>
                    <button type="button" onclick="addMatchingPair(${id})"
                            class="text-sm text-indigo-600 hover:text-indigo-900">Add Pair</button>
                </div>
            `;
            break;

        case 'essay':
            optionsContainer.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700">Model Answer (for reference)</label>
                    <textarea name="questions[${id}][correct_answer][answer]" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            `;
            break;
    }
}

function addOption(id) {
    const optionsContainer = document.getElementById(`options-${id}`);
    const optionCount = optionsContainer.children.length;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'flex items-center space-x-2';
    optionDiv.innerHTML = `
        <input type="text" name="questions[${id}][options][]" required
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <input type="radio" name="questions[${id}][correct_answer][answer]" value="${optionCount}" required>
    `;
    
    optionsContainer.appendChild(optionDiv);
}

function addMatchingPair(id) {
    const pairsContainer = document.getElementById(`pairs-${id}`);
    const pairCount = pairsContainer.children.length;
    
    const pairDiv = document.createElement('div');
    pairDiv.className = 'grid grid-cols-2 gap-2';
    pairDiv.innerHTML = `
        <input type="text" name="questions[${id}][options][left][]" placeholder="Left item" required
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <input type="text" name="questions[${id}][options][right][]" placeholder="Right item" required
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <input type="hidden" name="questions[${id}][correct_answer][pairs][]" value="${pairCount}">
    `;
    
    pairsContainer.appendChild(pairDiv);
}

// Add first question automatically
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});
</script>
@endpush
@endsection
