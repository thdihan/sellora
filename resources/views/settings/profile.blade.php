@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold text-primary mb-2">
                        <i class="fas fa-user-circle me-2"></i> Profile Settings
                    </h2>
                    <p class="text-muted mb-0 fs-6">Manage your personal profile information and preferences</p>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Picture Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-camera me-2"></i> Profile Picture
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <div class="profile-picture-container mb-4">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                                 alt="Profile Picture" 
                                 class="profile-picture rounded-circle shadow-lg" 
                                 id="profilePreview"
                                 style="width: 160px; height: 160px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                        @else
                            <div class="profile-picture-placeholder rounded-circle d-flex align-items-center justify-content-center shadow-lg" 
                                 id="profilePreview"
                                 style="width: 160px; height: 160px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 4px solid #fff; margin: 0 auto; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                                <i class="fas fa-user fa-4x text-secondary"></i>
                            </div>
                        @endif
                    </div>
                    
                    <form id="profilePictureForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control" id="profilePicture" name="photo" accept="image/*" style="display: none;">
                            <button type="button" class="btn btn-primary btn-lg px-4" onclick="document.getElementById('profilePicture').click()">
                                <i class="fas fa-upload me-2"></i> Upload Picture
                            </button>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg px-4" id="uploadBtn" style="display: none;">
                            <i class="fas fa-save me-2"></i> Save Picture
                        </button>
                    </form>
                    
                    @if(auth()->user()->photo)
                        <button type="button" class="btn btn-outline-danger" id="removePhotoBtn">
                            <i class="fas fa-trash me-2"></i> Remove Picture
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i> Account Information
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="fas fa-user-tag text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Role</small>
                                    <strong class="text-dark">{{ auth()->user()->role->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="fas fa-id-badge text-success me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Employee ID</small>
                                    <strong class="text-dark">{{ auth()->user()->employee_id ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="fas fa-calendar-plus text-info me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Member Since</small>
                                    <strong class="text-dark">{{ auth()->user()->created_at->format('M d, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="fas fa-clock text-warning me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Last Login</small>
                                    <strong class="text-dark">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y H:i') : 'Never' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Profile Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-user me-2"></i> Personal Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="profileForm" action="{{ route('settings.profile.update') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle me-2"></i> Basic Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="name" name="name" 
                                               value="{{ old('name', auth()->user()->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0" id="email" name="email" 
                                               value="{{ old('email', auth()->user()->email) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="designation" class="form-label fw-semibold">Designation</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-briefcase text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="designation" name="designation" 
                                               value="{{ old('designation', auth()->user()->designation) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" class="form-control border-start-0" id="phone" name="phone" 
                                               value="{{ old('phone', auth()->user()->phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Details Section -->
                        <div class="mb-4">
                            <h6 class="text-success fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-id-card me-2"></i> Personal Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-birthday-cake text-muted"></i>
                                        </span>
                                        <input type="date" class="form-control border-start-0" id="date_of_birth" name="date_of_birth" 
                                               value="{{ old('date_of_birth', auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="date_of_joining" class="form-label fw-semibold">Date of Joining</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-calendar-check text-muted"></i>
                                        </span>
                                        <input type="date" class="form-control border-start-0" id="date_of_joining" name="date_of_joining" 
                                               value="{{ old('date_of_joining', auth()->user()->date_of_joining ? auth()->user()->date_of_joining->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="blood_group" class="form-label fw-semibold">Blood Group</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-tint text-muted"></i>
                                        </span>
                                        <select class="form-select border-start-0" id="blood_group" name="blood_group">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" {{ old('blood_group', auth()->user()->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('blood_group', auth()->user()->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('blood_group', auth()->user()->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('blood_group', auth()->user()->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('blood_group', auth()->user()->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('blood_group', auth()->user()->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('blood_group', auth()->user()->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('blood_group', auth()->user()->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="timezone" class="form-label fw-semibold">Timezone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-globe text-muted"></i>
                                        </span>
                                        <select class="form-select border-start-0" id="timezone" name="timezone">
                                            <option value="UTC" {{ old('timezone', auth()->user()->timezone ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                            <option value="America/New_York" {{ old('timezone', auth()->user()->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                            <option value="America/Chicago" {{ old('timezone', auth()->user()->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                            <option value="America/Denver" {{ old('timezone', auth()->user()->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                            <option value="America/Los_Angeles" {{ old('timezone', auth()->user()->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                        </span>
                                        <textarea class="form-control border-start-0" id="address" name="address" rows="3" 
                                                  placeholder="Enter your full address">{{ old('address', auth()->user()->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="bio" class="form-label fw-semibold">Bio</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user-edit text-muted"></i>
                                        </span>
                                        <textarea class="form-control border-start-0" id="bio" name="bio" rows="3" 
                                                  placeholder="Tell us about yourself...">{{ old('bio', auth()->user()->bio) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Contact Section -->
                        <div class="mb-4">
                            <h6 class="text-warning fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-phone me-2"></i> Emergency Contact
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="emergency_contact_name" class="form-label fw-semibold">Contact Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user-friends text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="emergency_contact_name" name="emergency_contact_name" 
                                               value="{{ old('emergency_contact_name', auth()->user()->emergency_contact_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact_phone" class="form-label fw-semibold">Contact Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" class="form-control border-start-0" id="emergency_contact_phone" name="emergency_contact_phone" 
                                               value="{{ old('emergency_contact_phone', auth()->user()->emergency_contact_phone) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact_relationship" class="form-label fw-semibold">Relationship</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-heart text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                               value="{{ old('emergency_contact_relationship', auth()->user()->emergency_contact_relationship) }}" 
                                               placeholder="e.g., Spouse, Parent, Sibling">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notification Preferences Section -->
                        <div class="mb-4">
                            <h6 class="text-info fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-bell me-2"></i> Notification Preferences
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                                       name="email_notifications" value="1" 
                                                       {{ old('email_notifications', auth()->user()->email_notifications ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="email_notifications">
                                                    <i class="fas fa-envelope text-primary me-2"></i> Email Notifications
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                                       name="sms_notifications" value="1" 
                                                       {{ old('sms_notifications', auth()->user()->sms_notifications ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="sms_notifications">
                                                    <i class="fas fa-sms text-success me-2"></i> SMS Notifications
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="marketing_emails" 
                                                       name="marketing_emails" value="1" 
                                                       {{ old('marketing_emails', auth()->user()->marketing_emails ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="marketing_emails">
                                                    <i class="fas fa-bullhorn text-warning me-2"></i> Marketing Emails
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="security_alerts" 
                                                       name="security_alerts" value="1" 
                                                       {{ old('security_alerts', auth()->user()->security_alerts ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="security_alerts">
                                                    <i class="fas fa-shield-alt text-danger me-2"></i> Security Alerts
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end mt-5 pt-3 border-top">
                            <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-lock me-2"></i> Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="passwordForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-key text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 border-end-0" id="current_password" 
                                           name="current_password" required>
                                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" onclick="togglePasswordVisibility('current_password')">
                                        <i class="fas fa-eye text-muted" id="toggleCurrentPassword"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="new_password" class="form-label fw-semibold">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 border-end-0" id="new_password" 
                                           name="new_password" required>
                                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" onclick="togglePasswordVisibility('new_password')">
                                        <i class="fas fa-eye text-muted" id="toggleNewPassword"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-check-circle text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 border-end-0" id="confirm_password" 
                                           name="confirm_password" required>
                                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" onclick="togglePasswordVisibility('confirm_password')">
                                        <i class="fas fa-eye text-muted" id="toggleConfirmPassword"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-danger btn-lg px-4">
                                <i class="fas fa-key me-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile picture preview
    document.getElementById('profilePicture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                }
                document.getElementById('uploadBtn').style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Profile picture upload
    document.getElementById('profilePictureForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("settings.profile.upload-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                document.getElementById('uploadBtn').style.display = 'none';
                location.reload(); // Reload to show remove button
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while uploading the photo.');
        });
    });
    
    // Remove profile picture
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                fetch('{{ route("settings.profile.remove-photo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        location.reload();
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    showAlert('danger', 'An error occurred while removing the photo.');
                });
            }
        });
    }
    
    // Profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating profile.');
        });
    });
    
    // Password form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            showAlert('danger', 'New passwords do not match.');
            return;
        }
        
        const formData = new FormData(this);
        
        fetch('{{ route("settings.profile.update-password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                this.reset();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating password.');
        });
    });
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Password visibility toggle function
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.querySelector(`#toggle${fieldId.charAt(0).toUpperCase() + fieldId.slice(1).replace(/_([a-z])/g, (match, letter) => letter.toUpperCase())}`);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
});
</script>
@endpush