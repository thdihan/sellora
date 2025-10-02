@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Bills Management</h3>
                    <a href="{{ route('bills.create') }}" class="btn btn-primary">
                        Add New Bill
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('bills.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search bills..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        🔍
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary w-100">
                                        ❌ Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('bills.index') }}" class="row g-3">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
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

                    <!-- Bills Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td>{{ $bill->id }}</td>
                                        <td>{{ $bill->user->name }}</td>
                                        <td>
                                            <strong>${{ number_format($bill->amount, 2) }}</strong>
                                        </td>
                                        <td>{{ Str::limit($bill->purpose, 50) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $bill->status_color }}">
                                                {{ ucfirst($bill->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $bill->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    👁️
                                                </a>
                                                @if($bill->canBeEdited())
                                                    <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        Edit
                                                    </a>
                                                @endif
                                                @if($bill->canBeApproved())
                                                    <button type="button" class="btn btn-sm btn-outline-success" title="Approve" 
                                                            onclick="approveBill({{ $bill->id }})">
                                                        ✓
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Reject" 
                                                            onclick="rejectBill({{ $bill->id }})">
                                                        ❌
                                                    </button>
                                                @endif
                                                @if($bill->canBeMarkedAsPaid())
                                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Mark as Paid" 
                                                            onclick="markAsPaid({{ $bill->id }})">
                                                        💰
                                                    </button>
                                                @endif
                                                @if($bill->canBeDeleted())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" 
                                                            onclick="deleteBill({{ $bill->id }})">
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
                                                <span class="mb-3" style="font-size: 3rem;">🧾</span>
                                                <h5>No bills found</h5>
                                                <p>Start by creating your first bill.</p>
                                                <a href="{{ route('bills.create') }}" class="btn btn-primary">
                                                    Add New Bill
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($bills->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bills->appends(request()->query())->links('vendor.pagination.custom-3d') }}
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
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="approvalMessage"></p>
                <div class="mb-3">
                    <label for="approvalNotes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="approvalNotes" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentAction = null;
let currentBillId = null;

function approveBill(billId) {
    currentAction = 'approve';
    currentBillId = billId;
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to approve this bill?';
    document.getElementById('confirmAction').textContent = 'Approve';
    document.getElementById('confirmAction').className = 'btn btn-success';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function rejectBill(billId) {
    currentAction = 'reject';
    currentBillId = billId;
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to reject this bill?';
    document.getElementById('confirmAction').textContent = 'Reject';
    document.getElementById('confirmAction').className = 'btn btn-danger';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function markAsPaid(billId) {
    currentAction = 'mark-paid';
    currentBillId = billId;
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to mark this bill as paid?';
    document.getElementById('confirmAction').textContent = 'Mark as Paid';
    document.getElementById('confirmAction').className = 'btn btn-primary';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function deleteBill(billId) {
    if (confirm('Are you sure you want to delete this bill? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bills/${billId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('confirmAction').addEventListener('click', function() {
    if (currentAction && currentBillId) {
        const notes = document.getElementById('approvalNotes').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bills/${currentBillId}/${currentAction}`;
        form.innerHTML = `
            @csrf
            @method('PATCH')
            <input type="hidden" name="notes" value="${notes}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endpush