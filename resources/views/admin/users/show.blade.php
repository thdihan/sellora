@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">User Details: {{ $user->name }}</h1>
                <div class="d-flex gap-2">
                    @if(!$user->isOwner())
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                    @endif
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            @if($user->isOwner())
                <div class="alert alert-info mb-4">
                    <i class="fas fa-crown"></i>
                    <strong>System Owner Account:</strong> This is the system owner account with full administrative privileges.
                </div>
            @endif

            <div class="row">
                <!-- User Information Card -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">User Information</h5>
                            <div class="d-flex gap-2">
                                @if($user->is_active ?? true)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                                @if($user->isOwner())
                                    <span class="badge bg-warning">System Owner</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <p class="form-control-plaintext">{{ $user->name }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <p class="form-control-plaintext">
                                            {{ $user->email }}
                                            @if($user->email_verified_at)
                                                <i class="fas fa-check-circle text-success ms-2" title="Email Verified"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle text-warning ms-2" title="Email Not Verified"></i>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Role</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-primary fs-6">{{ $user->role->name ?? 'No Role Assigned' }}</span>
                                            @if($user->role && $user->role->description)
                                                <br><small class="text-muted">{{ $user->role->description }}</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Designation</label>
                                        <p class="form-control-plaintext">{{ $user->designation ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Account Status</label>
                                        <p class="form-control-plaintext">
                                            @if($user->is_active ?? true)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Verification</label>
                                        <p class="form-control-plaintext">
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">Verified</span>
                                                <br><small class="text-muted">{{ $user->email_verified_at->format('M d, Y \\a\\t g:i A') }}</small>
                                            @else
                                                <span class="badge bg-warning">Not Verified</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Timeline Card -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Account Created</h6>
                                        <p class="timeline-text">{{ $user->created_at->format('M d, Y \\a\\t g:i A') }}</p>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                
                                @if($user->email_verified_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Email Verified</h6>
                                        <p class="timeline-text">{{ $user->email_verified_at->format('M d, Y \\a\\t g:i A') }}</p>
                                        <small class="text-muted">{{ $user->email_verified_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Last Updated</h6>
                                        <p class="timeline-text">{{ $user->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(!$user->isOwner())
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit User
                                    </a>
                                    
                                    @if(!$user->email_verified_at)
                                        <form method="POST" action="{{ route('users.verify-email', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-check-circle"></i> Mark Email as Verified
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($user->is_active ?? true)
                                        <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning w-100" 
                                                    onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                <i class="fas fa-user-slash"></i> Deactivate Account
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-user-check"></i> Activate Account
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <hr>
                                    
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100" 
                                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete User
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-shield-alt"></i>
                                        <strong>Protected Account</strong><br>
                                        This system owner account cannot be modified or deleted.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Statistics Card -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $user->created_at->diffInDays() }}</h4>
                                        <small class="text-muted">Days Since Joined</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $user->updated_at->diffInDays() }}</h4>
                                    <small class="text-muted">Days Since Updated</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    height: calc(100% + 20px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #495057;
}
</style>
@endsection