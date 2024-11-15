document.addEventListener('DOMContentLoaded', function() {
    const contentViewer = document.getElementById('content-viewer');
    const sectionTitle = document.getElementById('section-title');
    const sectionContent = document.getElementById('section-content');

    // Add click event listeners to all section items
    document.querySelectorAll('.section-item').forEach(section => {
        section.addEventListener('click', async function() {
            const sectionId = this.dataset.sectionId;

            try {
                const response = await fetch(`/api/sections/${sectionId}`);
                const data = await response.json();

                if (!response.ok) throw new Error('Failed to load section');

                // Update the content viewer
                sectionTitle.textContent = data.title;

                // Handle different content types
                if (typeof data.content === 'object') {
                    switch(data.content.type) {
                        case 'text':
                            sectionContent.innerHTML = `<div class="prose max-w-none">${data.content.text}</div>`;
                            break;
                        case 'image':
                            sectionContent.innerHTML = `
                                <div class="flex justify-center">
                                    <img src="/storage/${data.content.path}" alt="${data.title}" class="max-w-full rounded-lg">
                                </div>`;
                            break;
                        case 'video':
                            sectionContent.innerHTML = `
                                <div class="aspect-video">
                                    <iframe src="${data.content.url}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                </div>`;
                            break;
                    }
                } else {
                    sectionContent.innerHTML = `<div class="prose max-w-none">${data.content}</div>`;
                }

                // Update active section styling
                document.querySelectorAll('.section-item div').forEach(item => {
                    item.classList.remove('bg-orange-50', 'border-orange-200');
                    item.classList.add('border-gray-200');
                });

                this.querySelector('div').classList.add('bg-orange-50', 'border-orange-200');
                this.querySelector('div').classList.remove('border-gray-200');

            } catch (error) {
                console.error('Error:', error);
                sectionContent.innerHTML = `
                    <div class="flex items-center justify-center h-64 text-red-500">
                        <p>Error loading section content. Please try again.</p>
                    </div>`;
            }
        });
    });
});
