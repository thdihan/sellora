@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">User Management</h1>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    Add New User
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Name, email, or designation">
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="card mb-4" id="bulkActionsCard" style="display: none;">
                <div class="card-body">
                    <form id="bulkUpdateForm" method="POST" action="{{ route('users.bulk-update-roles') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="bulk_role_id" class="form-label">Assign Role to Selected Users</label>
                                <select class="form-select" id="bulk_role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-users-cog"></i> Update Selected Users
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearSelection()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input user-checkbox" 
                                                   value="{{ $user->id }}" name="user_ids[]">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    @if($user->photo)
                                                        <img src="{{ asset('storage/' . $user->photo) }}" 
                                                             class="rounded-circle" width="32" height="32">
                                                    @else
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 32px; height: 32px; color: white; font-size: 14px;">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->isOwner())
                                                        <span class="badge bg-warning text-dark ms-1">Owner</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role)
                                                <span class="badge bg-info">{{ $user->role->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->designation ?? '-' }}</td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $user) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    Edit
                                                </a>
                                                @if(!$user->isOwner() && $user->id !== auth()->id())
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn" 
                                                            title="Delete" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>No users found.</p>
                                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                    Add First User
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsCard = document.getElementById('bulkActionsCard');
    const bulkUpdateForm = document.getElementById('bulkUpdateForm');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    // Individual checkbox change
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === userCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < userCheckboxes.length;
            toggleBulkActions();
        });
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActionsCard.style.display = 'block';
        } else {
            bulkActionsCard.style.display = 'none';
        }
    }

    // Bulk update form submission
    bulkUpdateForm.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        
        // Add hidden inputs for selected user IDs
        checkedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'user_ids[]';
            hiddenInput.value = checkbox.value;
            this.appendChild(hiddenInput);
        });

        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one user.');
            return false;
        }

        if (!confirm(`Are you sure you want to update the role for ${checkedBoxes.length} selected user(s)?`)) {
            e.preventDefault();
            return false;
        }
    });
});

function clearSelection() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('bulkActionsCard').style.display = 'none';
}

// Handle user deletion with AJAX
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-user-btn')) {
        const userId = e.target.getAttribute('data-user-id');
        const userName = e.target.getAttribute('data-user-name');
        
        if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Remove the user row from the table
                    const row = e.target.closest('tr');
                    row.remove();
                    
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        User "${userName}" deleted successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.card'));
                    
                    // Auto-hide alert after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                } else {
                    response.json().then(data => {
                        alert(data.message || 'Error deleting user. Please try again.');
                    }).catch(() => {
                        alert('Error deleting user. Please try again.');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting user. Please try again.');
            });
        }
    }
});
</script>
@endsection