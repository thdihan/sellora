@extends('layouts.app')

@section('title', 'Assessments')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Assessments</h1>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
            Create Assessment
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assessments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Assessments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Attempts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_attempts'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['average_score'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('assessments.index') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search assessments...">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="soft_skills" {{ request('category') === 'soft_skills' ? 'selected' : '' }}>Soft Skills</option>
                        <option value="knowledge" {{ request('category') === 'knowledge' ? 'selected' : '' }}>Knowledge</option>
                        <option value="personality" {{ request('category') === 'personality' ? 'selected' : '' }}>Personality</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="difficulty" class="form-label">Difficulty</label>
                    <select class="form-control" id="difficulty" name="difficulty">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('assessments.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assessments List</h6>
        </div>
        <div class="card-body">
            @if($assessments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-bold">{{ $assessment->title }}</div>
                                                @if($assessment->description)
                                                    <div class="text-muted small">{{ Str::limit($assessment->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assessment->category)
                                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $assessment->category)) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $assessment->type)) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $difficultyColors = [
                                                'beginner' => 'success',
                                                'intermediate' => 'warning', 
                                                'advanced' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $difficultyColors[$assessment->difficulty_level] ?? 'secondary' }}">
                                            {{ ucfirst($assessment->difficulty_level) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ count($assessment->questions) }}</td>
                                    <td class="text-center">
                                        <div class="small">
                                            <div>Total: {{ $assessment->attempts_count }}</div>
                                            <div class="text-success">Completed: {{ $assessment->completed_attempts_count }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assessment->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $assessment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('assessments.show', $assessment) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('assessments.take', $assessment) }}">
                                                    <i class="fas fa-play"></i> Take Assessment
                                                </a>
                                                <a class="dropdown-item" href="{{ route('assessments.edit', $assessment) }}">
                                                    Edit
                                                </a>
                                                <a class="dropdown-item" href="{{ route('assessments.analytics', $assessment) }}">
                                                    <i class="fas fa-chart-bar"></i> Analytics
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('assessments.toggle-status', $assessment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        @if($assessment->is_active)
                                                            <i class="fas fa-pause"></i> Deactivate
                                                        @else
                                                            <i class="fas fa-play"></i> Activate
                                                        @endif
                                                    </button>
                                                </form>
                                                <a class="dropdown-item" href="{{ route('assessments.duplicate', $assessment) }}">
                                                    <i class="fas fa-copy"></i> Duplicate
                                                </a>
                                                <a class="dropdown-item" href="{{ route('assessments.export', $assessment) }}">
                                                    <i class="fas fa-download"></i> Export Results
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assessment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $assessments->firstItem() }} to {{ $assessments->lastItem() }} of {{ $assessments->total() }} results
                    </div>
                    {{ $assessments->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No assessments found</h5>
                    <p class="text-gray-500">Create your first assessment to get started.</p>
                    <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                        Create Assessment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#category, #difficulty, #status').change(function() {
        $(this).closest('form').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    $('#search').on('input', function() {
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