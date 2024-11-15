import Dropzone from 'dropzone';
Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {
    const thumbnailDropzone = new Dropzone("#thumbnail-dropzone", {
        url: "/admin/courses/upload-thumbnail",
        paramName: "thumbnail",
        maxFilesize: 5, // MB
        maxFiles: 1,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        init: function() {
            this.on("success", function(file, response) {
                document.getElementById('thumbnail-input').value = response.path;

                // Show preview
                const previewContainer = document.getElementById('preview-container');
                previewContainer.innerHTML = `
                    <div class="relative group">
                        <img src="${response.url}" class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                            <button type="button" class="text-white" onclick="removeImage(this)">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            });

            this.on("error", function(file, errorMessage) {
                console.error(errorMessage);
                this.removeFile(file);
            });
        }
    });
});

window.removeImage = function(button) {
    const previewContainer = document.getElementById('preview-container');
    previewContainer.innerHTML = '';
    document.getElementById('thumbnail-input').value = '';
};
