@extends('layouts.app')

@section('title', 'Upload Presentation')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Upload Presentation</h1>
            <p class="mb-0 text-muted">Upload a new presentation file</p>
        </div>
        <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
            Back to Presentations
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Presentation Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presentations.store') }}" method="POST" enctype="multipart/form-data" id="presentationForm">
                        @csrf
                        
                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="file" class="form-label">Presentation File <span class="text-danger">*</span></label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                       id="file" name="file" accept=".ppt,.pptx,.pdf,.key" required>
                                <div class="file-upload-text">
                                    <h5>Drop your presentation here or click to browse</h5>
                                    <small class="text-muted">Supported formats: PPT, PPTX, PDF, KEY (Max: 50MB)</small>
                                </div>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="filePreview" class="mt-3" style="display: none;">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-file-powerpoint me-2"></i>
                                    <div>
                                        <strong id="fileName"></strong>
                                        <br><small id="fileSize" class="text-muted"></small>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" onclick="clearFile()"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" maxlength="1000">{{ old('description') }}</textarea>
                            <div class="form-text">Provide a brief description of your presentation content</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business</option>
                                <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="training" {{ old('category') == 'training' ? 'selected' : '' }}>Training</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags') }}" 
                                   placeholder="Enter tags separated by commas">
                            <div class="form-text">Add relevant tags to help organize and find your presentation</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sharing Options -->
                        <div class="mb-4">
                            <label class="form-label">Sharing Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" 
                                       {{ old('is_public') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    Make this presentation publicly viewable
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_template" name="is_template" value="1" 
                                       {{ old('is_template') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_template">
                                    Add to templates library
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload me-1"></i> Upload Presentation
                            </button>
                            <button type="submit" name="action" value="save_and_new" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> Upload & Add Another
                            </button>
                            <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Upload Guidelines -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Upload Guidelines</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Supported Formats</h6>
                    <ul class="list-unstyled mb-3">
                        <li>✓ PowerPoint (.ppt, .pptx)</li>
                        <li>✓ PDF (.pdf)</li>
                        <li>✓ Keynote (.key)</li>
                    </ul>

                    <h6 class="text-primary">File Requirements</h6>
                    <ul class="list-unstyled mb-3">
                        <li><i class="fas fa-info-circle text-info me-2"></i>Maximum file size: 50MB</li>
                        <li><i class="fas fa-info-circle text-info me-2"></i>Clear, descriptive title</li>
                        <li><i class="fas fa-info-circle text-info me-2"></i>Relevant category selection</li>
                    </ul>

                    <h6 class="text-primary">Best Practices</h6>
                    <ul class="list-unstyled mb-3">
                        <li><i class="fas fa-lightbulb text-warning me-2"></i>Use descriptive titles</li>
                        <li><i class="fas fa-lightbulb text-warning me-2"></i>Add relevant tags</li>
                        <li><i class="fas fa-lightbulb text-warning me-2"></i>Include detailed descriptions</li>
                        <li><i class="fas fa-lightbulb text-warning me-2"></i>Choose appropriate categories</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tip:</strong> Well-organized presentations with clear titles and descriptions are easier to find and share.
                    </div>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Recent Uploads</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentPresentations) && $recentPresentations->count() > 0)
                        @foreach($recentPresentations as $recent)
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $recent->thumbnail_url }}" class="rounded me-2" 
                                     width="30" height="20" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold">{{ Str::limit($recent->title, 25) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $recent->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No recent uploads</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.file-upload-area {
    position: relative;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.file-upload-area.dragover {
    border-color: #0d6efd;
    background-color: #e7f3ff;
}

.file-upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-upload-text {
    pointer-events: none;
}
</style>
@endpush

@push('scripts')
<script>
// File upload handling
const fileInput = document.getElementById('file');
const fileUploadArea = document.getElementById('fileUploadArea');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const titleInput = document.getElementById('title');

// Drag and drop functionality
fileUploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});

fileUploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
});

fileUploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
    }
});

// File input change
fileInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

// Handle file selection
function handleFileSelect(file) {
    // Validate file type
    const allowedTypes = ['.ppt', '.pptx', '.pdf', '.key'];
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(fileExtension)) {
        alert('Please select a valid presentation file (PPT, PPTX, PDF, or KEY)');
        clearFile();
        return;
    }
    
    // Validate file size (50MB)
    if (file.size > 50 * 1024 * 1024) {
        alert('File size must be less than 50MB');
        clearFile();
        return;
    }
    
    // Show file preview
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    filePreview.style.display = 'block';
    
    // Auto-fill title if empty
    if (!titleInput.value) {
        const nameWithoutExtension = file.name.replace(/\.[^/.]+$/, "");
        titleInput.value = nameWithoutExtension.replace(/[_-]/g, ' ');
    }
}

// Clear file selection
function clearFile() {
    fileInput.value = '';
    filePreview.style.display = 'none';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission handling
document.getElementById('presentationForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading...';
});

// Character counter for description
const descriptionTextarea = document.getElementById('description');
if (descriptionTextarea) {
    const maxLength = 1000;
    const counter = document.createElement('div');
    counter.className = 'form-text text-end';
    counter.style.marginTop = '0.25rem';
    descriptionTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - descriptionTextarea.value.length;
        counter.textContent = `${remaining} characters remaining`;
        counter.className = remaining < 100 ? 'form-text text-end text-warning' : 'form-text text-end';
    }
    
    descriptionTextarea.addEventListener('input', updateCounter);
    updateCounter();
}
</script>
@endpush