@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Expenses Management</h3>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                        Add New Expense
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search expenses..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="priority" class="form-select">
                                        <option value="">All Priorities</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="category" value="{{ request('category') }}">
                                <input type="hidden" name="priority" value="{{ request('priority') }}">
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        Filter by Date
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Expenses Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Employee</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->id }}</td>
                                        <td>
                                            <strong>{{ $expense->title }}</strong>
                                            @if($expense->vendor)
                                                <br><small class="text-muted">{{ $expense->vendor }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $expense->user->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $expense->category->name ?? $expense->getOriginal('category') ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $expense->currency }} {{ number_format($expense->amount, 2) }}</strong>
                                            @if($expense->tax_amount)
                                                <br><small class="text-muted">Tax: {{ $expense->currency }} {{ number_format($expense->tax_amount, 2) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $expense->status_color }}">
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $expense->priority_color }}">
                                                {{ ucfirst($expense->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    Edit
                                                </a>
                                                @if($expense->canBeApproved())
                                                    <button type="button" class="btn btn-sm btn-outline-success" title="Approve" 
                                                            onclick="approveExpense({{ $expense->id }})">
                                                        ✓
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Reject" 
                                                            onclick="rejectExpense({{ $expense->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                @if($expense->isApproved())
                                                    <form action="{{ route('expenses.mark-paid', $expense) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as Paid">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-receipt fa-3x mb-3"></i>
                                                <p>No expenses found.</p>
                                                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                                    Add First Expense
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($expenses->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $expenses->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" 
                                  placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectionForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Please provide a reason for rejecting this expense..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.table-responsive {
    border-radius: 0.375rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 2px;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
}
</style>

<script>
function approveExpense(expenseId) {
    const form = document.getElementById('approvalForm');
    form.action = `/expenses/${expenseId}/approve`;
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectExpense(expenseId) {
    const form = document.getElementById('rejectionForm');
    form.action = `/expenses/${expenseId}/reject`;
    
    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    modal.show();
}

// Clear form when modals are hidden
document.getElementById('approvalModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('approval_notes').value = '';
});

document.getElementById('rejectionModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('rejection_reason').value = '';
});
</script>
@endsection