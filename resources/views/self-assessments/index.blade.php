@extends('layouts.app')

@section('title', 'Self Assessments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-check"></i>
                        Self Assessments
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('self-assessments.history') }}" class="btn btn-outline-info">
                            <i class="fas fa-history"></i> History Timeline
                        </a>
                        <a href="{{ route('self-assessments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Assessment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="period" class="form-label">Period</label>
                                <input type="text" name="period" id="period" class="form-control" 
                                       placeholder="Search by period..." value="{{ request('period') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($assessments->count() > 0)
                        <!-- Assessments List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Submitted</th>
                                        <th>Reviewed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessments as $assessment)
                                        <tr>
                                            <td>
                                                <strong>{{ $assessment->period }}</strong>
                                            </td>
                                            <td>
                                                {!! $assessment->status_badge !!}
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $assessment->created_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($assessment->submitted_at)
                                                    <small class="text-muted">
                                                        {{ $assessment->submitted_at->format('M d, Y') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assessment->reviewed_at)
                                                    <small class="text-muted">
                                                        {{ $assessment->reviewed_at->format('M d, Y') }}
                                                        @if($assessment->reviewer)
                                                            <br>by {{ $assessment->reviewer->name }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('self-assessments.show', $assessment) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($assessment->is_editable)
                                                        <a href="{{ route('self-assessments.edit', $assessment) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('self-assessments.destroy', $assessment) }}" 
                                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assessment?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($assessments->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $assessments->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No self assessments found</h5>
                            @if(request()->hasAny(['status', 'period']))
                                <p class="text-gray-500">Try adjusting your filters or create a new assessment.</p>
                                <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @else
                                <p class="text-gray-500">Create your first self assessment to get started.</p>
                            @endif
                            <a href="{{ route('self-assessments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Assessment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#status').change(function() {
        $(this).closest('form').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    $('#period').on('input', function() {
        clearTimeout(searchTimeout);
        const form = $(this).closest('form');
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500);
    });
});
</script>
@endpush
@endsection