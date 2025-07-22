@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add New Ebook</h3>
                <ul class="breadcrumbs flex items-center gap10">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="#">
                            <div class="text-tiny">Ebooks</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Add Ebook</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <form id="ebook-form" class="form-new-product form-style-1" action="{{ route('admin.ebook.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf

                    <fieldset>
                        <label for="file-input" class="body-title">Upload EPUB File <span
                                class="tf-color-1">*</span></label>
                        <input id="file-input" type="file" name="file" accept=".epub" required>
                        @error('file')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                        <input type="hidden" id="cover-hidden" name="cover_image_data">
                    </fieldset>

                    <fieldset>
                        <label class="body-title">Cover Preview</label>
                        <div id="cover-preview" style="max-width: 200px; margin-top: 10px;">
                            <img id="cover-img" src="#" alt="Cover Image"
                                style="display:none; width: 100%; border-radius: 4px;" />
                        </div>
                    </fieldset>

                    <fieldset>
                        <label for="title" class="body-title">Title <span class="tf-color-1">*</span></label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}"
                            placeholder="Book title" required>
                        @error('title')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <label for="author_name" class="body-title">Author <span class="tf-color-1">*</span></label>
                        <input id="author_name" name="author_name" type="text" value="{{ old('author_name') }}"
                            placeholder="Author name" required>
                        @error('author_name')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <div class="flex items-center gap10 mb-5">
                            <label for="category" class="body-title">Category<span class="tf-color-1">*</span></label>
                            <button type="button" id="ai-category-btn" class="tf-button-small tf-button-outline"
                                style="padding: 4px 8px;">
                                <i class="icon-magic"></i> Suggest Category
                            </button>
                        </div>
                        <input id="category" name="category" type="text" value="{{ old('category') }}"
                            placeholder="e.g., Fantasy, Romance" required>
                        @error('category')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <div class="flex items-center gap10 mb-5">
                            <label for="description" class="body-title">Description</label>
                            <button type="button" id="ai-desc-btn" class="tf-button-small tf-button-outline"
                                style="padding: 4px 8px;">
                                <i class="icon-star"></i> AI Assist
                            </button>
                        </div>
                        <textarea id="description" name="description" placeholder="Book description">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <div class="justify-content-end">
                        <button class="tf-button w208" type="submit">Save Ebook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            // Elements
            const fileInput = document.getElementById('file-input');
            const titleInput = document.getElementById('title');
            const authorInput = document.getElementById('author_name');
            const descInput = document.getElementById('description');
            const categoryInput = document.getElementById('category');
            const coverImg = document.getElementById('cover-img');
            const coverHidden = document.getElementById('cover-hidden');
            const aiCategoryBtn = document.getElementById('ai-category-btn');
            const aiDescBtn = document.getElementById('ai-desc-btn');

            // EPUB File Handling
            fileInput.addEventListener('change', async () => {
                const file = fileInput.files[0];
                if (!file) return;

                try {
                    const book = await ePub(file);
                    const metadata = await book.loaded.metadata;

                    // Auto-fill fields
                    titleInput.value = metadata.title || '';
                    authorInput.value = metadata.creator || '';

                    // Auto-suggest category if we have title/author
                    if (titleInput.value && authorInput.value) {
                        await suggestCategory();
                    }

                    // Handle cover image
                    const coverUrl = await book.coverUrl();
                    if (coverUrl) {
                        coverImg.src = coverUrl;
                        coverImg.style.display = 'block';

                        // Convert cover to base64 for form submission
                        const response = await fetch(coverUrl);
                        const blob = await response.blob();
                        const reader = new FileReader();
                        reader.onloadend = () => {
                            coverHidden.value = reader.result;
                        };
                        reader.readAsDataURL(blob);
                    } else {
                        coverImg.style.display = 'none';
                        coverHidden.value = '';
                    }
                } catch (error) {
                    console.error("EPUB processing error:", error);
                    alert("Error processing EPUB file. Please check the file and try again.");
                }
            });

            // AI Category Suggestion
            aiCategoryBtn.addEventListener('click', suggestCategory);

            // AI Description Generation
            aiDescBtn.addEventListener('click', generateDescription);

            // Helper Functions
            async function suggestCategory() {
                if (!titleInput.value || !authorInput.value) {
                    alert("Please fill in title and author first!");
                    return;
                }

                try {
                    aiCategoryBtn.innerHTML = '<i class="icon-spinner spinner"></i> Thinking...';

                    const response = await fetch('/ai/suggest-category', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: titleInput.value,
                            author: authorInput.value
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`API error: ${response.status}`);
                    }

                    const data = await response.json();
                    categoryInput.value = data.category || 'Unknown';
                } catch (error) {
                    console.error("Category suggestion failed:", error);
                    alert("Failed to suggest category. Please try again.");
                } finally {
                    aiCategoryBtn.innerHTML = '<i class="icon-magic"></i> Suggest Category';
                }
            }

            async function generateDescription() {
                if (!titleInput.value || !authorInput.value) {
                    alert("Please fill in title and author first!");
                    return;
                }

                try {
                    aiDescBtn.innerHTML = '<i class="icon-spinner spinner"></i> Generating...';

                    const response = await fetch('/ai/generate-description', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: titleInput.value,
                            author: authorInput.value
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`API error: ${response.status}`);
                    }

                    const data = await response.json();
                    descInput.value = data.description || 'No description generated.';
                } catch (error) {
                    console.error("Description generation failed:", error);
                    alert("Failed to generate description. Please try again.");
                } finally {
                    aiDescBtn.innerHTML = '<i class="icon-star"></i> AI Assist';
                }
            }
        });
    </script>
    <style>
        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
