@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Add Question</h1>
            <p class="text-gray-600">{{ $assessment->title }} - {{ $course->title }}</p>
        </div>

        <form action="{{ route('admin.courses.assessments.questions.store', [$course, $assessment]) }}" 
              method="POST" 
              id="questionForm"
              class="bg-white rounded-lg shadow-sm p-6">
            @csrf

            <!-- Question Type -->
            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700">Question Type</label>
                <select name="type" id="type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Select a type</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="essay">Essay</option>
                    <option value="matching">Matching</option>
                </select>
            </div>

            <!-- Question Text -->
            <div class="mb-6">
                <label for="question_text" class="block text-sm font-medium text-gray-700">Question</label>
                <textarea name="question_text" id="question_text" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
            </div>

            <!-- Points -->
            <div class="mb-6">
                <label for="points" class="block text-sm font-medium text-gray-700">Points</label>
                <input type="number" name="points" id="points" required min="1" value="1"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- Multiple Choice Options -->
            <div id="multiple_choice_options" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                <div id="options_container" class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="correct_answer[]" value="0" class="rounded text-orange-600 focus:ring-orange-500">
                        <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addOption()" class="mt-2 text-sm text-orange-600 hover:text-orange-700">
                    + Add Option
                </button>
            </div>

            <!-- Matching Pairs -->
            <div id="matching_pairs" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Matching Pairs</label>
                <div id="pairs_container" class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="matching_left[]" placeholder="Left side" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <div class="flex items-center space-x-2">
                            <input type="text" name="matching_right[]" placeholder="Right side" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <button type="button" onclick="removePair(this)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addPair()" class="mt-2 text-sm text-orange-600 hover:text-orange-700">
                    + Add Pair
                </button>
            </div>

            <!-- Feedback -->
            <div class="mb-6">
                <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback</label>
                <textarea name="feedback" id="feedback" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                          placeholder="Optional feedback to show students"></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.courses.assessments.questions.index', [$course, $assessment]) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Add Question
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const multipleChoiceOptions = document.getElementById('multiple_choice_options');
    const matchingPairs = document.getElementById('matching_pairs');
    const questionForm = document.getElementById('questionForm');

    typeSelect.addEventListener('change', function() {
        multipleChoiceOptions.classList.toggle('hidden', this.value !== 'multiple_choice');
        matchingPairs.classList.toggle('hidden', this.value !== 'matching');
    });

    function addOption() {
        const container = document.getElementById('options_container');
        const optionCount = container.children.length;
        
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        div.innerHTML = `
            <input type="checkbox" name="correct_answer[]" value="${optionCount}" class="rounded text-orange-600 focus:ring-orange-500">
            <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        container.appendChild(div);
    }

    function removeOption(button) {
        button.closest('div').remove();
        updateOptionValues();
    }

    function updateOptionValues() {
        const container = document.getElementById('options_container');
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((checkbox, index) => {
            checkbox.value = index;
        });
    }

    function addPair() {
        const container = document.getElementById('pairs_container');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-2 gap-2';
        div.innerHTML = `
            <input type="text" name="matching_left[]" placeholder="Left side" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            <div class="flex items-center space-x-2">
                <input type="text" name="matching_right[]" placeholder="Right side" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                <button type="button" onclick="removePair(this)" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(div);
    }

    function removePair(button) {
        button.closest('.grid').remove();
    }

    questionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const type = typeSelect.value;
        let isValid = true;
        let correctAnswer = [];

        if (type === 'multiple_choice') {
            const options = Array.from(document.getElementsByName('options[]'))
                .map(input => input.value.trim())
                .filter(value => value !== '');

            const checked = Array.from(document.getElementsByName('correct_answer[]'))
                .map((checkbox, index) => checkbox.checked ? options[index] : null)
                .filter(value => value !== null);

            if (options.length < 2) {
                alert('Please add at least 2 options');
                isValid = false;
            } else if (checked.length === 0) {
                alert('Please select at least one correct answer');
                isValid = false;
            } else {
                correctAnswer = checked;
            }
        } else if (type === 'matching') {
            const leftSide = Array.from(document.getElementsByName('matching_left[]'))
                .map(input => input.value.trim())
                .filter(value => value !== '');
            const rightSide = Array.from(document.getElementsByName('matching_right[]'))
                .map(input => input.value.trim())
                .filter(value => value !== '');

            if (leftSide.length < 2 || rightSide.length < 2) {
                alert('Please add at least 2 matching pairs');
                isValid = false;
            } else {
                correctAnswer = leftSide.map((left, index) => ({
                    left: left,
                    right: rightSide[index]
                }));
            }
        } else if (type === 'essay') {
            correctAnswer = ['essay'];
        }

        if (isValid) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'correct_answer';
            input.value = JSON.stringify(correctAnswer);
            this.appendChild(input);

            if (type === 'multiple_choice') {
                const optionsInput = document.createElement('input');
                optionsInput.type = 'hidden';
                optionsInput.name = 'options';
                optionsInput.value = JSON.stringify(Array.from(document.getElementsByName('options[]'))
                    .map(input => input.value.trim())
                    .filter(value => value !== ''));
                this.appendChild(optionsInput);
            } else if (type === 'matching') {
                const optionsInput = document.createElement('input');
                optionsInput.type = 'hidden';
                optionsInput.name = 'options';
                optionsInput.value = JSON.stringify({
                    left: Array.from(document.getElementsByName('matching_left[]'))
                        .map(input => input.value.trim())
                        .filter(value => value !== ''),
                    right: Array.from(document.getElementsByName('matching_right[]'))
                        .map(input => input.value.trim())
                        .filter(value => value !== '')
                });
                this.appendChild(optionsInput);
            }

            this.submit();
        }
    });
</script>
@endpush

@endsection
