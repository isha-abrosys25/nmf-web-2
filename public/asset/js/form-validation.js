
window.VideoFormUtils = {
    //Video Add Article Form Validation
    validateUploadForm: function () {
        let isValid = true;
        const fieldRef = {
            first: null
        }; // Object reference to pass

        const title = document.getElementById("title").value.trim();
        const titleUrl = document.getElementById("title_url").value.trim();
        const category = document.querySelector('select[name="category"]').value;
        const author = document.querySelector('select[name="author"]').value;
        const description = document.getElementById("description").value.trim();
        const imageInput = document.getElementById("image_file");
        const videoInput = document.getElementById("video_file");
        const imageFile = imageInput.files[0];
        const videoFile = videoInput.files[0];


        const errorFields = ['title', 'title_url', 'category', 'author', 'description', 'image_file', 'video_file'];
        errorFields.forEach(field => this.clearError(`${field}-error`));

        if (!title) {
            this.showError('title-error', 'Title is required.', fieldRef);
            isValid = false;
        }

        if (!titleUrl) {
            this.showError('title_url-error', 'Title URL is required.', fieldRef);
            isValid = false;
        }

        if (!category || category === "0") {
            this.showError('category-error', 'Please select a category.', fieldRef);
            isValid = false;
        }

        if (!author || author === "0") {
            this.showError('author-error', 'Please select an author.', fieldRef);
            isValid = false;
        }

        if (!description) {
            this.showError('description-error', 'Description is required.', fieldRef);
            isValid = false;
        }

        if (!imageFile) {
            this.showError('image_file-error', 'Thumbnail image is required.', fieldRef);
            isValid = false;
        } else {
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedImageTypes.includes(imageFile.type)) {
                this.showError('image_file-error', 'Only .jpeg, .jpg, .png, .webp files are allowed.', fieldRef);
                isValid = false;
            } else if (imageFile.size > 200 * 1024) {
                this.showError('image_file-error', 'Image must not exceed 200 KB.', fieldRef);
                isValid = false;
            }
        }

        if (!videoFile) {
            this.showError('video_file-error', 'Video file is required.', fieldRef);
            isValid = false;
        } else {
            if (videoFile.type !== 'video/mp4') {
                this.showError('video_file-error', 'Only .mp4 files are allowed.', fieldRef);
                isValid = false;
            } else if (videoFile.size > 300 * 1024 * 1024) {
                this.showError('video_file-error', 'Video must not exceed 300 MB.', fieldRef);
                isValid = false;
            }
        }

        if (fieldRef.first) {
            fieldRef.first.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        return isValid;
    },

    showError: function (errorId, message, fieldRef) {
        const baseId = errorId.replace('-error', '');
        const field = document.getElementById(baseId);

        if (field) {
            field.classList.add('is-invalid');

            // If first invalid field hasn't been set, assign it
            if (!fieldRef.first) {
                fieldRef.first = field;
            }
        }

        let errorElement = document.getElementById(errorId);
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = errorId;
            errorElement.className = 'input-group-append';
            errorElement.innerHTML = `<div class="input-group-text text-danger">
            <i class="fa-solid fa-circle-exclamation me-1"></i>${message}</div>`;
            field?.parentNode?.appendChild(errorElement);
        } else {
            errorElement.style.display = 'block';
            errorElement.innerHTML = `<div class="input-group-text text-danger">
            <i class="fa-solid fa-circle-exclamation me-1"></i>${message}</div>`;
        }
    },

    clearError: function (errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.style.display = 'none';
        }

        const input = document.getElementById(errorId.replace('-error', ''));
        if (input) {
            input.classList.remove('is-invalid');
        }
    },

    validateVideoEditForm : function() {
            let isValid = true;
            const fieldRef = {
                first: null
            }; // Object reference to pass

            const title = document.getElementById("title").value.trim();
            const titleUrl = document.getElementById("title_url").value.trim();
            const category = document.querySelector('select[name="category"]').value;
            const author = document.querySelector('select[name="author"]').value;
            const description = document.getElementById("description").value.trim();
            const imageInput = document.getElementById("image_file");
            const videoInput = document.getElementById("video_file");
            const imageFile = imageInput.files[0];
            const videoFile = videoInput.files[0];

            const existingImage = window.existingThumb === true || window.existingThumb === 'true';
            const existingVideo = window.existingVideo === true || window.existingVideo === 'true';

            const errorFields = ['title', 'title_url', 'category', 'author', 'description', 'image_file', 'video_file'];
            errorFields.forEach(field => this.clearError(`${field}-error`));

            if (!title) {
                this.showError('title-error', 'Title is required.', fieldRef);
                isValid = false;
            }

            if (!titleUrl) {
                this.showError('title_url-error', 'Title URL is required.', fieldRef);
                isValid = false;
            }

            if (!category || category === "0") {
                this.showError('category-error', 'Please select a category.', fieldRef);
                isValid = false;
            }

            if (!author || author === "0") {
                this.showError('author-error', 'Please select an author.', fieldRef);
                isValid = false;
            }

            if (!description) {
                this.showError('description-error', 'Description is required.', fieldRef);
                isValid = false;
            }


            if (!imageFile && !existingImage) {
                this.showError('image_file-error', 'Thumbnail image is required.', fieldRef);
                isValid = false;
            }

            if (imageFile) {
                const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedImageTypes.includes(imageFile.type)) {
                    this.showError('image_file-error', 'Only .jpeg, .jpg, .png, .webp files are allowed.', fieldRef);
                    isValid = false;
                } else if (imageFile.size > 200 * 1024) {
                    this.showError('image_file-error', 'Image must not exceed 200 KB.', fieldRef);
                    isValid = false;
                }
            }


            if (!videoFile && !existingVideo) {
                this.showError('video_file-error', 'Video file is required.', fieldRef);
                isValid = false;
            }

            if (videoFile) {
                if (videoFile.type !== 'video/mp4') {
                    this.showError('video_file-error', 'Only .mp4 files are allowed.', fieldRef);
                    isValid = false;
                } else if (videoFile.size > 300 * 1024 * 1024) {
                    this.showError('video_file-error', 'Video must not exceed 300 MB.', fieldRef);
                    isValid = false;
                }
            }

            if (fieldRef.first) {
                fieldRef.first.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            return isValid;
        }
};