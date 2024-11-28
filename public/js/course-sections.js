document.addEventListener('DOMContentLoaded', function() {
    const sectionsContainer = document.getElementById('sections-container');
    const addSectionButton = document.getElementById('addSection');
    let sectionCount = 0;

    // Create initial section template
    const sectionTemplate = `
        <div class="section p-6 border-t border-gray-100">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <input type="text" name="sections[{index}][title]" required
                           placeholder="Section Title"
                           class="text-lg font-medium border-none focus:ring-0 w-full">
                    <button type="button" class="remove-section text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <div class="content-type-selector grid grid-cols-2 md:grid-cols-4 gap-3">
                    <button type="button" class="content-type-btn flex flex-col items-center p-4 border rounded-lg hover:border-orange-500" data-type="text">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        <span class="mt-2 text-sm font-medium">Text</span>
                    </button>
                    <button type="button" class="content-type-btn flex flex-col items-center p-4 border rounded-lg hover:border-orange-500" data-type="image">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="mt-2 text-sm font-medium">Image</span>
                    </button>
                    <button type="button" class="content-type-btn flex flex-col items-center p-4 border rounded-lg hover:border-orange-500" data-type="video">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0121 8.618v6.764a2 2 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span class="mt-2 text-sm font-medium">Video</span>
                    </button>
                    <button type="button" class="content-type-btn flex flex-col items-center p-4 border rounded-lg hover:border-orange-500" data-type="quiz">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="mt-2 text-sm font-medium">Quiz</span>
                    </button>
                </div>

                <div class="content-area mt-4">
                    <!-- Dynamic content will be inserted here -->
                </div>
            </div>
        </div>
    `;

    function createSection() {
        const section = document.createElement('div');
        section.innerHTML = sectionTemplate.replace(/{index}/g, sectionCount);

        // Add event listeners to content type buttons
        section.querySelectorAll('.content-type-btn').forEach(button => {
            button.addEventListener('click', function() {
                handleContentTypeSelection(this, section);
            });
        });

        // Add remove section handler
        section.querySelector('.remove-section').addEventListener('click', function() {
            section.remove();
        });

        sectionsContainer.appendChild(section);
        sectionCount++;
    }

    function handleContentTypeSelection(button, section) {
        const contentArea = section.querySelector('.content-area');
        const type = button.dataset.type;
        const sectionIndex = sectionCount - 1;

        // Update active state of buttons
        section.querySelectorAll('.content-type-btn').forEach(btn => {
            btn.classList.remove('border-orange-500', 'bg-orange-50');
        });
        button.classList.add('border-orange-500', 'bg-orange-50');

        // Clear existing content
        contentArea.innerHTML = '';

        switch (type) {
            case 'text':
                contentArea.innerHTML = `
                    <div class="space-y-2">
                        <input type="hidden" name="sections[${sectionIndex}][blocks][0][type]" value="text">
                        <textarea name="sections[${sectionIndex}][blocks][0][text_content]" rows="6" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Enter your content here..."></textarea>
                    </div>`;
                break;

            case 'video':
                contentArea.innerHTML = `
                    <div class="space-y-4">
                        <input type="hidden" name="sections[${sectionIndex}][blocks][0][type]" value="video">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">YouTube Video URL</label>
                            <input type="url" name="sections[${sectionIndex}][blocks][0][video_url]" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   onchange="previewYouTubeVideo(this, ${sectionIndex})">
                            <input type="text" name="sections[${sectionIndex}][blocks][0][video_title]" required
                                   class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                   placeholder="Enter video title">
                            <p class="text-sm text-gray-500">Paste a YouTube video URL (e.g., https://www.youtube.com/watch?v=xxxxx)</p>
                        </div>
                        <div id="video-preview-${sectionIndex}" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video Preview</label>
                            <div class="max-w-4xl mx-auto">
                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg">
                                    <div id="video-player-${sectionIndex}" class="w-full h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                break;

            case 'quiz':
                contentArea.innerHTML = `
                    <input type="hidden" name="sections[${sectionIndex}][blocks][0][type]" value="quiz">
                    <div class="bg-white rounded-lg p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Quiz Questions</h3>
                            <button type="button" 
                                    onclick="addQuizQuestion(${sectionIndex})"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Add Question
                            </button>
                        </div>
                        <div id="quiz-questions-${sectionIndex}" class="space-y-6">
                            <!-- Questions will be added here -->
                        </div>
                    </div>`;
                
                // Add initial question
                addQuizQuestion(sectionIndex);
                break;

            case 'image':
                contentArea.innerHTML = `
                    <div class="space-y-2">
                        <input type="hidden" name="sections[${sectionIndex}][blocks][0][type]" value="image">
                        <label class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <input type="file" name="sections[${sectionIndex}][blocks][0][image_path]" accept="image/*" required
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>`;
                break;
        }
    }

    // YouTube URL validation and preview
    window.previewYouTubeVideo = function(input, sectionIndex) {
        const url = input.value;
        const videoId = extractYouTubeVideoId(url);
        const previewDiv = document.getElementById(`video-preview-${sectionIndex}`);
        const playerDiv = document.getElementById(`video-player-${sectionIndex}`);

        if (videoId) {
            previewDiv.classList.remove('hidden');
            playerDiv.innerHTML = `
                <div class="w-full max-w-5xl mx-auto">
                    <div class="relative" style="padding-bottom: 56.25%;">
                        <iframe 
                            src="https://www.youtube.com/embed/${videoId}"
                            class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>`;
        } else {
            previewDiv.classList.add('hidden');
            playerDiv.innerHTML = '';
            input.setCustomValidity('Please enter a valid YouTube URL');
        }
    };

    function extractYouTubeVideoId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    // Quiz functionality
    window.addQuizQuestion = function(sectionIndex, questionIndex = null) {
        const questionsContainer = document.getElementById(`quiz-questions-${sectionIndex}`);
        const newQuestionIndex = questionIndex ?? questionsContainer.children.length;
        
        const questionHTML = `
            <div class="quiz-question bg-white rounded-lg border border-gray-200 p-6 space-y-4" data-question-index="${newQuestionIndex}">
                <div class="flex justify-between items-start">
                    <h4 class="text-lg font-medium text-gray-900">Question ${newQuestionIndex + 1}</h4>
                    <button type="button" onclick="removeQuizQuestion(this)" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" 
                               name="sections[${sectionIndex}][blocks][0][quiz_data][questions][${newQuestionIndex}][text]" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                               placeholder="Enter your question here"
                               required>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                            <button type="button" 
                                    onclick="addOption(${sectionIndex}, ${newQuestionIndex})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Add Option
                            </button>
                        </div>
                        <div class="space-y-2" id="options-container-${sectionIndex}-${newQuestionIndex}">
                            <!-- Initial option -->
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       name="sections[${sectionIndex}][blocks][0][quiz_data][questions][${newQuestionIndex}][options][]" 
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                       placeholder="Option 1"
                                       required>
                                <input type="radio" 
                                       name="sections[${sectionIndex}][blocks][0][quiz_data][questions][${newQuestionIndex}][correct_answer]" 
                                       value="0"
                                       required>
                                <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Select the radio button next to the correct answer.</p>
                    </div>
                </div>
            </div>
        `;
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = questionHTML;
        questionsContainer.appendChild(tempDiv.firstElementChild);
    };

    window.removeQuizQuestion = function(button) {
        const questionElement = button.closest('.quiz-question');
        if (questionElement.parentElement.children.length > 1) {
            questionElement.remove();
            updateQuestionNumbers(questionElement.parentElement);
        }
    };

    window.addOption = function(sectionIndex, questionIndex) {
        const optionsContainer = document.getElementById(`options-container-${sectionIndex}-${questionIndex}`);
        const optionIndex = optionsContainer.children.length;
        
        const optionHTML = `
            <div class="flex items-center space-x-2">
                <input type="text" 
                       name="sections[${sectionIndex}][blocks][0][quiz_data][questions][${questionIndex}][options][]" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                       placeholder="Option ${optionIndex + 1}"
                       required>
                <input type="radio" 
                       name="sections[${sectionIndex}][blocks][0][quiz_data][questions][${questionIndex}][correct_answer]" 
                       value="${optionIndex}"
                       required>
                <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = optionHTML;
        optionsContainer.appendChild(tempDiv.firstElementChild);
        updateOptionValues(optionsContainer);
    };

    window.removeOption = function(button) {
        const optionElement = button.closest('.flex');
        const optionsContainer = optionElement.parentElement;
        if (optionsContainer.children.length > 1) {
            optionElement.remove();
            updateOptionValues(optionsContainer);
        }
    };

    function updateOptionValues(optionsContainer) {
        const options = optionsContainer.querySelectorAll('.flex');
        options.forEach((option, index) => {
            const radio = option.querySelector('input[type="radio"]');
            radio.value = index;
        });
    }

    function updateQuestionNumbers(questionsContainer) {
        const questions = questionsContainer.querySelectorAll('.quiz-question');
        questions.forEach((question, index) => {
            // Update question number display
            const questionTitle = question.querySelector('h4');
            questionTitle.textContent = `Question ${index + 1}`;

            // Update question index in data attribute
            question.dataset.questionIndex = index;

            // Update form field names
            const textInput = question.querySelector('input[type="text"]');
            const optionsContainer = question.querySelector('[id^="options-container-"]');
            const sectionIndex = parseInt(optionsContainer.id.split('-')[2]);

            // Update question text input name
            textInput.name = `sections[${sectionIndex}][blocks][0][quiz_data][questions][${index}][text]`;

            // Update options
            const options = optionsContainer.querySelectorAll('.flex');
            options.forEach((option, optionIndex) => {
                const optionInput = option.querySelector('input[type="text"]');
                const radioInput = option.querySelector('input[type="radio"]');

                optionInput.name = `sections[${sectionIndex}][blocks][0][quiz_data][questions][${index}][options][]`;
                radioInput.name = `sections[${sectionIndex}][blocks][0][quiz_data][questions][${index}][correct_answer]`;
                radioInput.value = optionIndex;
            });
        });
    }

    // Initialize the add section button
    if (addSectionButton) {
        addSectionButton.addEventListener('click', createSection);
    }
});
