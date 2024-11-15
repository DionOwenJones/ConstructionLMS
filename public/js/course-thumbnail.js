document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const imageInput = document.getElementById('course-image');
    const imagePreview = document.getElementById('image-preview');

    // Handle click to select file
    dropZone.addEventListener('click', function() {
        imageInput.click();
    });

    // Handle file selection
    imageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            updatePreview(file);
        }
    });

    // Handle drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-orange-500');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-orange-500');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-orange-500');

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const file = e.dataTransfer.files[0];
            imageInput.files = e.dataTransfer.files; // Update the input's files
            updatePreview(file);
        }
    });

    function updatePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `
                <div class="relative">
                    <img src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                    <button type="button" onclick="removeImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});

// Global function to remove image
window.removeImage = function() {
    const imageInput = document.getElementById('course-image');
    const imagePreview = document.getElementById('image-preview');

    imageInput.value = ''; // Clear the file input
    imagePreview.innerHTML = `
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="mt-1 text-sm text-gray-600">Drag and drop an image here, or click to select</p>
        <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
    `;
};
