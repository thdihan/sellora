@extends('layouts.app')

@section('title', 'Budget Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Budget Management</h1>
            <p class="mb-0 text-muted">Manage your budgets, track spending, and monitor financial goals</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('budgets.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
            <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                Create Budget
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Budgets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="text-gray-300" style="font-size: 2rem;">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Exceeded</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['exceeded'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Allocated</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_allocated'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Spent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_spent'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('budgets.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search budgets...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="period_type" class="form-label">Period Type</label>
                    <select class="form-control" id="period_type" name="period_type">
                        <option value="">All Periods</option>
                        @foreach($periodTypes as $type)
                            <option value="{{ $type }}" {{ request('period_type') == $type ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Budgets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Budgets List</h6>
        </div>
        <div class="card-body">
            @if($budgets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="budgetsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Period</th>
                                <th>Duration</th>
                                <th>Total Amount</th>
                                <th>Spent</th>
                                <th>Remaining</th>
                                <th>Utilization</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgets as $budget)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-bold">{{ $budget->name }}</div>
                                                @if($budget->description)
                                                    <div class="text-muted small">{{ Str::limit($budget->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $budget->period_type)) }}</span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $budget->start_date->format('M d, Y') }}</div>
                                            <div class="text-muted">to {{ $budget->end_date->format('M d, Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">${{ number_format($budget->total_amount, 2) }}</span>
                                        <div class="text-muted small">{{ $budget->currency }}</div>
                                    </td>
                                    <td>
                                        <span class="text-danger">${{ number_format($budget->spent_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">${{ number_format($budget->remaining_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $utilization = $budget->utilization_percentage;
                                            $progressClass = $utilization > 90 ? 'bg-danger' : ($utilization > 75 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                 style="width: {{ min($utilization, 100) }}%" 
                                                 aria-valuenow="{{ $utilization }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($utilization, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'active' => 'success',
                                                'completed' => 'primary',
                                                'cancelled' => 'dark',
                                                'exceeded' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$budget->status] ?? 'secondary' }}">
                                            {{ ucfirst($budget->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $budget->creator->name ?? 'Unknown' }}</div>
                                            <div class="text-muted">{{ $budget->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($budget->status, ['draft', 'pending']))
                                                <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    Edit
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="duplicateBudget({{ $budget->id }})" title="Duplicate">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            @if($budget->status === 'draft')
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="submitForApproval({{ $budget->id }})" title="Submit for Approval">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            @endif
                                            @if($budget->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="approveBudget({{ $budget->id }})" title="Approve">
                                                    ✓
                                                </button>
                                            @endif
                                            @if($budget->status === 'approved')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="activateBudget({{ $budget->id }})" title="Activate">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                            @if(in_array($budget->status, ['active', 'exceeded']))
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="completeBudget({{ $budget->id }})" title="Complete">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </button>
                                            @endif
                                            @if(!in_array($budget->status, ['completed', 'cancelled']))
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="cancelBudget({{ $budget->id }})" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            @if(in_array($budget->status, ['draft', 'pending', 'cancelled']))
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteBudget({{ $budget->id }})" title="Delete">
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $budgets->firstItem() }} to {{ $budgets->lastItem() }} of {{ $budgets->total() }} results
                    </div>
                    {{ $budgets->links('vendor.pagination.custom-3d') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-wallet fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No budgets found</h5>
                    <p class="text-muted">Create your first budget to start managing your finances.</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                        Create Budget
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitForApproval(budgetId) {
    if (confirm('Are you sure you want to submit this budget for approval?')) {
        fetch(`/budgets/${budgetId}/submit-approval`, {
            method: 'POST',
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
                alert(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting for approval');
        });
    }
}

function approveBudget(budgetId) {
    if (confirm('Are you sure you want to approve this budget?')) {
        fetch(`/budgets/${budgetId}/approve`, {
            method: 'POST',
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
                alert(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving budget');
        });
    }
}

function activateBudget(budgetId) {
    if (confirm('Are you sure you want to activate this budget?')) {
        fetch(`/budgets/${budgetId}/activate`, {
            method: 'POST',
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
                alert(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while activating budget');
        });
    }
}

function completeBudget(budgetId) {
    if (confirm('Are you sure you want to complete this budget?')) {
        fetch(`/budgets/${budgetId}/complete`, {
            method: 'POST',
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
                alert(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while completing budget');
        });
    }
}

function cancelBudget(budgetId) {
    if (confirm('Are you sure you want to cancel this budget?')) {
        fetch(`/budgets/${budgetId}/cancel`, {
            method: 'POST',
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
                alert(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling budget');
        });
    }
}

function duplicateBudget(budgetId) {
    if (confirm('Are you sure you want to duplicate this budget?')) {
        window.location.href = `/budgets/${budgetId}/duplicate`;
    }
}

function deleteBudget(budgetId) {
    if (confirm('Are you sure you want to delete this budget? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/budgets/${budgetId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection