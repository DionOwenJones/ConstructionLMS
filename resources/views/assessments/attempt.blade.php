@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Assessment Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $assessment->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $assessment->description }}</p>
                
                <!-- Timer -->
                <div class="mt-4 text-lg font-medium text-gray-700" id="timer">
                    Time Remaining: <span id="time-left"></span>
                </div>
                
                <!-- Progress -->
                <div class="mt-2">
                    <div class="text-sm text-gray-600">Question <span id="current-question">1</span> of {{ count($questions) }}</div>
                    <div class="mt-1 h-2 bg-gray-200 rounded-full">
                        <div id="progress-bar" class="h-2 bg-orange-500 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <form id="assessment-form" action="{{ route('assessments.submit', $assessment) }}" method="POST">
                @csrf
                <input type="hidden" name="start_time" value="{{ now() }}">
                
                <!-- Questions Container -->
                <div id="questions-container">
                    @foreach($questions as $index => $question)
                    <div class="question-slide {{ $index === 0 ? '' : 'hidden' }}" data-question-index="{{ $index }}">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Question {{ $index + 1 }}</h3>
                            <p class="mt-2 text-gray-700">{{ $question->text }}</p>
                        </div>

                        @if($question->type === 'multiple_choice')
                            <div class="mt-4 space-y-4">
                                @foreach(json_decode($question->options) as $optionIndex => $option)
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $optionIndex }}"
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'matching')
                            <div class="mt-4 space-y-4">
                                @php $pairs = json_decode($question->matching_pairs, true); @endphp
                                @foreach($pairs as $pairIndex => $pair)
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-700 w-1/3">{{ $pair['left'] }}</span>
                                    <select name="answers[{{ $question->id }}][{{ $pairIndex }}]"
                                            class="mt-1 block w-2/3 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                        <option value="">Select a match...</option>
                                        @foreach($pairs as $option)
                                        <option value="{{ $option['right'] }}">{{ $option['right'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <button type="button" 
                            id="prev-button"
                            class="hidden px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Previous
                    </button>
                    <button type="button"
                            id="next-button"
                            class="ml-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Next
                    </button>
                    <button type="submit"
                            id="submit-button"
                            class="hidden ml-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Submit Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('assessment-form');
        const questionsContainer = document.getElementById('questions-container');
        const slides = document.querySelectorAll('.question-slide');
        const nextButton = document.getElementById('next-button');
        const prevButton = document.getElementById('prev-button');
        const submitButton = document.getElementById('submit-button');
        const progressBar = document.getElementById('progress-bar');
        const currentQuestionSpan = document.getElementById('current-question');
        let currentSlide = 0;

        // Timer setup
        const timeLimit = {{ $assessment->time_limit * 60 }}; // Convert minutes to seconds
        let timeLeft = timeLimit;
        const timerDisplay = document.getElementById('time-left');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                form.submit();
            }
            timeLeft--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        function updateSlideVisibility() {
            slides.forEach((slide, index) => {
                slide.classList.toggle('hidden', index !== currentSlide);
            });

            // Update buttons
            prevButton.classList.toggle('hidden', currentSlide === 0);
            nextButton.classList.toggle('hidden', currentSlide === slides.length - 1);
            submitButton.classList.toggle('hidden', currentSlide !== slides.length - 1);

            // Update progress
            const progress = ((currentSlide + 1) / slides.length) * 100;
            progressBar.style.width = `${progress}%`;
            currentQuestionSpan.textContent = currentSlide + 1;
        }

        nextButton.addEventListener('click', () => {
            if (currentSlide < slides.length - 1) {
                currentSlide++;
                updateSlideVisibility();
            }
        });

        prevButton.addEventListener('click', () => {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlideVisibility();
            }
        });

        // Prevent form submission when pressing enter
        form.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        // Initialize visibility
        updateSlideVisibility();
    });
</script>
@endpush
@endsection
