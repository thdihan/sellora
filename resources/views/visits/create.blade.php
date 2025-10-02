@extends('layouts.app')

@section('title', 'Schedule New Visit')

@push('styles')
<style>
.form-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}
.section-title {
    color: #333;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}
.required {
    color: #dc3545;
}
.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}
.priority-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 0.5rem;
}
.priority-low { background-color: #d4edda; color: #155724; }
.priority-medium { background-color: #d1ecf1; color: #0c5460; }
.priority-high { background-color: #fff3cd; color: #856404; }
.priority-urgent { background-color: #f8d7da; color: #721c24; }
.file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}
.file-upload-area:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}
.file-upload-area.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}
.file-list {
    margin-top: 1rem;
}
.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}
.btn-action {
    min-width: 120px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Schedule New Visit</h1>
            <p class="text-muted">Create a new customer visit appointment</p>
        </div>
        <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Visits
        </a>
    </div>

    <form action="{{ route('visits.store') }}" method="POST" enctype="multipart/form-data" id="visitForm">
        @csrf
        
        <!-- Customer Information -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-user text-primary"></i> Customer Information
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">
                            Customer Name <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                               id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">
                            Customer Address <span class="required">*</span>
                        </label>
                        <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                  id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address') }}</textarea>
                        @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Details -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-calendar-check text-primary"></i> Visit Details
            </h5>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="visit_type" class="form-label">
                            Visit Type <span class="required">*</span>
                        </label>
                        <select class="form-select @error('visit_type') is-invalid @enderror" 
                                id="visit_type" name="visit_type" required>
                            <option value="">Select Visit Type</option>
                            <option value="sales" {{ old('visit_type') == 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="support" {{ old('visit_type') == 'support' ? 'selected' : '' }}>Support</option>
                            <option value="delivery" {{ old('visit_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="maintenance" {{ old('visit_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="consultation" {{ old('visit_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                        </select>
                        @error('visit_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" onchange="updatePriorityBadge()">
                            <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', 'medium') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <span id="priorityBadge" class="priority-badge priority-medium">Medium</span>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="estimated_duration" class="form-label">Estimated Duration (hours)</label>
                        <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                               id="estimated_duration" name="estimated_duration" 
                               value="{{ old('estimated_duration', 1) }}" min="0.5" max="24" step="0.5">
                        @error('estimated_duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="scheduled_at" class="form-label">
                            Scheduled Date & Time <span class="required">*</span>
                        </label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                               id="scheduled_at" name="scheduled_at" 
                               value="{{ old('scheduled_at', request('date') ? request('date') . 'T09:00' : '') }}" required>
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the date and time for the visit</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose of Visit</label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                  id="purpose" name="purpose" rows="3" 
                                  placeholder="Describe the purpose and objectives of this visit">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Additional Info -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-map-marker-alt text-primary"></i> Location & Additional Information
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="location_address" class="form-label">Specific Location Address</label>
                        <textarea class="form-control @error('location_address') is-invalid @enderror" 
                                  id="location_address" name="location_address" rows="3" 
                                  placeholder="If different from customer address, specify the exact location">{{ old('location_address') }}</textarea>
                        @error('location_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty to use customer address</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes or special instructions">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Follow-up Settings -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-clock text-primary"></i> Follow-up Settings
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requires_follow_up" 
                                   name="requires_follow_up" value="1" 
                                   {{ old('requires_follow_up') ? 'checked' : '' }}
                                   onchange="toggleFollowUpDate()">
                            <label class="form-check-label" for="requires_follow_up">
                                This visit requires follow-up
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3" id="followUpDateGroup" style="display: none;">
                        <label for="follow_up_date" class="form-label">Follow-up Date</label>
                        <input type="datetime-local" class="form-control @error('follow_up_date') is-invalid @enderror" 
                               id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}">
                        @error('follow_up_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- File Attachments -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-paperclip text-primary"></i> File Attachments
            </h5>
            
            <div class="file-upload-area" onclick="document.getElementById('attachments').click()">
                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0">Click to select files or drag and drop</p>
                <small class="text-muted">Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB each)</small>
                <input type="file" id="attachments" name="attachments[]" multiple 
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;" 
                       onchange="handleFileSelect(this.files)">
            </div>
            
            <div id="fileList" class="file-list"></div>
            
            @error('attachments')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
            @error('attachments.*')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="form-section">
            <div class="d-flex justify-content-between">
                <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-times"></i> Cancel
                </a>
                
                <div>
                    <button type="button" class="btn btn-outline-primary btn-action me-2" onclick="saveDraft()">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary btn-action">
                        <i class="fas fa-calendar-plus"></i> Schedule Visit
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Priority badge update
function updatePriorityBadge() {
    const select = document.getElementById('priority');
    const badge = document.getElementById('priorityBadge');
    const value = select.value;
    
    badge.className = `priority-badge priority-${value}`;
    badge.textContent = value.charAt(0).toUpperCase() + value.slice(1);
}

// Follow-up date toggle
function toggleFollowUpDate() {
    const checkbox = document.getElementById('requires_follow_up');
    const dateGroup = document.getElementById('followUpDateGroup');
    
    if (checkbox.checked) {
        dateGroup.style.display = 'block';
        // Set default follow-up date to 1 week after scheduled date
        const scheduledDate = document.getElementById('scheduled_at').value;
        if (scheduledDate) {
            const followUpDate = new Date(scheduledDate);
            followUpDate.setDate(followUpDate.getDate() + 7);
            document.getElementById('follow_up_date').value = followUpDate.toISOString().slice(0, 16);
        }
    } else {
        dateGroup.style.display = 'none';
        document.getElementById('follow_up_date').value = '';
    }
}

// File handling
let selectedFiles = [];

function handleFileSelect(files) {
    for (let file of files) {
        if (file.size > 10 * 1024 * 1024) { // 10MB limit
            alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
            continue;
        }
        
        if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    }
    
    updateFileList();
}

function updateFileList() {
    const fileList = document.getElementById('fileList');
    
    if (selectedFiles.length === 0) {
        fileList.innerHTML = '';
        return;
    }
    
    fileList.innerHTML = selectedFiles.map((file, index) => `
        <div class="file-item">
            <div>
                <i class="fas fa-file"></i>
                <span class="ms-2">${file.name}</span>
                <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFileList();
    
    // Update the file input
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    document.getElementById('attachments').files = dt.files;
}

// Drag and drop
const uploadArea = document.querySelector('.file-upload-area');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    handleFileSelect(e.dataTransfer.files);
});

// Save draft functionality
function saveDraft() {
    const formData = new FormData(document.getElementById('visitForm'));
    formData.append('_draft', '1');
    
    fetch('{{ route('visits.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Draft saved successfully!');
        } else {
            alert('Error saving draft: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving draft.');
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePriorityBadge();
    
    // Check if follow-up is already checked (for old input)
    if (document.getElementById('requires_follow_up').checked) {
        toggleFollowUpDate();
    }
    
    // Set minimum date to today
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('scheduled_at').min = now.toISOString().slice(0, 16);
});
</script>
@endpush