@extends('layouts.app')

@section('title', 'Sales Targets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sales Targets</h3>
                    <div class="btn-group">
                        <a href="{{ route('sales-targets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Target
                        </a>
                        <a href="{{ route('sales-targets.bulk-create') }}" class="btn btn-success">
                            <i class="fas fa-users"></i> Bulk Assign Targets
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Assigned To</th>
                                    <th>Target Year</th>
                                    <th>Total Yearly Target</th>
                                    <th>Status</th>
                                    <th>Assigned By</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($targets as $target)
                                    <tr>
                                        <td>{{ $target->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-primary">
                                                        {{ strtoupper(substr($target->assignedTo->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong>{{ $target->assignedTo->name }}</strong><br>
                                                    <small class="text-muted">{{ $target->assignedTo->role->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $target->target_year }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                ৳{{ number_format($target->total_yearly_target, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($target->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($target->status === 'inactive')
                                                <span class="badge bg-warning">Inactive</span>
                                            @else
                                                <span class="badge bg-secondary">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $target->assignedBy->name }}<br>
                                                <span class="text-muted">{{ $target->assignedBy->role->name }}</span>
                                            </small>
                                        </td>
                                        <td>{{ $target->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales-targets.show', $target) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($target->assigned_by_user_id === auth()->id())
                                                    <a href="{{ route('sales-targets.edit', $target) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Edit Target">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('sales-targets.destroy', $target) }}" 
                                                          method="POST" 
                                                          class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this target?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete Target">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-target fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Sales Targets Found</h5>
                                                <p class="text-muted">Create your first sales target to get started.</p>
                                                <a href="{{ route('sales-targets.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create New Target
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($targets->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $targets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar {
    width: 32px;
    height: 32px;
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.empty-state {
    padding: 2rem;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush