@extends('layouts.app')

@section('title', 'Visits Management')

@push('styles')
<style>
.visit-card {
    border-left: 4px solid #e3f2fd;
    transition: all 0.3s ease;
}
.visit-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.visit-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}
.status-scheduled { background-color: #e3f2fd; color: #1976d2; }
.status-in_progress { background-color: #fff3e0; color: #f57c00; }
.status-completed { background-color: #e8f5e8; color: #388e3c; }
.status-cancelled { background-color: #ffebee; color: #d32f2f; }
.status-rescheduled { background-color: #f3e5f5; color: #7b1fa2; }
.priority-low { border-left-color: #4caf50; }
.priority-medium { border-left-color: #ff9800; }
.priority-high { border-left-color: #f44336; }
.priority-urgent { border-left-color: #9c27b0; }
.filter-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.stat-label {
    color: #666;
    font-size: 0.9rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Visits Management</h1>
            <p class="text-muted">Manage customer visits and appointments</p>
        </div>
        <div>
            <a href="{{ route('visits.calendar') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-alt"></i> Calendar View
            </a>
            <a href="{{ route('visits.create') }}" class="btn btn-primary">
                Schedule Visit
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number text-primary">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total Visits</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-warning">{{ $stats['scheduled'] ?? 0 }}</div>
            <div class="stat-label">Scheduled</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-info">{{ $stats['in_progress'] ?? 0 }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-success">{{ $stats['completed'] ?? 0 }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-danger">{{ $stats['overdue'] ?? 0 }}</div>
            <div class="stat-label">Overdue</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('visits.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Visit Type</label>
                <select name="visit_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="sales" {{ request('visit_type') == 'sales' ? 'selected' : '' }}>Sales</option>
                    <option value="support" {{ request('visit_type') == 'support' ? 'selected' : '' }}>Support</option>
                    <option value="delivery" {{ request('visit_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="maintenance" {{ request('visit_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="consultation" {{ request('visit_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Visits List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Visits List</h5>
        </div>
        <div class="card-body">
            @if($visits->count() > 0)
                <div class="row">
                    @foreach($visits as $visit)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card visit-card priority-{{ $visit->priority }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $visit->customer_name }}</h6>
                                        <span class="visit-status status-{{ $visit->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt"></i> {{ Str::limit($visit->customer_address, 30) }}
                                    </p>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-clock"></i> {{ $visit->scheduled_at->format('M d, Y H:i') }}
                                    </p>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-tag"></i> {{ ucfirst($visit->visit_type) }}
                                        @if($visit->priority !== 'medium')
                                            <span class="badge bg-{{ $visit->priority_color }} ms-1">{{ ucfirst($visit->priority) }}</span>
                                        @endif
                                    </p>
                                    
                                    @if($visit->purpose)
                                        <p class="small mb-3">{{ Str::limit($visit->purpose, 60) }}</p>
                                    @endif
                                    
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('visits.show', $visit) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($visit->canBeStarted())
                                            <form method="POST" action="{{ route('visits.start', $visit) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($visit->canBeCompleted())
                                            <form method="POST" action="{{ route('visits.complete', $visit) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    ✓
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($visit->canBeRescheduled())
                                            <a href="{{ route('visits.edit', $visit) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-calendar"></i>
                                            </a>
                                        @endif
                                        
                                        @if($visit->canBeCancelled())
                                            <form method="POST" action="{{ route('visits.cancel', $visit) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to cancel this visit?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($visit->canBeDeleted())
                                            <form method="POST" action="{{ route('visits.destroy', $visit) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this visit? This action cannot be undone.')">
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
                
                <!-- Pagination -->
                @if($visits->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $visits->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No visits found</h5>
                    <p class="text-muted">Start by scheduling your first visit.</p>
                    <a href="{{ route('visits.create') }}" class="btn btn-primary">
                        Schedule Visit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection