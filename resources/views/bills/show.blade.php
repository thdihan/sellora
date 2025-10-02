@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Bill #{{ $bill->id }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-{{ $bill->status_color }} fs-6">
                            {{ ucfirst($bill->status) }}
                        </span>
                        @if($bill->canBeEdited())
                            <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-outline-warning">
                                ✏️ Edit
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Bill Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                ℹ️ Bill Information
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Amount</label>
                                <p class="fs-4 text-success mb-0">${{ number_format($bill->amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $bill->status_color }} fs-6">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Submitted By</label>
                                <p class="mb-0">{{ $bill->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created Date</label>
                                <p class="mb-0">{{ $bill->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Purpose</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $bill->purpose }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Actions -->
                    @if($bill->canBeApproved() || $bill->canBeMarkedAsPaid())
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    📋 Actions
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($bill->canBeApproved())
                                        <button type="button" class="btn btn-success" onclick="approveBill()">
                                            ✅ Approve Bill
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="rejectBill()">
                                            ❌ Reject Bill
                                        </button>
                                    @endif
                                    @if($bill->canBeMarkedAsPaid())
                                        <button type="button" class="btn btn-primary" onclick="markAsPaid()">
                                            💰 Mark as Paid
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if($bill->files->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    📎 Attachments ({{ $bill->files->count() }})
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    @foreach($bill->files as $index => $file)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex flex-column h-100">
                                                        <div class="text-center mb-3">
                                                            <span style="font-size: 3rem;" class="text-muted">📄</span>
                                                        </div>
                                                        <h6 class="card-title text-truncate" title="{{ $file->original_name }}">
                                                            {{ $file->original_name }}
                                                        </h6>
                                                        <p class="card-text text-muted small mb-3">
                                                            Size: {{ $file->formatted_size }}
                                                        </p>
                                                        <div class="mt-auto">
                                                            <a href="{{ route('bills.download-attachment', [$bill, $index]) }}" 
                                                               class="btn btn-primary btn-sm w-100">
                                                                📥 Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    📎 Attachments
                                </h5>
                                <div class="text-center py-4">
                                    <span style="font-size: 3rem;" class="text-muted mb-3">📄</span>
                                    <p class="text-muted">No attachments uploaded</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Approval History -->
                    @if($bill->approvals->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    📜 Approval History
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="timeline">
                                    @foreach($bill->approvals->sortByDesc('created_at') as $approval)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-{{ $approval->action_badge_color }}"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <span class="badge bg-{{ $approval->action_badge_color }}">{{ ucfirst($approval->action) }}</span>
                                                            by {{ $approval->actor->name }}
                                                        </h6>
                                                        @if($approval->notes)
                                                            <p class="text-muted mb-1">{{ $approval->notes }}</p>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ $approval->created_at->format('M d, Y H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                            ← Back to Bills
                        </a>
                        <div class="d-flex gap-2">
                            @if($bill->canBeEdited())
                                <a href="{{ route('bills.edit', $bill) }}" class="btn btn-warning">
                                    ✏️ Edit Bill
                                </a>
                            @endif
                            @if($bill->canBeDeleted())
                                <button type="button" class="btn btn-danger" onclick="deleteBill()">
                                    🗑️ Delete Bill
                                </button>
                            @endif
                        </div>
                    </div>
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

@push('styles')
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
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #dee2e6;
}

.bg-success {
    background-color: #198754 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.bg-primary {
    background-color: #0d6efd !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}
</style>
@endpush

@push('scripts')
<script>
let currentAction = null;

function approveBill() {
    currentAction = 'approve';
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to approve this bill?';
    document.getElementById('confirmAction').textContent = 'Approve';
    document.getElementById('confirmAction').className = 'btn btn-success';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function rejectBill() {
    currentAction = 'reject';
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to reject this bill?';
    document.getElementById('confirmAction').textContent = 'Reject';
    document.getElementById('confirmAction').className = 'btn btn-danger';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function markAsPaid() {
    currentAction = 'mark-paid';
    document.getElementById('approvalMessage').textContent = 'Are you sure you want to mark this bill as paid?';
    document.getElementById('confirmAction').textContent = 'Mark as Paid';
    document.getElementById('confirmAction').className = 'btn btn-primary';
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function deleteBill() {
    if (confirm('Are you sure you want to delete this bill? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("bills.destroy", $bill) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('confirmAction').addEventListener('click', function() {
    if (currentAction) {
        const notes = document.getElementById('approvalNotes').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bills/{{ $bill->id }}/${currentAction}`;
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