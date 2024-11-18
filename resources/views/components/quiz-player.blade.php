@props(['quizData'])

<div
    x-data="quizPlayer(@js($quizData))"
    x-init="init()"
    class="w-full max-w-4xl mx-auto"
>
    <!-- Progress Bar -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs sm:text-sm font-medium text-gray-700">Question <span x-text="currentQuestion + 1"></span> of <span x-text="questions.length"></span></span>
            <span class="text-xs sm:text-sm font-medium text-gray-700" x-text="Math.round((currentQuestion + 1) / questions.length * 100) + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-orange-500 h-2 rounded-full transition-all duration-300"
                 :style="{ width: ((currentQuestion + 1) / questions.length * 100) + '%' }"></div>
        </div>
    </div>

    <!-- Question Card -->
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden">
        <template x-for="(question, index) in questions" :key="index">
            <div x-show="currentQuestion === index" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform -translate-x-full">
                
                <!-- Question Header -->
                <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900" x-text="question"></h3>
                </div>

                <!-- Answer Options -->
                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                    <template x-for="(answer, answerIndex) in answers[index]" :key="answerIndex">
                        <button @click="selectAnswer(index, answerIndex)"
                                :class="{
                                    'w-full text-left p-3 sm:p-4 rounded-lg border-2 transition-all duration-200': true,
                                    'bg-white border-gray-200 hover:border-orange-500': !isAnswerSelected(index, answerIndex) && !showFeedback,
                                    'bg-green-50 border-green-500': isCorrectAnswer(index, answerIndex),
                                    'bg-red-50 border-red-500': isIncorrectAnswer(index, answerIndex),
                                    'border-orange-500': isAnswerSelected(index, answerIndex) && !showFeedback
                                }">
                            <div class="flex items-center">
                                <div :class="{
                                    'w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 flex items-center justify-center mr-3': true,
                                    'border-gray-300': !isAnswerSelected(index, answerIndex) && !showFeedback,
                                    'border-orange-500 bg-orange-500': isAnswerSelected(index, answerIndex) && !showFeedback,
                                    'border-green-500 bg-green-500': isCorrectAnswer(index, answerIndex),
                                    'border-red-500 bg-red-500': isIncorrectAnswer(index, answerIndex)
                                }">
                                    <svg class="w-3 h-3 text-white" 
                                         :class="{ 'hidden': !isAnswerSelected(index, answerIndex) && !isCorrectAnswer(index, answerIndex) }"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" x-text="answer"></span>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- Feedback Message -->
                <div x-show="showFeedback"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="px-4 sm:px-6 pb-4 sm:pb-6">
                    <div :class="{
                        'p-3 sm:p-4 rounded-lg': true,
                        'bg-green-50 text-green-800': feedbackType === 'success',
                        'bg-red-50 text-red-800': feedbackType === 'error'
                    }">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <template x-if="feedbackType === 'success'">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                                <template x-if="feedbackType === 'error'">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs sm:text-sm font-medium" x-text="feedbackMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <button @click="previousQuestion"
                                :class="{
                                    'inline-flex items-center px-3 sm:px-4 py-2 border border-gray-300 shadow-sm text-xs sm:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500': true,
                                    'opacity-50 cursor-not-allowed': currentQuestion === 0
                                }"
                                :disabled="currentQuestion === 0">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </button>
                        <button @click="nextQuestion"
                                :class="{
                                    'inline-flex items-center px-3 sm:px-4 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500': true,
                                    'opacity-50 cursor-not-allowed': !canProceed()
                                }"
                                :disabled="!canProceed()">
                            <span x-text="currentQuestion === questions.length - 1 ? 'Finish' : 'Next'"></span>
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Debug Info (only shown during development) -->
    @if(config('app.debug'))
        <div class="mt-8 p-4 bg-gray-100 rounded-lg">
            <p class="text-xs sm:text-sm font-medium text-gray-700">Debug Info:</p>
            <pre class="mt-2 text-xs text-gray-600 overflow-x-auto">
                Questions: @json($quizData['questions'] ?? [], JSON_PRETTY_PRINT)
                Answers: @json($quizData['answers'] ?? [], JSON_PRETTY_PRINT)
                Correct Answers: @json($quizData['correct_answers'] ?? [], JSON_PRETTY_PRINT)
            </pre>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quizPlayer', (quizData) => ({
        currentQuestion: 0,
        questions: quizData.questions || [],
        answers: quizData.answers || [],
        correctAnswers: quizData.correct_answers || [],
        userAnswers: [],
        showFeedback: false,
        feedbackMessage: '',
        feedbackType: '',
        
        init() {
            this.userAnswers = new Array(this.questions.length).fill(null);
        },
        
        selectAnswer(questionIndex, answerIndex) {
            this.userAnswers[questionIndex] = answerIndex;
            this.showFeedback = true;
            
            if (answerIndex === this.correctAnswers[questionIndex]) {
                this.feedbackMessage = 'Correct! Well done!';
                this.feedbackType = 'success';
            } else {
                this.feedbackMessage = 'Incorrect. Try again!';
                this.feedbackType = 'error';
            }
        },
        
        isAnswerSelected(questionIndex, answerIndex) {
            return this.userAnswers[questionIndex] === answerIndex;
        },
        
        isCorrectAnswer(questionIndex, answerIndex) {
            return this.showFeedback && answerIndex === this.correctAnswers[questionIndex];
        },
        
        isIncorrectAnswer(questionIndex, answerIndex) {
            return this.showFeedback && this.isAnswerSelected(questionIndex, answerIndex) && !this.isCorrectAnswer(questionIndex, answerIndex);
        },
        
        canProceed() {
            return this.userAnswers[this.currentQuestion] !== null;
        },
        
        previousQuestion() {
            if (this.currentQuestion > 0) {
                this.currentQuestion--;
                this.showFeedback = false;
            }
        },
        
        nextQuestion() {
            if (this.currentQuestion < this.questions.length - 1 && this.canProceed()) {
                this.currentQuestion++;
                this.showFeedback = false;
            }
        }
    }));
});
</script>
@endpush
