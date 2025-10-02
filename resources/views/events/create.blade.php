@extends('layouts.app')

@section('content')
<style>
    .form-section {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    .priority-badge {
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .priority-badge:hover {
        transform: scale(1.05);
    }
    .priority-badge.active {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }
    .color-picker {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #dee2e6;
        cursor: pointer;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    .color-picker:hover {
        transform: scale(1.1);
    }
    .color-picker.active {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
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
    .attendee-tag {
        background-color: #e9ecef;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        margin: 0.25rem;
        display: inline-block;
    }
    .recurring-options {
        display: none;
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    .time-fields {
        transition: all 0.3s ease;
    }
    .time-fields.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Event</h1>
            <p class="text-muted mb-0">Schedule a new event in your calendar</p>
        </div>
        <div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                ← Back to Events
            </a>
            <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary">
                📅 Calendar View
            </a>
        </div>
    </div>

    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">ℹ️ Basic Information</h5>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="event_type" class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_type') is-invalid @enderror" 
                                        id="event_type" name="event_type" required>
                                    <option value="">Select Type</option>
                                    <option value="meeting" {{ old('event_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="appointment" {{ old('event_type') == 'appointment' ? 'selected' : '' }}>Appointment</option>
                                    <option value="deadline" {{ old('event_type') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                                    <option value="reminder" {{ old('event_type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                                    <option value="personal" {{ old('event_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="holiday" {{ old('event_type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                                    <option value="other" {{ old('event_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('event_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Describe your event...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Date & Time -->
                <div class="form-section">
                    <h5 class="section-title">🕒 Date & Time</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', request('date')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', request('date')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" 
                                   value="1" {{ old('is_all_day') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_all_day">
                                All Day Event
                            </label>
                        </div>
                    </div>
                    
                    <div class="row time-fields" id="timeFields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Location & Details -->
                <div class="form-section">
                    <h5 class="section-title">📍 Location & Details</h5>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location') }}" 
                               placeholder="Enter event location...">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes or instructions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Attendees -->
                <div class="form-section">
                    <h5 class="section-title">👥 Attendees</h5>
                    
                    <div class="mb-3">
                        <label for="attendee_input" class="form-label">Add Attendees (Email addresses)</label>
                        <input type="email" class="form-control" id="attendee_input" 
                               placeholder="Enter email address and press Enter">
                        <small class="form-text text-muted">Press Enter to add each email address</small>
                    </div>
                    
                    <div id="attendees_list" class="mb-3">
                        <!-- Attendee tags will be added here -->
                    </div>
                    
                    <input type="hidden" name="attendees" id="attendees_hidden">
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Priority & Status -->
                <div class="form-section">
                    <h5 class="section-title">🚩 Priority</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex flex-wrap">
                            <span class="badge priority-badge bg-secondary" data-priority="low">
                                ↓ Low
                            </span>
                            <span class="badge priority-badge bg-info active" data-priority="medium">
                                ➖ Medium
                            </span>
                            <span class="badge priority-badge bg-warning" data-priority="high">
                                ↑ High
                            </span>
                            <span class="badge priority-badge bg-danger" data-priority="urgent">
                                ❗ Urgent
                            </span>
                        </div>
                        <input type="hidden" name="priority" id="priority" value="{{ old('priority', 'medium') }}">
                    </div>
                </div>
                
                <!-- Color -->
                <div class="form-section">
                    <h5 class="section-title">🎨 Color</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex flex-wrap">
                            <div class="color-picker" data-color="#007bff" style="background-color: #007bff;"></div>
                            <div class="color-picker" data-color="#28a745" style="background-color: #28a745;"></div>
                            <div class="color-picker" data-color="#dc3545" style="background-color: #dc3545;"></div>
                            <div class="color-picker" data-color="#ffc107" style="background-color: #ffc107;"></div>
                            <div class="color-picker" data-color="#6f42c1" style="background-color: #6f42c1;"></div>
                            <div class="color-picker" data-color="#fd7e14" style="background-color: #fd7e14;"></div>
                            <div class="color-picker" data-color="#20c997" style="background-color: #20c997;"></div>
                            <div class="color-picker" data-color="#6c757d" style="background-color: #6c757d;"></div>
                        </div>
                        <input type="hidden" name="color" id="color" value="{{ old('color') }}">
                        <small class="form-text text-muted">Choose a color for your event (optional)</small>
                    </div>
                </div>
                
                <!-- Reminder -->
                <div class="form-section">
                    <h5 class="section-title">🔔 Reminder</h5>
                    
                    <div class="mb-3">
                        <label for="reminder_minutes" class="form-label">Remind me before</label>
                        <select class="form-select @error('reminder_minutes') is-invalid @enderror" 
                                id="reminder_minutes" name="reminder_minutes">
                            <option value="">No reminder</option>
                            <option value="5" {{ old('reminder_minutes') == '5' ? 'selected' : '' }}>5 minutes</option>
                            <option value="15" {{ old('reminder_minutes') == '15' ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('reminder_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="60" {{ old('reminder_minutes') == '60' ? 'selected' : '' }}>1 hour</option>
                            <option value="120" {{ old('reminder_minutes') == '120' ? 'selected' : '' }}>2 hours</option>
                            <option value="1440" {{ old('reminder_minutes') == '1440' ? 'selected' : '' }}>1 day</option>
                            <option value="10080" {{ old('reminder_minutes') == '10080' ? 'selected' : '' }}>1 week</option>
                        </select>
                        @error('reminder_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Recurring -->
                <div class="form-section">
                    <h5 class="section-title">🔄 Recurring</h5>
                    
                    <div class="mb-3">
                        <label for="recurring_type" class="form-label">Repeat</label>
                        <select class="form-select @error('recurring_type') is-invalid @enderror" 
                                id="recurring_type" name="recurring_type">
                            <option value="none" {{ old('recurring_type', 'none') == 'none' ? 'selected' : '' }}>Does not repeat</option>
                            <option value="daily" {{ old('recurring_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('recurring_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('recurring_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('recurring_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('recurring_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="recurring-options" id="recurringOptions">
                        <div class="mb-3">
                            <label for="recurring_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('recurring_end_date') is-invalid @enderror" 
                                   id="recurring_end_date" name="recurring_end_date" value="{{ old('recurring_end_date') }}">
                            @error('recurring_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3" id="weeklyOptions" style="display: none;">
                            <label class="form-label">Repeat on</label>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="1" id="monday">
                                    <label class="form-check-label" for="monday">Mon</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="2" id="tuesday">
                                    <label class="form-check-label" for="tuesday">Tue</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="3" id="wednesday">
                                    <label class="form-check-label" for="wednesday">Wed</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="4" id="thursday">
                                    <label class="form-check-label" for="thursday">Thu</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="5" id="friday">
                                    <label class="form-check-label" for="friday">Fri</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="6" id="saturday">
                                    <label class="form-check-label" for="saturday">Sat</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recurring_days[]" value="0" id="sunday">
                                    <label class="form-check-label" for="sunday">Sun</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Attachments -->
                <div class="form-section">
                    <h5 class="section-title">📎 Attachments</h5>
                    
                    <div class="file-upload-area" id="fileUploadArea">
                        <span style="font-size: 2rem; color: #6c757d;" class="mb-2 d-block">☁️</span>
                        <p class="mb-2">Drag & drop files here or click to browse</p>
                        <small class="text-muted">Maximum file size: 10MB</small>
                        <input type="file" name="attachments[]" id="attachments" multiple class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                    </div>
                    
                    <div id="filesList" class="mt-3">
                        <!-- Selected files will be shown here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="form-section">
            <div class="d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                        💾 Save as Draft
                    </button>
                </div>
                <div>
                    <a href="{{ route('events.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        📅 Create Event
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Priority selection
    const priorityBadges = document.querySelectorAll('.priority-badge');
    const priorityInput = document.getElementById('priority');
    
    priorityBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            priorityBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            priorityInput.value = this.dataset.priority;
        });
    });
    
    // Color selection
    const colorPickers = document.querySelectorAll('.color-picker');
    const colorInput = document.getElementById('color');
    
    colorPickers.forEach(picker => {
        picker.addEventListener('click', function() {
            colorPickers.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            colorInput.value = this.dataset.color;
        });
    });
    
    // All day toggle
    const allDayCheckbox = document.getElementById('is_all_day');
    const timeFields = document.getElementById('timeFields');
    
    allDayCheckbox.addEventListener('change', function() {
        if (this.checked) {
            timeFields.classList.add('disabled');
            timeFields.querySelectorAll('input').forEach(input => {
                input.removeAttribute('required');
                input.value = '';
            });
        } else {
            timeFields.classList.remove('disabled');
        }
    });
    
    // Recurring options
    const recurringType = document.getElementById('recurring_type');
    const recurringOptions = document.getElementById('recurringOptions');
    const weeklyOptions = document.getElementById('weeklyOptions');
    
    recurringType.addEventListener('change', function() {
        if (this.value !== 'none') {
            recurringOptions.style.display = 'block';
            if (this.value === 'weekly') {
                weeklyOptions.style.display = 'block';
            } else {
                weeklyOptions.style.display = 'none';
            }
        } else {
            recurringOptions.style.display = 'none';
            weeklyOptions.style.display = 'none';
        }
    });
    
    // Attendees management
    const attendeeInput = document.getElementById('attendee_input');
    const attendeesList = document.getElementById('attendees_list');
    const attendeesHidden = document.getElementById('attendees_hidden');
    let attendees = [];
    
    attendeeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const email = this.value.trim();
            if (email && isValidEmail(email) && !attendees.includes(email)) {
                attendees.push(email);
                updateAttendeesList();
                this.value = '';
            }
        }
    });
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function updateAttendeesList() {
        attendeesList.innerHTML = attendees.map((email, index) => `
            <span class="attendee-tag">
                ${email}
                <button type="button" class="btn-close btn-close-sm ms-2" onclick="removeAttendee(${index})"></button>
            </span>
        `).join('');
        attendeesHidden.value = JSON.stringify(attendees);
    }
    
    window.removeAttendee = function(index) {
        attendees.splice(index, 1);
        updateAttendeesList();
    };
    
    // File upload
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('attachments');
    const filesList = document.getElementById('filesList');
    
    fileUploadArea.addEventListener('click', () => fileInput.click());
    
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
        fileInput.files = files;
        updateFilesList();
    });
    
    fileInput.addEventListener('change', updateFilesList);
    
    function updateFilesList() {
        const files = Array.from(fileInput.files);
        filesList.innerHTML = files.map((file, index) => `
            <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                <div>
                    📄 
                    <span>${file.name}</span>
                    <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    ❌
                </button>
            </div>
        `).join('');
    }
    
    window.removeFile = function(index) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        updateFilesList();
    };
    
    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
    
    // Initialize end date minimum
    if (startDate.value) {
        endDate.min = startDate.value;
    }
    
    // Form validation
    const form = document.getElementById('eventForm');
    form.addEventListener('submit', function(e) {
        const allDay = document.getElementById('is_all_day').checked;
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        
        if (!allDay && (!startTime.value || !endTime.value)) {
            e.preventDefault();
            alert('Please provide start and end times for non-all-day events.');
            return false;
        }
    });
});

function saveDraft() {
    // Add draft functionality here
    alert('Draft functionality would be implemented here');
}
</script>
@endsection