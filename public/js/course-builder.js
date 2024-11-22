/**
 * Course Builder JavaScript
 * This file handles the dynamic course creation interface, including section management,
 * content block creation, and form submission.
 */

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
                <button type="button" class="remove-block text-gray-400 hover:text-red-500">
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
                        <div class="quiz-questions">
                            <div class="quiz-question bg-gray-50 p-4 rounded-lg">
                                <input type="text" 
                                       name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][questions][]" 
                                       class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                                       placeholder="Enter question">
                                <div class="mt-4 space-y-2">
                                    <div class="quiz-option">
                                        <input type="text" 
                                               name="sections[${sectionIndex}][blocks][${blockIndex}][quiz_data][answers][]" 
                                               class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" 
                                               placeholder="Enter answer option">
                                    </div>
                                </div>
                                <button type="button" 
                                        class="add-option mt-2 text-sm text-orange-600 hover:text-orange-700">
                                    + Add Option
                                </button>
                            </div>
                        </div>
                        <button type="button" 
                                class="add-question text-sm text-orange-600 hover:text-orange-700">
                            + Add Question
                        </button>
                    </div>`;
                break;
        }

        block.innerHTML = blockContent;
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
            const inputs = block.querySelectorAll('input[name^="sections"], textarea[name^="sections"]');
            inputs.forEach(input => {
                const name = input.name;
                input.name = name.replace(/\[blocks\]\[\d+\]/, `[blocks][${index}]`);
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
