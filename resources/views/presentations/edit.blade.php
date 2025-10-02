@extends('layouts.app')

@section('title', 'Edit Presentation')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Presentation</h1>
            <p class="mb-0 text-muted">Update presentation details and settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('presentations.show', $presentation) }}" class="btn btn-outline-info">
                👁️ View
            </a>
            <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Presentations
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Presentation Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presentations.update', $presentation) }}" method="POST" enctype="multipart/form-data" id="presentationForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current File Info -->
                        <div class="mb-4">
                            <label class="form-label">Current File</label>
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-file-powerpoint me-2"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ $presentation->original_filename }}</strong>
                                    <br><small class="text-muted">{{ $presentation->formatted_file_size }} • Uploaded {{ $presentation->created_at->format('M d, Y') }}</small>
                                </div>
                                <a href="{{ route('presentations.download', $presentation) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Replace File (Optional) -->
                        <div class="mb-4">
                            <label for="file" class="form-label">Replace File (Optional)</label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                       id="file" name="file" accept=".ppt,.pptx,.pdf,.key">
                                <div class="file-upload-text">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1">Click to select or drag and drop a new presentation file</p>
                                    <small class="text-muted">Supported formats: PPT, PPTX, PDF, KEY (Max: 50MB)</small>
                                </div>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="filePreview" class="mt-3" style="display: none;">
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div>
                                        <strong>New file selected: <span id="fileName"></span></strong>
                                        <br><small id="fileSize" class="text-muted"></small>
                                        <br><small class="text-warning">This will replace the current file</small>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" onclick="clearFile()"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $presentation->title) }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" maxlength="1000">{{ old('description', $presentation->description) }}</textarea>
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
                                <option value="business" {{ old('category', $presentation->category) == 'business' ? 'selected' : '' }}>Business</option>
                                <option value="education" {{ old('category', $presentation->category) == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="marketing" {{ old('category', $presentation->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="training" {{ old('category', $presentation->category) == 'training' ? 'selected' : '' }}>Training</option>
                                <option value="other" {{ old('category', $presentation->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags', $presentation->tags) }}" 
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
                                <option value="active" {{ old('status', $presentation->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ old('status', $presentation->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ old('status', $presentation->status) == 'archived' ? 'selected' : '' }}>Archived</option>
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
                                       {{ old('is_public', $presentation->is_public) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    Make this presentation publicly viewable
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_template" name="is_template" value="1" 
                                       {{ old('is_template', $presentation->is_template) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_template">
                                    Add to templates library
                                </label>
                            </div>
                        </div>

                        <!-- Version Control -->
                        @if($presentation->versions->count() > 0)
                            <div class="mb-4">
                                <label class="form-label">Version Control</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_version" name="create_version" value="1">
                                    <label class="form-check-label" for="create_version">
                                        Create new version (keep current version as backup)
                                    </label>
                                </div>
                                <small class="form-text text-muted">If unchecked, the current version will be updated</small>
                            </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Update Presentation
                            </button>
                            <a href="{{ route('presentations.show', $presentation) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Presentation Info -->
        <div class="col-lg-4">
            <!-- Current Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Presentation Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $presentation->views_count }}</h4>
                                <small class="text-muted">Views</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ $presentation->downloads_count }}</h4>
                            <small class="text-muted">Downloads</small>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Created:</span>
                        <span>{{ $presentation->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Last Updated:</span>
                        <span>{{ $presentation->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">File Size:</span>
                        <span>{{ $presentation->formatted_file_size }}</span>
                    </div>
                </div>
            </div>

            <!-- Version History -->
            @if($presentation->versions->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Version History</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>Current Version</strong>
                                    <br><small class="text-muted">{{ $presentation->updated_at->format('M d, Y g:i A') }}</small>
                                </div>
                                <span class="badge bg-primary">Active</span>
                            </div>
                            @foreach($presentation->versions->take(5) as $version)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span>Version {{ $version->version_number }}</span>
                                        <br><small class="text-muted">{{ $version->created_at->format('M d, Y g:i A') }}</small>
                                    </div>
                                    <a href="{{ $version->file_url }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('presentations.download', $presentation) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                        <a href="{{ route('presentations.duplicate', $presentation) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-copy me-1"></i> Duplicate
                        </a>
                        <a href="{{ route('presentations.analytics', $presentation) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> View Analytics
                        </a>
                        <hr>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleTemplate({{ $presentation->id }})">
                            <i class="fas fa-star me-1"></i> {{ $presentation->is_template ? 'Remove from Templates' : 'Make Template' }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleArchive({{ $presentation->id }})">
                            <i class="fas fa-archive me-1"></i> {{ $presentation->status === 'archived' ? 'Restore' : 'Archive' }}
                        </button>
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deletePresentation({{ $presentation->id }})">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this presentation? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All versions and analytics data will be permanently lost.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to permanently delete this presentation? This action cannot be undone.')">Delete Permanently</button>
                </form>
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
// File upload handling (same as create view)
const fileInput = document.getElementById('file');
const fileUploadArea = document.getElementById('fileUploadArea');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

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
    } else {
        clearFile();
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
});

// Delete presentation
function deletePresentation(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/presentations/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Toggle template status
function toggleTemplate(id) {
    fetch(`/presentations/${id}/toggle-template`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating template status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating template status');
    });
}

// Toggle archive status
function toggleArchive(id) {
    fetch(`/presentations/${id}/toggle-archive`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating archive status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating archive status');
    });
}

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