@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit User: {{ $user->name }}</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            @if($user->isOwner())
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>System Owner Account:</strong> This is the system owner account. Some fields are protected and cannot be modified.
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('users.update', $user) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" 
                                               {{ $user->isOwner() ? 'readonly' : '' }} required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($user->isOwner())
                                            <div class="form-text text-warning">
                                                <i class="fas fa-lock"></i> Owner email cannot be changed
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select @error('role_id') is-invalid @enderror" 
                                                id="role_id" name="role_id" {{ $user->isOwner() ? 'disabled' : '' }} required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" 
                                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                                                        data-description="{{ $role->description }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($user->isOwner())
                                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                        @endif
                                        @error('role_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text" id="roleDescription"></div>
                                        @if($user->isOwner())
                                            <div class="form-text text-warning">
                                                <i class="fas fa-lock"></i> Owner role cannot be changed
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="designation" class="form-label">Designation</label>
                                        <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                               id="designation" name="designation" value="{{ old('designation', $user->designation) }}" 
                                               placeholder="e.g., Senior Sales Manager">
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="mb-3">Account Status</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="email_verified" 
                                                       name="email_verified" {{ $user->email_verified_at ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_verified">
                                                    Email Verified
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" 
                                                       name="is_active" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                                                       {{ $user->isOwner() ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Account Active
                                                </label>
                                                @if($user->isOwner())
                                                    <input type="hidden" name="is_active" value="1">
                                                    <div class="form-text text-warning">
                                                        <i class="fas fa-lock"></i> Owner account cannot be deactivated
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <h6 class="mb-3">Change Password (Optional)</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" name="password">
                                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Leave blank to keep current password. Must be at least 8 characters if changing.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" 
                                                       id="password_confirmation" name="password_confirmation">
                                                <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Information Card -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y \\a\\t g:i A') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email Verified:</strong> 
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">{{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="badge bg-warning">Not Verified</span>
                                        @endif
                                    </p>
                                    <p><strong>Current Role:</strong> 
                                        <span class="badge bg-primary">{{ $user->role->name ?? 'No Role' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Role description display
    const roleSelect = document.getElementById('role_id');
    const roleDescription = document.getElementById('roleDescription');
    
    if (roleSelect && !roleSelect.disabled) {
        roleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description');
            roleDescription.textContent = description || '';
        });
        
        // Trigger on page load
        roleSelect.dispatchEvent(new Event('change'));
    }
    
    // Password visibility toggles
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
@endsection