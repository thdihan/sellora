@extends('layouts.app')

@section('title', 'Edit Visit')

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
.current-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border-left: 4px solid #007bff;
    margin-bottom: 1rem;
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}
.status-scheduled { background-color: #e3f2fd; color: #1976d2; }
.status-in_progress { background-color: #fff3e0; color: #f57c00; }
.status-completed { background-color: #e8f5e8; color: #388e3c; }
.status-cancelled { background-color: #ffebee; color: #d32f2f; }
.status-rescheduled { background-color: #f3e5f5; color: #7b1fa2; }
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
.change-highlight {
    background-color: #fff3cd;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    border: 1px solid #ffeaa7;
}
.btn-action {
    min-width: 120px;
}
.reschedule-reason {
    background: #fff3e0;
    border: 1px solid #ffcc02;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Visit</h1>
            <p class="text-muted">Modify visit details and reschedule if needed</p>
        </div>
        <div>
            <a href="{{ route('visits.show', $visit) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
                Back to List
            </a>
        </div>
    </div>

    <!-- Current Visit Info -->
    <div class="current-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">{{ $visit->customer_name }}</h5>
                <p class="mb-1">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <strong>Current Schedule:</strong> {{ $visit->scheduled_at->format('l, F j, Y \a\t g:i A') }}
                </p>
                <p class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $visit->customer_address }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="status-badge status-{{ $visit->status }}">
                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                </span>
                <span class="priority-badge priority-{{ $visit->priority }}">
                    {{ ucfirst($visit->priority) }}
                </span>
                <div class="mt-2">
                    <small class="text-muted">{{ ucfirst($visit->visit_type) }} Visit</small>
                </div>
            </div>
        </div>
    </div>

    @if($visit->status === 'scheduled' || $visit->status === 'rescheduled')
        <div class="reschedule-reason">
            <h6><i class="fas fa-info-circle me-2"></i>Rescheduling Information</h6>
            <p class="mb-0">You can reschedule this visit by changing the date/time below. The original schedule will be saved for reference.</p>
        </div>
    @endif

    <form action="{{ route('visits.update', $visit) }}" method="POST" enctype="multipart/form-data" id="visitForm">
        @csrf
        @method('PUT')
        
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
                               id="customer_name" name="customer_name" 
                               value="{{ old('customer_name', $visit->customer_name) }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" 
                               value="{{ old('customer_phone', $visit->customer_phone) }}">
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
                               id="customer_email" name="customer_email" 
                               value="{{ old('customer_email', $visit->customer_email) }}">
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
                                  id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address', $visit->customer_address) }}</textarea>
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
                            <option value="sales" {{ old('visit_type', $visit->visit_type) == 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="support" {{ old('visit_type', $visit->visit_type) == 'support' ? 'selected' : '' }}>Support</option>
                            <option value="delivery" {{ old('visit_type', $visit->visit_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="maintenance" {{ old('visit_type', $visit->visit_type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="consultation" {{ old('visit_type', $visit->visit_type) == 'consultation' ? 'selected' : '' }}>Consultation</option>
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
                            <option value="low" {{ old('priority', $visit->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $visit->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $visit->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $visit->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <span id="priorityBadge" class="priority-badge priority-{{ $visit->priority }}">{{ ucfirst($visit->priority) }}</span>
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
                               value="{{ old('estimated_duration', $visit->estimated_duration) }}" 
                               min="0.5" max="24" step="0.5">
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
                               value="{{ old('scheduled_at', $visit->scheduled_at->format('Y-m-d\TH:i')) }}" 
                               required onchange="highlightChange(this, '{{ $visit->scheduled_at->format('Y-m-d\TH:i') }}')">
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Original: {{ $visit->scheduled_at->format('M j, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose of Visit</label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                  id="purpose" name="purpose" rows="3" 
                                  placeholder="Describe the purpose and objectives of this visit">{{ old('purpose', $visit->purpose) }}</textarea>
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
                                  placeholder="If different from customer address, specify the exact location">{{ old('location_address', $visit->location_address) }}</textarea>
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
                                  placeholder="Any additional notes or special instructions">{{ old('notes', $visit->notes) }}</textarea>
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
                                   {{ old('requires_follow_up', $visit->requires_follow_up) ? 'checked' : '' }}
                                   onchange="toggleFollowUpDate()">
                            <label class="form-check-label" for="requires_follow_up">
                                This visit requires follow-up
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3" id="followUpDateGroup" style="{{ $visit->requires_follow_up ? 'display: block;' : 'display: none;' }}">
                        <label for="follow_up_date" class="form-label">Follow-up Date</label>
                        <input type="datetime-local" class="form-control @error('follow_up_date') is-invalid @enderror" 
                               id="follow_up_date" name="follow_up_date" 
                               value="{{ old('follow_up_date', $visit->follow_up_date ? $visit->follow_up_date->format('Y-m-d\TH:i') : '') }}">
                        @error('follow_up_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @if($visit->status === 'scheduled' || $visit->status === 'rescheduled')
            <!-- Reschedule Reason -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-edit text-primary"></i> Reason for Changes
                </h5>
                
                <div class="mb-3">
                    <label for="reschedule_reason" class="form-label">Reason for Rescheduling/Changes</label>
                    <textarea class="form-control @error('reschedule_reason') is-invalid @enderror" 
                              id="reschedule_reason" name="reschedule_reason" rows="3" 
                              placeholder="Please provide a reason for the changes made to this visit">{{ old('reschedule_reason') }}</textarea>
                    @error('reschedule_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">This will be recorded in the visit history</div>
                </div>
            </div>
        @endif

        @if($visit->status === 'completed')
            <!-- Visit Outcome -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-flag-checkered text-primary"></i> Visit Outcome
                </h5>
                
                <div class="mb-3">
                    <label for="outcome" class="form-label">Visit Outcome</label>
                    <textarea class="form-control @error('outcome') is-invalid @enderror" 
                              id="outcome" name="outcome" rows="4" 
                              placeholder="Describe the outcome and results of this visit">{{ old('outcome', $visit->outcome) }}</textarea>
                    @error('outcome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Form Actions -->
        <div class="form-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if($visit->status === 'scheduled' || $visit->status === 'rescheduled')
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Changes to scheduled visits will update the visit and may trigger notifications.
                        </small>
                    @endif
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('visits.show', $visit) }}" class="btn btn-outline-secondary btn-action">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    
                    <button type="submit" class="btn btn-primary btn-action">
                        <i class="fas fa-save"></i> Update Visit
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
        if (scheduledDate && !document.getElementById('follow_up_date').value) {
            const followUpDate = new Date(scheduledDate);
            followUpDate.setDate(followUpDate.getDate() + 7);
            document.getElementById('follow_up_date').value = followUpDate.toISOString().slice(0, 16);
        }
    } else {
        dateGroup.style.display = 'none';
        document.getElementById('follow_up_date').value = '';
    }
}

// Highlight changes
function highlightChange(element, originalValue) {
    if (element.value !== originalValue) {
        element.classList.add('change-highlight');
    } else {
        element.classList.remove('change-highlight');
    }
}

// Form validation
document.getElementById('visitForm').addEventListener('submit', function(e) {
    const scheduledAt = document.getElementById('scheduled_at').value;
    const originalScheduledAt = '{{ $visit->scheduled_at->format('Y-m-d\TH:i') }}';
    
    // Check if rescheduling and reason is provided
    if (scheduledAt !== originalScheduledAt) {
        const rescheduleReason = document.getElementById('reschedule_reason');
        if (rescheduleReason && !rescheduleReason.value.trim()) {
            e.preventDefault();
            alert('Please provide a reason for rescheduling this visit.');
            rescheduleReason.focus();
            return false;
        }
    }
    
    // Confirm if making significant changes
    const now = new Date();
    const scheduledDate = new Date(scheduledAt);
    
    if (scheduledDate < now) {
        if (!confirm('The scheduled date is in the past. Are you sure you want to continue?')) {
            e.preventDefault();
            return false;
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePriorityBadge();
    
    // Set minimum date to today for rescheduling
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('scheduled_at').min = now.toISOString().slice(0, 16);
    
    // Add change listeners to highlight modifications
    const originalValues = {
        customer_name: '{{ $visit->customer_name }}',
        customer_phone: '{{ $visit->customer_phone }}',
        customer_email: '{{ $visit->customer_email }}',
        customer_address: `{{ $visit->customer_address }}`,
        visit_type: '{{ $visit->visit_type }}',
        priority: '{{ $visit->priority }}',
        estimated_duration: '{{ $visit->estimated_duration }}',
        purpose: `{{ $visit->purpose }}`,
        location_address: `{{ $visit->location_address }}`,
        notes: `{{ $visit->notes }}`
    };
    
    Object.keys(originalValues).forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('input', function() {
                highlightChange(this, originalValues[fieldName]);
            });
        }
    });
});
</script>
@endpush