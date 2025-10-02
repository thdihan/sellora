@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Expense Details</h4>
                    <div>
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($expense->status === 'pending' && auth()->user()->role && auth()->user()->role->name === 'Admin')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                ✓ Approve
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                × Reject
                            </button>
                        @endif
                        @if($expense->status === 'approved' && auth()->user()->role && auth()->user()->role->name === 'Admin')
                            <form action="{{ route('expenses.mark-paid', $expense) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-money-bill"></i> Mark as Paid
                                </button>
                            </form>
                        @endif
                        @if($expense->user_id === auth()->id() && $expense->status === 'pending')
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Title:</strong></div>
                                <div class="col-sm-9">{{ $expense->title }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Description:</strong></div>
                                <div class="col-sm-9">{{ $expense->description ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Category:</strong></div>
                                <div class="col-sm-9">
                                    <span class="badge bg-info">{{ ucfirst($expense->category->name ?? $expense->getOriginal('category') ?? 'N/A') }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Amount:</strong></div>
                                <div class="col-sm-9">
                                    <span class="h5 text-primary">{{ $expense->currency }} {{ number_format($expense->amount, 2) }}</span>
                                </div>
                            </div>
                            @if($expense->tax_amount)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Tax Amount:</strong></div>
                                <div class="col-sm-9">{{ $expense->currency }} {{ number_format($expense->tax_amount, 2) }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Total Amount:</strong></div>
                                <div class="col-sm-9">
                                    <span class="h5 text-success">{{ $expense->currency }} {{ number_format($expense->getTotalAmount(), 2) }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Date:</strong></div>
                                <div class="col-sm-9">{{ $expense->expense_date->format('M d, Y') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Payment Method:</strong></div>
                                <div class="col-sm-9">{{ ucfirst($expense->payment_method) }}</div>
                            </div>
                            @if($expense->reference_number)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Reference Number:</strong></div>
                                <div class="col-sm-9">{{ $expense->reference_number }}</div>
                            </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Reimbursable:</strong></div>
                                <div class="col-sm-9">
                                    <span class="badge {{ $expense->is_reimbursable ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $expense->is_reimbursable ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Submitted by:</strong></div>
                                <div class="col-sm-9">{{ $expense->user->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Submitted on:</strong></div>
                                <div class="col-sm-9">{{ $expense->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Status Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Status:</strong><br>
                                        <span class="badge bg-{{ $expense->status_color }} fs-6">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Priority:</strong><br>
                                        <span class="badge bg-{{ $expense->priority_color }}">
                                            {{ ucfirst($expense->priority) }}
                                        </span>
                                    </div>
                                    @if($expense->approved_by)
                                    <div class="mb-3">
                                        <strong>Approved by:</strong><br>
                                        {{ $expense->approver->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Approved on:</strong><br>
                                        {{ $expense->approved_at->format('M d, Y H:i') }}
                                    </div>
                                    @endif
                                    @if($expense->notes)
                                    <div class="mb-3">
                                        <strong>Notes:</strong><br>
                                        <div class="alert alert-info p-2">
                                            {{ $expense->notes }}
                                        </div>
                                    </div>
                                    @endif
                                    @if($expense->rejection_reason)
                                    <div class="mb-3">
                                        <strong>Rejection Reason:</strong><br>
                                        <div class="alert alert-danger p-2">
                                            {{ $expense->rejection_reason }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($expense->attachments && count($expense->attachments) > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Attachments</h6>
                            <div class="row">
                                @foreach($expense->attachments as $index => $attachment)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file fa-2x text-muted mb-2"></i>
                                            <p class="card-text small">{{ $attachment['name'] }}</p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('expenses.download-attachment', [$expense, $index]) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                @if($expense->user_id === auth()->id() && $expense->status === 'pending')
                                                <form action="{{ route('expenses.remove-attachment', [$expense, $index]) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" 
                                                            onclick="return confirm('Remove this attachment?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@if($expense->status === 'pending' && auth()->user()->role && auth()->user()->role->name === 'Admin')
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Approve Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
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

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reject Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
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
@endif
@endsection