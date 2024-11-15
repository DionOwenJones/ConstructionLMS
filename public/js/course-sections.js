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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
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

        // Generate content based on type
        switch(type) {
            case 'text':
                contentArea.innerHTML = `
                    <div class="space-y-4">
                        <input type="hidden" name="sections[${sectionIndex}][type]" value="text">
                        <textarea name="sections[${sectionIndex}][content]" rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Enter your content here..."></textarea>
                    </div>
                `;
                break;

            case 'image':
                contentArea.innerHTML = `
                    <div class="space-y-4">
                        <input type="hidden" name="sections[${sectionIndex}][type]" value="image">
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <div id="image-preview-${sectionIndex}" class="mb-4"></div>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500">
                                        <span>Upload a file</span>
                                        <input type="file" name="sections[${sectionIndex}][image]" class="sr-only" accept="image/*" onchange="previewImage(this, ${sectionIndex})">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                    </div>
                `;
                break;

            case 'video':
                contentArea.innerHTML = `
                    <div class="space-y-4">
                        <input type="hidden" name="sections[${sectionIndex}][type]" value="video">
                        <input type="url" name="sections[${sectionIndex}][video_url]"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                               placeholder="Enter YouTube or Vimeo URL">
                        <p class="text-xs text-gray-500">Supported: YouTube and Vimeo URLs</p>
                    </div>
                `;
                break;

            case 'quiz':
                contentArea.innerHTML = `
                    <div class="space-y-4">
                        <input type="hidden" name="sections[${sectionIndex}][type]" value="quiz">
                        <div id="quiz-${sectionIndex}" class="space-y-4">
                            <div class="quiz-question bg-gray-50 p-4 rounded-lg">
                                <input type="text" name="sections[${sectionIndex}][quiz][questions][]"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 mb-4"
                                       placeholder="Enter your question">
                                <div class="answer-options space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" name="sections[${sectionIndex}][quiz][correct][]" value="0">
                                        <input type="text" name="sections[${sectionIndex}][quiz][answers][]"
                                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                               placeholder="Answer option">
                                    </div>
                                </div>
                                <button type="button" onclick="addAnswerOption(${sectionIndex})"
                                        class="mt-2 text-sm text-orange-600 hover:text-orange-700">
                                    + Add Answer Option
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addQuestion(${sectionIndex})"
                                class="text-sm text-orange-600 hover:text-orange-700">
                            + Add Another Question
                        </button>
                    </div>
                `;
                break;
        }
    }

    // Initialize the add section button
    if (addSectionButton) {
        addSectionButton.addEventListener('click', createSection);
    }
});

