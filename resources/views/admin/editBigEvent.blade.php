@extends('layouts.adminNew')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="{{ asset('asset/new_admin/css/main_style.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary border-0">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="padding-block: 10px;">
                                <h3 class="card-title mb-0 fs-5">EDIT EVENT</h3>


                                <div class="d-flex gap-2">
                                    <button type="button" class="--btn btn-publish"
                                        onclick="submitArticleForm('pub')">Publish</button>
                                    {{-- <button type="button" class="--btn btn-save" onclick="submitArticleForm('du')">Save as
                                        Draft</button> --}}
                                </div>
                            </div>


                            <form id="articleForm" class="form-group" method="post"
                                action="{{ asset('events/edit/' . $data['event']->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">

                                    <div class="form-group row ">
                                        <div class="input-field">
                                            <input class="at-title" placeholder="" autocomplete="off" type="text"
                                                name="name" id="name" oninput="clearError('name-error')"
                                                value="{{ $data['event']->title }}" />
                                            <label for="name">Event Title <span class="text-danger">*</span></label>
                                            @error('name')
                                                <div class="input-group-append" id="name-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('name') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row ">
                                        <div class="input-field col-md-6">
                                            <input placeholder="" autocomplete="off" type="text" name="eng_name"
                                                id="eng_name" oninput="clearError('eng_name-error', 'site_url-error')"
                                                value="{{ $data['event']->eng_name }}" />
                                            <label for="eng_name">Event URL <span class="text-danger">*</span></label>
                                            @error('eng_name')
                                                <div class="input-group-append" id="eng_name-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('eng_name') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                            @error('site_url') --}}
                                    {{-- <div class="text-danger">{{ $message }}</div> --}}
                                    {{-- <div class="input-group-append" id="site_url-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $message }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="input-field col-md-6">
                                            <input placeholder="" autocomplete="off" type="text" name="tags"
                                                id="tags" oninput="clearError('tags-error')"
                                                value="{{ $data['event']->tag }}" />
                                            <label for="tags">Tag</label>
                                            @error('tags')
                                                <div class="input-group-append" id="tags-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('tags') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div> --}}


                                    {{-- <div class="form-group row">
                                        <div class="col-md-6 mb-2">
                                            <label class="customLable" for="authName">Author name <span
                                                    class="text-danger">*</span></label>
                                            <select class="js-example-basic-single form-select" data-width="100%"
                                                name="author" oninput="clearError('author-error')">
                                                <option value="">Select Author Name <span class="text-danger">*</span>
                                                </option>
                                                @foreach ($data['authors'] as $author)
                                                    <option value="{{ $author->id }}"
                                                        {{ old('author', $data['event']->author_id) == $author->id ? 'selected' : '' }}>
                                                        {{ $author->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('author')
                                                <div class="input-group-append" id="author-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('author') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="customLable" for="primicat">Prime category <span
                                                    class="text-danger">*</span></label>
                                            <select class="js-example-basic-single form-select" data-width="100%"
                                                name="category" oninput="clearError('category-error')">
                                                <option value="">Select Prime Category <span
                                                        class="text-danger">*</span></option>
                                                @foreach ($data['categories'] as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category', $data['event']->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <div class="input-group-append" id="category-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('category') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
 --}}

                                    {{-- <div class="form-group row">
                                        <div class="input-field">
                                            <textarea autocomplete="off" name="sort_desc" placeholder=""
                                                id="sort_desc" oninput="clearError('sort_desc-error')" class="input-textarea">{{ $data['event']->short_desc }}</textarea>
                                            <label for="sort_desc">Brief <span class="text-danger">*</span></label>
                                            @error('sort_desc')
                                                <div class="input-group-append" id="sort_desc-error">
                                                    <div class="input-group-text">
                                                        <span class="me-1"><i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $errors->first('sort_desc') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div> --}}

                                    {{-- --------------------------------------- editor container---------------------- --}}
                                    {{-- <div class="Editor-block row mb-4" style="padding: 10px">
                                        <label for="exampleFormControlSelect2"
                                            style="font-size: 17px; color: #757575; margin-bottom: 8px">Event
                                            Description</label>
                                        <textarea id="default" name="description">{{ old('description', $data['event']->description) }}</textarea>
                                    </div> --}}

                                    <div class="form-group row">
                                        <div class="input-field col-md-12">
                                            <input placeholder="" autocomplete="off" type="url" name="video_url"
                                                id="video_url"
                                                value="{{ old('video_url', $data['event']->video_url ?? '') }}" />
                                            <label for="video_url">Video Link</label>

                                            @error('video_url')
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="me-1">
                                                            <i class="fa-solid fa-circle-exclamation"></i>
                                                            {{ $message }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- --------------------------------------- Uploads container---------------------- --}}
                                    <div class="uploads-container row">
                                        {{-- <div class="uploads col-md-5" id="image-upload-section">
                                            <div class="uploads-box">
                                                <span class="-title">Upload Thumb Image</span>
                                                <p class="-paragraph px-5">
                                                <ul class="-paragraph-content text-start" style="color: #ff3131;">
                                                    <li>Only .jpeg, .jpg, and .png files are allowed*</li>
                                                    <li class="text-left">The image size must not exceed 200 KB.</li>
                                                    <li class="text-left">Image dimension should be (800x450) px.</li>
                                                </ul>
                                                </p>

                                                @if (isset($data['event']->event_image))
                                                    @php
                                                        $thumbnailName = basename($data['event']->event_image);
                                                    @endphp
                                                    <p>Saved Image: <strong>{{ $thumbnailName }}</strong></p>
                                                @endif
                                                <label for="file-input" class="drop-container">
                                                    <input type="file" accept=".jpeg,.jpg,.png" name="file"
                                                        id="file-input">
                                                </label>
                                                @error('file')
                                                    <div class="input-group-append" id="file-error">
                                                        <div class="input-group-text text-danger">
                                                            {{ $message }}
                                                            <!-- Error message will display here (e.g., "The thumb image size must not exceed 200 KB.") -->
                                                        </div>
                                                    </div>
                                                @enderror
                                                </>
                                            </div>
                                        </div> --}}
                                        <div class="uploads col-md-5" id="video-upload-section">
                                            <div class="uploads-box">
                                                <h3 class="-title">Upload Video</h3>
                                                <p class="-paragraph px-5">
                                                <ul class="-paragraph-content text-start" style="color: #ff3131d9;">
                                                    <li>Only .mp4 video files are allowed*</li>
                                                    <li class="text-left">File size must not exceed 200MB.</li>
                                                </ul>
                                                </p>

                                                @if (isset($data['event']->video_path))
                                                    @php
                                                        $videoName = basename($data['event']->video_path);
                                                    @endphp
                                                    <p>Saved Video: <strong>{{ $videoName }}</strong></p>
                                                @endif
                                                <label for="file-input-video" class="drop-container">
                                                    <span class="drop-title">Drop files here</span>
                                                    or
                                                    <input type="file" accept=".mp4" id="file-input-video"
                                                        name="video_file" oninput="clearError('video_file-error')">
                                                </label>

                                                @error('video_file')
                                                    <div class="input-group-append" id="video_file-error">
                                                        <div class="input-group-text text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="uploads col-md-5" id="video-thumb-upload-section">
                                            <div class="uploads-box">
                                                <span class="-title">Video Thumb Image</span>
                                                <p class="-paragraph px-5">
                                                <ul class="-paragraph-content text-start" style="color: #ff3131;">
                                                    <li>Only .jpeg, .jpg, and .png files are allowed*</li>
                                                    <li class="text-left">The image size must not exceed 200 KB.</li>
                                                    <li class="text-left">Image dimension should be (800x450) px.</li>
                                                </ul>
                                                </p>

                                                @if (isset($data['event']->video_thumb))
                                                    @php
                                                        $thumbnailName = basename($data['event']->video_thumb);
                                                    @endphp
                                                    <p>Saved Video Thumbnail: <strong>{{ $thumbnailName }}</strong></p>
                                                @endif
                                                <label for="video-thumb-file-input" class="drop-container">
                                                    <input type="file" accept=".jpeg,.jpg,.png"
                                                        name="video-thumb-file" id="video-thumb-file-input">
                                                </label>
                                                @error('video-thumb-file')
                                                    <div class="input-group-append" id="video-thumb-file-error">
                                                        <div class="input-group-text text-danger">
                                                            {{ $message }}
                                                            <!-- Error message will display here (e.g., "The thumb image size must not exceed 200 KB.") -->
                                                        </div>
                                                    </div>
                                                @enderror
                                                </>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uploads-container row">
                                        <div class="uploads col-md-5" id="Bg-image-upload-section">
                                            <div class="uploads-box">
                                                <span class="-title">Background Image</span>
                                                <p class="-paragraph px-5">
                                                <ul class="-paragraph-content text-start" style="color: #ff3131;">
                                                    <li>Only .jpeg, .jpg, and .png files are allowed*</li>
                                                    <li class="text-left">The image size must not exceed 200 KB.</li>
                                                    <li class="text-left">Image dimension should be (1500x515) px.</li>
                                                </ul>
                                                </p>

                                                @if (isset($data['event']->background_image))
                                                    @php
                                                        $thumbnailName = basename($data['event']->background_image);
                                                    @endphp
                                                    <p>Saved Bg Image: <strong>{{ $thumbnailName }}</strong></p>
                                                @endif
                                                <label for="bg-file-input" class="drop-container">
                                                    <input type="file" accept=".jpeg,.jpg,.png" name="bg-file"
                                                        id="bg-file-input">
                                                </label>
                                                @error('bg-file')
                                                    <div class="input-group-append" id="bg-file-error">
                                                        <div class="input-group-text text-danger">
                                                            {{ $message }}
                                                            <!-- Error message will display here (e.g., "The thumb image size must not exceed 200 KB.") -->
                                                        </div>
                                                    </div>
                                                @enderror
                                                </>
                                            </div>
                                        </div>
                                        <div class="uploads col-md-5" id="banner-image-upload-section">
                                            <div class="uploads-box">
                                                <span class="-title">Banner Image</span>
                                                <p class="-paragraph px-5">
                                                <ul class="-paragraph-content text-start" style="color: #ff3131;">
                                                    <li>Only .jpeg, .jpg, and .png files are allowed*</li>
                                                    <li class="text-left">The image size must not exceed 200 KB.</li>
                                                    <li class="text-left">Image dimension should be (1500x100) px.</li>
                                                </ul>
                                                </p>

                                                @if (isset($data['event']->banner_image))
                                                    @php
                                                        $thumbnailName = basename($data['event']->banner_image);
                                                    @endphp
                                                    <p>Saved Banner: <strong>{{ $thumbnailName }}</strong></p>
                                                @endif
                                                <label for="banner-file-input" class="drop-container">
                                                    <input type="file" accept=".jpeg,.jpg,.png" name="banner-file"
                                                        id="banner-file-input">
                                                </label>
                                                @error('banner-file')
                                                    <div class="input-group-append" id="banner-file-error">
                                                        <div class="input-group-text text-danger">
                                                            {{ $message }}
                                                            <!-- Error message will display here (e.g., "The thumb image size must not exceed 200 KB.") -->
                                                        </div>
                                                    </div>
                                                @enderror
                                                </>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Action button --}}
                                    <input type="hidden" name="from" value="{{ request()->get('from') }}">
                                    <div class="button-container row">
                                        <button class="--btn btn-publish" name="publish" value="pub">Publish</button>
                                        {{-- <button class="--btn btn-save" name="draft" value="du">Save as
                                            Draft</button> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-body -->

                </div>
            </div>
            <!-- /.card -->
        </section>
    </div>
    <!-- Include jQuery, Popper.js, and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>


    @push('custom-scripts')
        <script src="{{ asset('asset/new_admin/tinymce/tinymce.min.js') }}"></script>
        <script>
            tinymce.init({
                selector: 'textarea#default',
                license_key: 'gpl',
                width: 1000,
                height: 300,
                plugins: [
                    'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                    'searchreplace', 'wordcount', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media',
                    'table', 'emoticons', 'template', 'codesample'
                ],
                toolbar: 'customtextbox insertfacebookvideo | undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify |' +
                    'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
                    'forecolor backcolor emoticons',
                setup: function(editor) {
                    editor.ui.registry.addButton('customtextbox', {
                        // text: ' Box Text',
                        text: 'bayan',
                        tooltip: 'Insert custom boxed text',
                        onAction: function() {
                            const userInput = prompt("Enter text to display as a box:");
                            if (userInput) {
                                const encodedText = editor.dom.encode(userInput);
                                const boxedHTML =
                                    `<span class="custom-box">${encodedText}</span>&nbsp;`;
                                editor.insertContent(boxedHTML);
                            }
                        }
                    });
                    // 🔹 New button: Insert Facebook Video
                    editor.ui.registry.addButton('insertfacebookvideo', {
                        text: 'FB Video',
                        tooltip: 'Insert Facebook Video',
                        onAction: function() {
                            const fbUrl = prompt("Paste Facebook Video URL:");
                            if (fbUrl) {
                                const embedHTML = `
                            <div class="fb-video" 
                                 data-href="${fbUrl}" 
                                 data-width="500" 
                                 data-show-text="false">
                            </div>
                        `;
                                editor.insertContent(embedHTML);
                            }
                        }
                    });
                },

                extended_valid_elements: 'span[class|style]',
                menu: {
                    favs: {
                        title: 'menu',
                        items: 'code visualaid | searchreplace | emoticons'
                    }
                },
                menubar: 'favs file edit view insert format tools table',
                content_style: 'body{font-family:Helvetica,Arial,sans-serif; font-size:16px}',
                content_css: '{{ asset('asset/new_admin/css/main_style.css') }}',

                /*Image Upload Settings */
                automatic_uploads: false,
                images_upload_handler: null,
                /*NL1030:Image not loading in app */
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,



                // Used in General tab
                file_picker_types: 'image',
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype === 'image') {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = function() {
                            var file = this.files[0];

                            // Client-side size validation (200 KB = 204800 bytes)
                            if (file.size > 204800) {
                                alert("The image size must not exceed 200 KB.");
                                return;
                            }

                            var formData = new FormData();
                            formData.append('file', file);
                            formData.append('_token', '{{ csrf_token() }}');

                            fetch('{{ url('/files/upload') }}', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        callback(data.location);
                                    } else {
                                        alert('Upload failed: ' + data.message);
                                    }
                                })
                                .catch(() => {
                                    alert('An error occurred while uploading the image.');
                                });
                        };
                        input.click();
                    }
                }
            });
        </script>

        <script>
            function clearError(...ids) {
                ids.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.textContent = ''; // Clear the error message
                    }
                });
            }
        </script>


        <script>
            function submitArticleForm(type) {
                const form = document.getElementById('articleForm');

                // Remove previous hidden inputs if any
                ['publish', 'draft'].forEach(name => {
                    const el = form.querySelector(`input[name="${name}"]`);
                    if (el) el.remove();
                });

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';

                if (type === 'du') {
                    hiddenInput.name = 'draft';
                    hiddenInput.value = 'du';
                } else {
                    hiddenInput.name = 'publish';
                    hiddenInput.value = 'pub';
                }

                form.appendChild(hiddenInput);
                form.submit();
            }
        </script>
    @endpush
@endsection
