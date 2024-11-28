/**
 * Course Builder JavaScript
 * This file handles the dynamic course creation interface, including section management,
 * content block creation, and form submission.
 */

// Make removeContentBlock function globally accessible
window.removeContentBlock = function(button) {
    if (confirm('Are you sure you want to delete this content block?')) {
        // Find the closest parent content block
        const contentBlock = button.closest('.content-block');
        if (contentBlock) {
            // Remove the content block from the DOM
            contentBlock.parentElement.removeChild(contentBlock);
            
            // Find the section containing this block
            const section = button.closest('.content-blocks');
            if (section) {
                // Update indices of remaining blocks
                updateBlockIndices(section);
            }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Main container elements and counter
    const sectionsContainer = document.getElementById('sections-container');
    const addSectionButton = document.getElementById('add-section');
    const blocksLibrary = document.getElementById('blocks-library');
    let sectionCount = 0;

    // Initialize drag and drop functionality for the blocks library
    if (blocksLibrary) {
        new Sortable(blocksLibrary, {
            group: {
                name: 'shared',
                pull: 'clone',     // Allows blocks to be dragged from library
                put: false         // Prevents blocks from being dropped into library
            },
            sort: false,          // Prevents sorting within the library
            animation: 150,
            ghostClass: 'bg-orange-50',
            dragClass: 'shadow-lg'
        });
    }

    /**
     * Creates a new content block element based on the specified type
     * @param {string} type - The type of content block (text, video, image, quiz)
     * @param {number} sectionIndex - Index of the section containing this block
     * @param {number} blockIndex - Index of this block within its section
     * @returns {HTMLElement} The created content block element
     */
    function createContentBlock(type, sectionIndex, blockIndex) {
        const block = document.createElement('div');
        block.className = 'content-block bg-white rounded-lg shadow p-4 mb-4';
        block.setAttribute('data-type', type);

        // Common block header with drag handle and remove button
        let blockContent = `
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">${type.charAt(0).toUpperCase() + type.slice(1)} Block</span>
                </div>
                <button type="button" class="remove-block text-gray-400 hover:text-red-500 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <input type="hidden" name="sections[${sectionIndex}][blocks][${blockIndex}][type]" value="${type}">
        `;

        // Add specific input fields based on block type
        switch(type) {
            case 'text':
                // Text content block with rich text editor
                blockContent += `
                    <div class="mt-4">
                        <textarea name="sections[${sectionIndex}][blocks][${blockIndex}][text_content]" 
                                class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                                rows="4" 
                                placeholder="Enter text content"></textarea>
                    </div>`;
                break;

            case 'video':
                // Video block with URL and title inputs
                blockContent += `
                    <div class="mt-4 space-y-4">
                        <input type="text" 
                               name="sections[${sectionIndex}][blocks][${blockIndex}][video_url]" 
                               class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                               placeholder="Enter YouTube video URL">
                        <input type="text" 
                               name="sections[${sectionIndex}][blocks][${blockIndex}][video_title]" 
                               class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                               placeholder="Enter video title">
                        <p class="mt-1 text-sm text-gray-500">Example: https://www.youtube.com/watch?v=...</p>
                    </div>`;
                break;

            case 'image':
                // Image upload block
                blockContent += `
                    <div class="mt-4 space-y-4">
                        <input type="file" 
                               name="sections[${sectionIndex}][blocks][${blockIndex}][image_path]" 
                               class="w-full" 
                               accept="image/*">
                    </div>`;
                break;

            case 'quiz':
                // Quiz block with questions and answers
                blockContent += `
                    <div class="mt-4 space-y-4">
                        <div class="quiz-questions" data-section-index="${sectionIndex}" data-block-index="${blockIndex}">
                            <!-- Questions will be added here -->
                        </div>
                        <button type="button" 
                                onclick="addQuizQuestion(this)"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Add Question
                        </button>
                    </div>`;

                // After creating the block, add the first question
                setTimeout(() => {
                    const quizQuestions = block.querySelector('.quiz-questions');
                    if (quizQuestions) {
                        addQuizQuestion(quizQuestions);
                    }
                }, 0);
                break;
        }

        block.innerHTML = blockContent;

        // Add event listener to the remove button after the block is created
        const removeButton = block.querySelector('.remove-block');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                removeContentBlock(this);
            });
        }

        return block;
    }

    /**
     * Creates a new course section using the section template
     * Initializes drag-and-drop functionality for content blocks within the section
     */
    function createSection() {
        // Clone the section template
        const template = document.getElementById('section-template');
        const section = template.content.cloneNode(true).children[0];
        section.innerHTML = section.innerHTML.replace(/{index}/g, sectionCount);
        
        // Initialize sortable for content blocks within the section
        const contentBlocks = section.querySelector('.content-blocks');
        if (contentBlocks) {
            contentBlocks.dataset.sectionIndex = sectionCount;
            new Sortable(contentBlocks, {
                group: {
                    name: 'shared',
                    pull: true,    // Allows blocks to be dragged out
                    put: true      // Allows blocks to be dropped in
                },
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-orange-50',
                dragClass: 'shadow-lg',
                onAdd: function(evt) {
                    // Handle new block being added to section
                    const item = evt.item;
                    const type = item.getAttribute('data-type');
                    const sectionIndex = evt.to.dataset.sectionIndex;
                    const blockIndex = evt.newIndex;
                    
                    const contentBlock = createContentBlock(type, sectionIndex, blockIndex);
                    item.outerHTML = contentBlock.outerHTML;
                    
                    // Add event listener to the new remove button
                    const newBlock = evt.to.children[evt.newIndex];
                    if (newBlock) {
                        const removeButton = newBlock.querySelector('.remove-block');
                        if (removeButton) {
                            removeButton.addEventListener('click', function() {
                                removeContentBlock(this);
                            });
                        }
                    }
                    
                    // Update block indices after adding
                    updateBlockIndices(evt.to);
                },
                onUpdate: function(evt) {
                    // Update indices after reordering blocks
                    updateBlockIndices(evt.to);
                }
            });
        }

        // Add remove section functionality
        const removeButton = section.querySelector('.remove-section');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                section.remove();
                updateSectionIndices();
            });
        }

        sectionsContainer.appendChild(section);
        sectionCount++;
        updateSectionIndices();
    }

    /**
     * Updates the indices of blocks within a section after reordering
     * @param {HTMLElement} section - The section containing blocks to update
     */
    function updateBlockIndices(section) {
        const blocks = section.querySelectorAll('.content-block');
        const sectionIndex = section.dataset.sectionIndex;
        
        blocks.forEach((block, index) => {
            // Update all input field names with new indices
            block.querySelectorAll('input, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/g, function(match, offset) {
                        // Replace only the block index, not the section index
                        return offset === name.indexOf('[blocks][') + 8 ? `[${index}]` : match;
                    });
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    /**
     * Updates the indices of all sections after reordering or removal
     */
    function updateSectionIndices() {
        const sections = sectionsContainer.querySelectorAll('.course-section');
        sections.forEach((section, index) => {
            const inputs = section.querySelectorAll('input[name^="sections"], textarea[name^="sections"]');
            inputs.forEach(input => {
                const name = input.name;
                input.name = name.replace(/sections\[\d+\]/, `sections[${index}]`);
            });
            
            const contentBlocks = section.querySelector('.content-blocks');
            if (contentBlocks) {
                contentBlocks.dataset.sectionIndex = index;
            }
        });
    }

    // Initialize the add section button
    if (addSectionButton) {
        addSectionButton.addEventListener('click', createSection);
    }

    // Handle certificate expiry settings visibility
    const expiryCheckbox = document.getElementById('has_expiry');
    const expirySettings = document.getElementById('expiry-settings');
    if (expiryCheckbox && expirySettings) {
        expiryCheckbox.addEventListener('change', function() {
            expirySettings.classList.toggle('hidden', !this.checked);
        });
    }

    // Handle course thumbnail image preview
    const thumbnailInput = document.querySelector('input[name="image"]');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    if (thumbnailInput && thumbnailPreview) {
        thumbnailInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbnailPreview.querySelector('img').src = e.target.result;
                    thumbnailPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /**
     * Handles the course form submission
     * Collects all section and block data, converts to JSON, and submits via AJAX
     */
    document.getElementById('courseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Process all sections and their content blocks
        document.querySelectorAll('.course-section').forEach((section, sectionIndex) => {
            const contentBlocks = [];
            section.querySelectorAll('.content-block').forEach((block) => {
                const blockType = block.getAttribute('data-type');
                const blockContent = {};
                
                // Collect all input values from the block
                block.querySelectorAll('input, textarea').forEach(input => {
                    blockContent[input.name.split('_').pop()] = input.value;
                });
                
                contentBlocks.push({
                    type: blockType,
                    content: blockContent
                });
            });
            
            // Store the JSON string in the hidden content field
            const contentInput = section.querySelector('input[name="sections[' + sectionIndex + '][content]"]');
            contentInput.value = JSON.stringify(contentBlocks);
        });

        // Prepare and send form data via AJAX
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Handle both JSON and HTML responses
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json().then(data => {
                    if (response.ok) {
                        window.location.href = data.redirect || '/admin/courses';
                    } else {
                        throw new Error(data.message || 'Error creating course');
                    }
                });
            } else {
                // Handle HTML response (like redirect or error page)
                window.location.href = response.url;
            }
        })
        .catch(error => {
            alert(error.message || 'Error creating course');
        });
    });

    // Create the initial section when the page loads
    createSection();
});

// Quiz functionality
window.addQuizQuestion = function(element) {
    const quizQuestions = element.tagName === 'BUTTON' 
        ? element.previousElementSibling 
        : element;
    const sectionIndex = quizQuestions.dataset.sectionIndex;
    const blockIndex = quizQuestions.dataset.blockIndex;
    const questionIndex = quizQuestions.children.length;

    const questionHTML = `
        <div class="quiz-question bg-gray-50 p-4 rounded-lg mb-4">
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-sm font-medium text-gray-900">Question ${questionIndex + 1}</h4>
                <button type="button" onclick="removeQuizQuestion(this)" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            
            <input type="text" 
                   name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][text]" 
                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                   placeholder="Enter question">
            
            <div class="mt-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                    <button type="button" 
                            onclick="addQuizOption(this, ${sectionIndex}, ${blockIndex}, ${questionIndex})"
                            class="text-sm text-orange-600 hover:text-orange-700">
                        + Add Option
                    </button>
                </div>
                <div class="quiz-options space-y-2">
                    <!-- Initial option -->
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][options][]" 
                               class="flex-1 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                               placeholder="Enter answer option">
                        <input type="radio" 
                               name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][correct_answer]" 
                               value="0"
                               class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300"
                               required>
                        <button type="button" onclick="removeQuizOption(this)" class="text-gray-400 hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = questionHTML;
    quizQuestions.appendChild(tempDiv.firstElementChild);
};

window.removeQuizQuestion = function(button) {
    const questionElement = button.closest('.quiz-question');
    const quizQuestions = questionElement.parentElement;
    
    if (quizQuestions.children.length > 1) {
        questionElement.remove();
        updateQuizQuestionNumbers(quizQuestions);
    }
};

window.addQuizOption = function(button, sectionIndex, blockIndex, questionIndex) {
    const optionsContainer = button.closest('.quiz-question').querySelector('.quiz-options');
    const optionIndex = optionsContainer.children.length;
    
    const optionHTML = `
        <div class="flex items-center space-x-2">
            <input type="text" 
                   name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][options][]" 
                   class="flex-1 rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                   placeholder="Enter answer option">
            <input type="radio" 
                   name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][correct_answer]" 
                   value="${optionIndex}"
                   class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300"
                   required>
            <button type="button" onclick="removeQuizOption(this)" class="text-gray-400 hover:text-red-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = optionHTML;
    optionsContainer.appendChild(tempDiv.firstElementChild);
};

window.removeQuizOption = function(button) {
    const optionElement = button.closest('.flex');
    const optionsContainer = optionElement.parentElement;
    
    if (optionsContainer.children.length > 1) {
        optionElement.remove();
        updateQuizOptionValues(optionsContainer);
    }
};

function updateQuizQuestionNumbers(quizQuestions) {
    const questions = quizQuestions.querySelectorAll('.quiz-question');
    questions.forEach((question, index) => {
        // Update question number
        const title = question.querySelector('h4');
        title.textContent = `Question ${index + 1}`;

        // Update field names
        const questionInput = question.querySelector('input[type="text"]');
        const optionsContainer = question.querySelector('.quiz-options');
        const sectionIndex = quizQuestions.dataset.sectionIndex;
        const blockIndex = quizQuestions.dataset.blockIndex;

        questionInput.name = `sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${index}][text]`;

        // Update options
        const options = optionsContainer.querySelectorAll('.flex');
        options.forEach((option, optionIndex) => {
            const optionInput = option.querySelector('input[type="text"]');
            const radioInput = option.querySelector('input[type="radio"]');

            optionInput.name = `sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${index}][options][]`;
            radioInput.name = `sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${index}][correct_answer]`;
            radioInput.value = optionIndex;
        });
    });
}

function updateQuizOptionValues(optionsContainer) {
    const options = optionsContainer.querySelectorAll('.flex');
    const questionElement = optionsContainer.closest('.quiz-question');
    const sectionIndex = questionElement.closest('.quiz-questions').dataset.sectionIndex;
    const blockIndex = questionElement.closest('.quiz-questions').dataset.blockIndex;
    const questionIndex = Array.from(questionElement.parentElement.children).indexOf(questionElement);

    options.forEach((option, index) => {
        const optionInput = option.querySelector('input[type="text"]');
        const radioInput = option.querySelector('input[type="radio"]');

        optionInput.name = `sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][options][]`;
        radioInput.name = `sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][${questionIndex}][correct_answer]`;
        radioInput.value = index;
    });
}
