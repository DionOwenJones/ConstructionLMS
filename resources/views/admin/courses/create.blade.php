@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form id="courseForm" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Course Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Course Information</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                        <input type="text" name="title" id="title" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (£)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">£</span>
                            </div>
                            <input type="number" name="price" id="price" required min="0" step="0.01"
                                   class="pl-7 block w-full rounded-md border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <input type="file" name="image" accept="image/*" required id="courseImage"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
                                       onchange="previewImage(this)">
                            </div>
                        </div>
                        <div id="thumbnail-preview" class="mt-2 hidden">
                            <img src="" alt="Thumbnail preview" class="h-32 w-auto rounded-lg">
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Certificate Expiry -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="has_expiry" id="has_expiry"
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="has_expiry" class="font-medium text-gray-700">Enable Certificate Expiration</label>
                                <p class="text-gray-500 text-sm">Set an expiration period for course certificates</p>
                            </div>
                        </div>
                        
                        <div id="expiry-settings" class="mt-4 hidden">
                            <div class="flex items-center space-x-2">
                                <input type="number" name="validity_months" min="1" value="12"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">months</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Assessment Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-start mb-6">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="has_assessment" id="has_assessment"
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="has_assessment" class="font-medium text-gray-700">Enable Course Assessment</label>
                        <p class="text-gray-500 text-sm">Add an assessment that students must pass to complete the course</p>
                    </div>
                </div>

                <div id="assessment-settings" class="space-y-6 hidden">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <!-- Title and Description -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <label for="assessment_title" class="block text-sm font-medium text-gray-700">Assessment Title</label>
                                <input type="text" name="assessment_title" id="assessment_title"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors">
                            </div>

                            <div>
                                <label for="assessment_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="assessment_description" id="assessment_description" rows="3"
                                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"></textarea>
                            </div>
                        </div>

                        <!-- Settings Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Time Limit -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit</label>
                                    <span class="text-xs text-gray-500">minutes</span>
                                </div>
                                <input type="number" name="time_limit" id="time_limit" min="1"
                                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors">
                            </div>

                            <!-- Passing Score -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label for="passing_score" class="block text-sm font-medium text-gray-700">Passing Score</label>
                                    <span class="text-xs text-gray-500">percentage</span>
                                </div>
                                <input type="number" name="passing_score" id="passing_score" min="0" max="100"
                                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors">
                            </div>

                            <!-- Maximum Attempts -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label for="max_attempts" class="block text-sm font-medium text-gray-700">Maximum Attempts</label>
                                    <span class="text-xs text-gray-500">tries</span>
                                </div>
                                <input type="number" name="max_attempts" id="max_attempts" min="1"
                                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors">
                            </div>
                        </div>

                        <!-- Toggle Options -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Randomize Questions -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="randomize_questions" id="randomize_questions" class="sr-only">
                                        <div class="block bg-gray-200 w-14 h-8 rounded-full transition-colors"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform"></div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Randomize Questions</span>
                                        <p class="text-xs text-gray-500 mt-1">Questions will be presented in random order</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Show Feedback -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="show_feedback" id="show_feedback" class="sr-only">
                                        <div class="block bg-gray-200 w-14 h-8 rounded-full transition-colors"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform"></div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Show Feedback</span>
                                        <p class="text-xs text-gray-500 mt-1">Display feedback after submission</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Questions -->
            <div id="assessment-section" class="bg-white rounded-lg shadow-sm p-6 mb-6 hidden">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Assessment Questions</h3>
                        <p class="text-sm text-gray-500">Add and manage your assessment questions</p>
                    </div>
                    <button type="button" id="add-question" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Question
                    </button>
                </div>
                
                <div id="questions-container" class="space-y-6">
                    <!-- Questions will be added here -->
                </div>
            </div>

            <!-- Question Template -->
            <template id="question-template">
                <div class="question-block bg-gray-50 rounded-xl p-6 border border-gray-200 transform transition-all duration-200 hover:shadow-md">
                    <div class="space-y-6">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white rounded-lg px-3 py-2 shadow-sm">
                                    <span class="text-sm font-medium text-gray-700">Question <span class="question-number"></span></span>
                                </div>
                                <select name="questions[INDEX][type]" class="question-type rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm transition-colors">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="matching">Matching</option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-600">Points:</label>
                                    <input type="number" name="questions[INDEX][points]" min="1" value="1" 
                                           class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm transition-colors"
                                           placeholder="Points">
                                </div>
                                <button type="button" class="delete-question p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Question Content -->
                        <div class="bg-white rounded-lg p-6 shadow-sm space-y-6">
                            <!-- Question Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                                <textarea name="questions[INDEX][text]" rows="2" 
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                          required></textarea>
                            </div>

                            <!-- Answer Options Container -->
                            <div class="answer-options space-y-6">
                                <!-- Multiple Choice Options -->
                                <div class="multiple-choice-options">
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                                        <button type="button" class="add-option inline-flex items-center px-3 py-1.5 border border-orange-600 rounded-md text-sm text-orange-600 hover:bg-orange-50 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Option
                                        </button>
                                    </div>
                                    <div class="options-container space-y-3">
                                        <!-- Options will be added here -->
                                    </div>
                                </div>

                                <!-- True/False Options -->
                                <div class="true-false-options hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-4">Correct Answer</label>
                                    <div class="space-y-3">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="questions[INDEX][correct_answer]" value="true"
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <span class="ml-3 text-gray-700">True</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="questions[INDEX][correct_answer]" value="false"
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <span class="ml-3 text-gray-700">False</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Matching Options -->
                                <div class="matching-options hidden">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-4">Items</label>
                                            <div class="matching-items-container space-y-3">
                                                <!-- Matching items will be added here -->
                                            </div>
                                            <button type="button" class="add-matching-item mt-3 inline-flex items-center px-3 py-1.5 border border-orange-600 rounded-md text-sm text-orange-600 hover:bg-orange-50 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Add Item
                                            </button>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-4">Matches</label>
                                            <div class="matching-matches-container space-y-3">
                                                <!-- Matching matches will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Feedback</label>
                                <textarea name="questions[INDEX][feedback]" rows="2"
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                          placeholder="Optional feedback to show students after answering"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Option Template -->
            <template id="option-template">
                <div class="option-row flex items-center space-x-3">
                    <input type="radio" name="questions[INDEX][correct_answer]" value="OPTION_INDEX"
                           class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                    <input type="text" name="questions[INDEX][options][]" 
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                           placeholder="Enter option text" required>
                    <button type="button" class="delete-option p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Matching Item Template -->
            <template id="matching-item-template">
                <div class="matching-item flex items-center space-x-3">
                    <input type="text" name="questions[INDEX][matching_items][]" 
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                           placeholder="Enter item" required>
                    <input type="text" name="questions[INDEX][matching_matches][]" 
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                           placeholder="Enter matching answer" required>
                    <button type="button" class="delete-matching-item p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Assessment Questions -->
            <div id="assessment-section" class="bg-white rounded-lg shadow-sm p-6 mb-6 hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Assessment Questions</h2>
                    <button type="button" id="add-question" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Question
                    </button>
                </div>

                <div id="questions-container" class="space-y-6">
                    <!-- Questions will be added here -->
                </div>
            </div>

            <!-- Question Template -->
            <template id="question-template">
                <div class="question-block bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="space-y-6">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white rounded-lg px-3 py-2 shadow-sm">
                                    <span class="text-sm font-medium text-gray-700">Question <span class="question-number"></span></span>
                                </div>
                                <select name="questions[INDEX][type]" class="question-type rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm transition-colors">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="matching">Matching</option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="questions[INDEX][points]" min="1" value="1" 
                                       class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm transition-colors"
                                       placeholder="Points">
                                <button type="button" class="delete-question p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Question Content -->
                        <div class="bg-white rounded-lg p-6 shadow-sm space-y-6">
                            <!-- Question Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                                <textarea name="questions[INDEX][text]" rows="2" 
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                          required></textarea>
                            </div>

                            <!-- Answer Options Container -->
                            <div class="answer-options space-y-6">
                                <!-- Multiple Choice -->
                                <div class="multiple-choice-options">
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                                        <button type="button" class="add-option inline-flex items-center text-sm text-orange-600 hover:text-orange-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Option
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-3">
                                            <input type="radio" name="questions[INDEX][correct_answer]" value="0" required
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <input type="text" name="questions[INDEX][options][]" 
                                                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                   placeholder="Option 1" required>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <input type="radio" name="questions[INDEX][correct_answer]" value="1"
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <input type="text" name="questions[INDEX][options][]" 
                                                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                   placeholder="Option 2" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- True/False -->
                                <div class="true-false-options hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-4">Correct Answer</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                            <input type="radio" name="questions[INDEX][correct_answer_tf]" value="true"
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <span class="ml-3 text-sm">True</span>
                                        </label>
                                        <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                            <input type="radio" name="questions[INDEX][correct_answer_tf]" value="false"
                                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                            <span class="ml-3 text-sm">False</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Matching -->
                                <div class="matching-options hidden">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <div class="flex items-center justify-between mb-4">
                                                <label class="block text-sm font-medium text-gray-700">Left Items</label>
                                                <button type="button" class="add-matching-pair inline-flex items-center text-sm text-orange-600 hover:text-orange-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    Add Pair
                                                </button>
                                            </div>
                                            <div class="space-y-3 matching-left-items">
                                                <input type="text" name="questions[INDEX][matching_left][]" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                       placeholder="Left Item 1" required>
                                                <input type="text" name="questions[INDEX][matching_left][]" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                       placeholder="Left Item 2" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-4">Right Items</label>
                                            <div class="space-y-3 matching-right-items">
                                                <input type="text" name="questions[INDEX][matching_right][]" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                       placeholder="Right Item 1" required>
                                                <input type="text" name="questions[INDEX][matching_right][]" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                                       placeholder="Right Item 2" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Feedback</label>
                                <textarea name="questions[INDEX][feedback]" rows="2" 
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                          placeholder="Provide feedback that will be shown to students after answering this question"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <style>
                /* Modern Toggle Switch Styles */
                input[type="checkbox"] + .block {
                    transition: background-color 0.3s ease;
                }
                input[type="checkbox"]:checked + .block {
                    background-color: #ea580c;
                }
                input[type="checkbox"]:checked + .block + .dot {
                    transform: translateX(100%);
                }
                .dot {
                    transition: transform 0.3s ease;
                }
            </style>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Assessment toggle functionality
                    const hasAssessmentCheckbox = document.getElementById('has_assessment');
                    const assessmentSettings = document.getElementById('assessment-settings');
                    const assessmentSection = document.getElementById('assessment-section');

                    if (hasAssessmentCheckbox && assessmentSettings && assessmentSection) {
                        hasAssessmentCheckbox.addEventListener('change', function() {
                            assessmentSettings.classList.toggle('hidden', !this.checked);
                            assessmentSection.classList.toggle('hidden', !this.checked);
                        });
                    }

                    // Question management
                    const addQuestionButton = document.getElementById('add-question');
                    const questionTemplate = document.getElementById('question-template');
                    const optionTemplate = document.getElementById('option-template');
                    const matchingItemTemplate = document.getElementById('matching-item-template');
                    const questionsContainer = document.getElementById('questions-container');
                    let questionCounter = 1;

                    function createOption(questionIndex, optionIndex) {
                        const optionNode = optionTemplate.content.cloneNode(true);
                        const optionRow = optionNode.querySelector('.option-row');
                        
                        // Update name and value attributes
                        optionRow.querySelector('input[type="radio"]').name = `questions[${questionIndex}][correct_answer]`;
                        optionRow.querySelector('input[type="radio"]').value = optionIndex;
                        optionRow.querySelector('input[type="text"]').name = `questions[${questionIndex}][options][]`;

                        // Add delete functionality
                        optionRow.querySelector('.delete-option').addEventListener('click', function() {
                            optionRow.remove();
                            updateOptionIndexes(questionIndex);
                        });

                        return optionRow;
                    }

                    function createMatchingItem(questionIndex) {
                        const itemNode = matchingItemTemplate.content.cloneNode(true);
                        const itemRow = itemNode.querySelector('.matching-item');
                        
                        // Update name attributes
                        itemRow.querySelector('input[name*="matching_items"]').name = `questions[${questionIndex}][matching_items][]`;
                        itemRow.querySelector('input[name*="matching_matches"]').name = `questions[${questionIndex}][matching_matches][]`;

                        // Add delete functionality
                        itemRow.querySelector('.delete-matching-item').addEventListener('click', function() {
                            itemRow.remove();
                        });

                        return itemRow;
                    }

                    function updateOptionIndexes(questionIndex) {
                        const questionBlock = document.querySelector(`[data-question-index="${questionIndex}"]`);
                        if (!questionBlock) return;

                        const options = questionBlock.querySelectorAll('.option-row');
                        options.forEach((option, index) => {
                            option.querySelector('input[type="radio"]').value = index;
                        });
                    }

                    function setupQuestionTypeHandling(questionBlock, questionIndex) {
                        const typeSelect = questionBlock.querySelector('.question-type');
                        const multipleChoiceOptions = questionBlock.querySelector('.multiple-choice-options');
                        const trueFalseOptions = questionBlock.querySelector('.true-false-options');
                        const matchingOptions = questionBlock.querySelector('.matching-options');

                        typeSelect.addEventListener('change', function() {
                            // Hide all option types first
                            multipleChoiceOptions.classList.add('hidden');
                            trueFalseOptions.classList.add('hidden');
                            matchingOptions.classList.add('hidden');

                            // Show the selected option type
                            switch(this.value) {
                                case 'multiple_choice':
                                    multipleChoiceOptions.classList.remove('hidden');
                                    break;
                                case 'true_false':
                                    trueFalseOptions.classList.remove('hidden');
                                    break;
                                case 'matching':
                                    matchingOptions.classList.remove('hidden');
                                    break;
                            }
                        });

                        // Add option button functionality
                        const addOptionBtn = questionBlock.querySelector('.add-option');
                        const optionsContainer = questionBlock.querySelector('.options-container');
                        let optionCounter = 0;

                        addOptionBtn.addEventListener('click', function() {
                            const option = createOption(questionIndex, optionCounter++);
                            optionsContainer.appendChild(option);
                        });

                        // Add matching item button functionality
                        const addMatchingItemBtn = questionBlock.querySelector('.add-matching-item');
                        const matchingItemsContainer = questionBlock.querySelector('.matching-items-container');

                        addMatchingItemBtn.addEventListener('click', function() {
                            const item = createMatchingItem(questionIndex);
                            matchingItemsContainer.appendChild(item);
                        });

                        // Add initial options/items
                        if (optionsContainer) {
                            // Add two default options for multiple choice
                            optionsContainer.appendChild(createOption(questionIndex, optionCounter++));
                            optionsContainer.appendChild(createOption(questionIndex, optionCounter++));
                        }

                        if (matchingItemsContainer) {
                            // Add two default matching items
                            matchingItemsContainer.appendChild(createMatchingItem(questionIndex));
                            matchingItemsContainer.appendChild(createMatchingItem(questionIndex));
                        }
                    }

                    if (addQuestionButton && questionTemplate && questionsContainer) {
                        addQuestionButton.addEventListener('click', function() {
                            const questionBlock = questionTemplate.content.cloneNode(true);
                            const questionIndex = questionCounter++;

                            // Set data attribute for the question block
                            questionBlock.querySelector('.question-block').dataset.questionIndex = questionIndex;

                            // Update question number
                            questionBlock.querySelector('.question-number').textContent = questionIndex;

                            // Update all name attributes with the current index
                            questionBlock.querySelectorAll('[name*="[INDEX]"]').forEach(element => {
                                element.name = element.name.replace('[INDEX]', `[${questionIndex}]`);
                            });

                            // Add delete functionality
                            const deleteButton = questionBlock.querySelector('.delete-question');
                            if (deleteButton) {
                                deleteButton.addEventListener('click', function() {
                                    this.closest('.question-block').remove();
                                    updateQuestionNumbers();
                                });
                            }

                            // Add the question block to the container with a fade-in animation
                            const container = document.createElement('div');
                            container.appendChild(questionBlock);
                            container.firstElementChild.style.opacity = '0';
                            questionsContainer.appendChild(container.firstElementChild);

                            // Setup question type handling
                            setupQuestionTypeHandling(container.firstElementChild, questionIndex);

                            // Trigger fade-in animation
                            requestAnimationFrame(() => {
                                container.firstElementChild.style.opacity = '1';
                                container.firstElementChild.style.transition = 'opacity 0.3s ease-in-out';
                            });
                        });
                    }

                    // Update question numbers function
                    function updateQuestionNumbers() {
                        document.querySelectorAll('.question-number').forEach((el, index) => {
                            el.textContent = index + 1;
                        });
                        questionCounter = document.querySelectorAll('.question-block').length + 1;
                    }

                    // Add validation before form submission
                    const courseForm = document.getElementById('courseForm');
                    if (courseForm) {
                        courseForm.addEventListener('submit', function(e) {
                            if (hasAssessmentCheckbox.checked) {
                                const questions = document.querySelectorAll('.question-block');
                                if (questions.length === 0) {
                                    e.preventDefault();
                                    alert('Please add at least one question to the assessment.');
                                    return;
                                }

                                // Validate each question
                                questions.forEach((question, index) => {
                                    const questionType = question.querySelector('.question-type').value;
                                    const questionText = question.querySelector('textarea[name*="[text]"]').value.trim();
                                    
                                    if (!questionText) {
                                        e.preventDefault();
                                        alert(`Question ${index + 1} text is required.`);
                                        return;
                                    }

                                    if (questionType === 'multiple_choice') {
                                        const options = question.querySelectorAll('input[name*="[options][]"]');
                                        const selectedAnswer = question.querySelector('input[name*="[correct_answer]"]:checked');
                                        
                                        if (options.length < 2) {
                                            e.preventDefault();
                                            alert(`Question ${index + 1} must have at least two options.`);
                                            return;
                                        }

                                        if (!selectedAnswer) {
                                            e.preventDefault();
                                            alert(`Please select a correct answer for question ${index + 1}.`);
                                            return;
                                        }
                                    } else if (questionType === 'true_false') {
                                        const selectedAnswer = question.querySelector('input[name*="[correct_answer]"]:checked');
                                        if (!selectedAnswer) {
                                            e.preventDefault();
                                            alert(`Please select a correct answer for question ${index + 1}.`);
                                            return;
                                        }
                                    } else if (questionType === 'matching') {
                                        const items = question.querySelectorAll('input[name*="[matching_items][]"]');
                                        const matches = question.querySelectorAll('input[name*="[matching_matches][]"]');
                                        
                                        if (items.length < 2) {
                                            e.preventDefault();
                                            alert(`Question ${index + 1} must have at least two matching pairs.`);
                                            return;
                                        }

                                        if (items.length !== matches.length) {
                                            e.preventDefault();
                                            alert(`Question ${index + 1} must have an equal number of items and matches.`);
                                            return;
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            </script>
            @endpush

            <!-- Course Content -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Course Content</h2>
                    <p class="mt-1 text-sm text-gray-500">Organize your course content into sections</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-12 gap-6">
                        <!-- Content Blocks Library -->
                        <div class="col-span-3">
                            <div class="bg-gray-50 rounded-lg p-4 sticky top-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-4">Content Blocks</h3>
                                <div id="blocks-library" class="space-y-2">
                                    <!-- Text Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="text">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Text Block</span>
                                        </div>
                                    </div>

                                    <!-- Video Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="video">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Video Block</span>
                                        </div>
                                    </div>

                                    <!-- Image Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="image">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Image Block</span>
                                        </div>
                                    </div>

                                    <!-- Quiz Block -->
                                    <div class="block-template cursor-move p-3 bg-white rounded-md shadow-sm border border-gray-200" data-type="quiz">
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Quiz Block</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Sections -->
                        <div class="col-span-9">
                            <div id="sections-container" class="space-y-6">
                                <!-- Sections will be added here -->
                            </div>

                            <button type="button" id="add-section"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Section
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Section Template -->
<template id="section-template">
    <div class="course-section bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <input type="text" name="sections[{index}][title]" placeholder="Section Title" required
                       class="text-lg font-medium text-gray-900 border-none focus:ring-0 w-full">
                <button type="button" class="remove-section text-gray-400 hover:text-red-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="section-content">
            <input type="hidden" name="sections[{index}][content]" value="">
            <div class="content-blocks min-h-[100px] p-4" data-section-index="{index}">
                <!-- Content blocks will be dropped here -->
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/course-builder.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Assessment toggle functionality
        const hasAssessmentCheckbox = document.getElementById('has_assessment');
        const assessmentSettings = document.getElementById('assessment-settings');
        const assessmentSection = document.getElementById('assessment-section');

        if (hasAssessmentCheckbox) {
            hasAssessmentCheckbox.addEventListener('change', function() {
                console.log('Assessment checkbox changed:', this.checked); // Debug log
                if (assessmentSettings) {
                    assessmentSettings.classList.toggle('hidden', !this.checked);
                }
                if (assessmentSection) {
                    assessmentSection.classList.toggle('hidden', !this.checked);
                }

                // Make fields required when assessment is enabled
                const requiredFields = ['assessment_title', 'assessment_description', 'time_limit', 'passing_score', 'max_attempts'];
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.required = this.checked;
                    }
                });
            });
        }

        // Certificate expiry toggle functionality
        const hasExpiryCheckbox = document.getElementById('has_expiry');
        const expirySettings = document.getElementById('expiry-settings');

        if (hasExpiryCheckbox && expirySettings) {
            hasExpiryCheckbox.addEventListener('change', function() {
                expirySettings.classList.toggle('hidden', !this.checked);
            });
        }

        // Question management
        const questionsContainer = document.getElementById('questions-container');
        const questionTemplate = document.getElementById('question-template');
        const addQuestionButton = document.getElementById('add-question');
        let questionCounter = 0;

        if (addQuestionButton && questionTemplate && questionsContainer) {
            addQuestionButton.addEventListener('click', function() {
                const questionBlock = questionTemplate.content.cloneNode(true);
                const questionIndex = questionCounter++;

                // Update all name attributes with the current index
                questionBlock.querySelectorAll('[name*="INDEX"]').forEach(element => {
                    element.name = element.name.replace('INDEX', questionIndex);
                });

                // Add event listeners for question type changes
                const questionTypeSelect = questionBlock.querySelector('.question-type');
                const multipleChoiceOptions = questionBlock.querySelector('.multiple-choice-options');
                const trueFalseOptions = questionBlock.querySelector('.true-false-options');
                const matchingOptions = questionBlock.querySelector('.matching-options');

                if (questionTypeSelect) {
                    questionTypeSelect.addEventListener('change', function() {
                        multipleChoiceOptions.classList.toggle('hidden', this.value !== 'multiple_choice');
                        trueFalseOptions.classList.toggle('hidden', this.value !== 'true_false');
                        matchingOptions.classList.toggle('hidden', this.value !== 'matching');
                    });
                }

                // Add option button functionality
                const addOptionButton = questionBlock.querySelector('.add-option');
                const optionsContainer = questionBlock.querySelector('.multiple-choice-options .space-y-2');

                if (addOptionButton && optionsContainer) {
                    addOptionButton.addEventListener('click', function() {
                        const optionCount = optionsContainer.children.length;
                        const newOption = document.createElement('div');
                        newOption.className = 'flex items-center space-x-3';
                        newOption.innerHTML = `
                            <input type="radio" name="questions[${questionIndex}][correct_answer]" value="${optionCount}"
                                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                            <input type="text" name="questions[${questionIndex}][options][]" 
                                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors"
                                   placeholder="Option ${optionCount + 1}" required>
                            <button type="button" class="text-red-600 hover:text-red-700" onclick="this.parentElement.remove()">×</button>
                        `;
                        optionsContainer.appendChild(newOption);
                    });
                }

                // Add matching pair button functionality
                const addMatchingPairButton = questionBlock.querySelector('.add-matching-pair');
                const matchingLeftContainer = questionBlock.querySelector('.matching-left-items');
                const matchingRightContainer = questionBlock.querySelector('.matching-right-items');

                if (addMatchingPairButton && matchingLeftContainer && matchingRightContainer) {
                    addMatchingPairButton.addEventListener('click', function() {
                        const pairCount = matchingLeftContainer.children.length;

                        const leftInput = document.createElement('input');
                        leftInput.type = 'text';
                        leftInput.name = `questions[${questionIndex}][matching_left][]`;
                        leftInput.className = 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors';
                        leftInput.placeholder = `Left Item ${pairCount + 1}`;
                        leftInput.required = true;

                        const rightInput = document.createElement('input');
                        rightInput.type = 'text';
                        rightInput.name = `questions[${questionIndex}][matching_right][]`;
                        rightInput.className = 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-colors';
                        rightInput.placeholder = `Right Item ${pairCount + 1}`;
                        rightInput.required = true;

                        matchingLeftContainer.appendChild(leftInput);
                        matchingRightContainer.appendChild(rightInput);
                    });
                }

                // Delete question button functionality
                const deleteButton = questionBlock.querySelector('.delete-question');
                if (deleteButton) {
                    deleteButton.addEventListener('click', function() {
                        this.closest('.question-block').remove();
                        updateQuestionNumbers();
                    });
                }

                questionsContainer.appendChild(questionBlock);
                updateQuestionNumbers();

                // Add entrance animation
                const newQuestion = questionsContainer.lastElementChild;
                newQuestion.style.opacity = '0';
                newQuestion.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    newQuestion.style.opacity = '1';
                    newQuestion.style.transform = 'translateY(0)';
                }, 10);
            });
        }

        // Update question numbers function
        function updateQuestionNumbers() {
            document.querySelectorAll('.question-number').forEach((el, index) => {
                el.textContent = index + 1;
            });
        }

        // Initialize animation classes
        const questionBlocks = document.querySelectorAll('.question-block');
        questionBlocks.forEach(block => {
            block.classList.add('transition-all', 'duration-300', 'ease-in-out');
        });
    });
</script>
@endpush

@endsection
