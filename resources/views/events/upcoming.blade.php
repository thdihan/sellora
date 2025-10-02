@extends('layouts.app')

@section('title', 'Upcoming Events')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            🕒
                            Upcoming Events
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                                <li class="breadcrumb-item active">Upcoming</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total_upcoming'] }}</h4>
                                            <small>Total Upcoming</small>
                                        </div>
                                        <span class="opacity-75" style="font-size: 2rem;">🕒</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['today'] }}</h4>
                                            <small>Today</small>
                                        </div>
                                        <span class="opacity-75" style="font-size: 2rem;">📅</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['this_week'] }}</h4>
                                            <small>This Week</small>
                                        </div>
                                        <span class="opacity-75" style="font-size: 2rem;">📅</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['this_month'] }}</h4>
                                            <small>This Month</small>
                                        </div>
                                        <span class="opacity-75" style="font-size: 2rem;">📅</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Actions -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('events.upcoming') }}">
                                <div class="input-group">
                                    <span class="input-group-text">🔍</span>
                                    <input type="text" class="form-control" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Search upcoming events...">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        Search
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('events.upcoming') }}" class="btn btn-outline-danger">
                                            Clear
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                ➕ Create Event
                            </a>
                            <a href="{{ route('events.calendar') }}" class="btn btn-outline-secondary">
                                📅 Calendar View
                            </a>
                        </div>
                    </div>

                    <!-- Upcoming Events List -->
                    @if($events->count() > 0)
                        <div class="row">
                            @foreach($events as $event)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 event-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="badge badge-{{ $event->priority === 'urgent' ? 'danger' : ($event->priority === 'high' ? 'warning' : ($event->priority === 'medium' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($event->priority) }}
                                            </span>
                                            <span class="badge badge-outline-primary">
                                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $event->title }}</h5>
                                            @if($event->description)
                                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                            @endif
                                            
                                            <div class="event-details">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="text-primary me-2">📅</span>
                                                    <span>{{ $event->start_date->format('M j, Y') }}</span>
                                                </div>
                                                
                                                @if(!$event->is_all_day)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-primary me-2">🕒</span>
                                                        <span>{{ $event->start_time->format('g:i A') }}</span>
                                                        @if($event->end_time)
                                                            - {{ $event->end_time->format('g:i A') }}
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-primary me-2">🕒</span>
                                                        <span>All Day</span>
                                                    </div>
                                                @endif
                                                
                                                @if($event->location)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-primary me-2">📍</span>
                                                        <span>{{ Str::limit($event->location, 30) }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex align-items-center">
                                                    <span class="text-primary me-2">ℹ️</span>
                                                    <span class="badge badge-{{ $event->status === 'completed' ? 'success' : ($event->status === 'cancelled' ? 'danger' : 'primary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                                    👁️ View
                                                </a>
                                                <div>
                                                    @can('update', $event)
                                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-secondary">
                                                            ✏️ Edit
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($events->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $events->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <span class="text-muted mb-3" style="font-size: 3rem;">📅</span>
                            <h5 class="text-muted">No upcoming events found</h5>
                            @if(request('search'))
                                <p class="text-muted">Try adjusting your search criteria or <a href="{{ route('events.upcoming') }}">view all upcoming events</a>.</p>
                            @else
                                <p class="text-muted">Create your first event to get started!</p>
                                <a href="{{ route('events.create') }}" class="btn btn-primary">
                                    ➕ Create Event
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// TODO: Implement upcoming events loading logic
document.addEventListener('DOMContentLoaded', function() {
    // Load upcoming events via AJAX
    // This should integrate with the EventController@upcoming method
    console.log('Upcoming Events page loaded - TODO: Implement event loading');
});
</script>
@endsection