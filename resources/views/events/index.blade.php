@extends('layouts.app')

@section('content')
<style>
    .event-card {
        transition: all 0.3s ease;
        border-left: 4px solid #e9ecef;
    }
    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .event-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .priority-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    .status-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .stats-card.upcoming {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stats-card.today {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stats-card.completed {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .event-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .event-location {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Events Calendar</h1>
            <p class="text-muted mb-0">Manage your events and schedule</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary">
                📅 Calendar View
            </a>
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                Create Event
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <small>Total Events</small>
                    </div>
                    <span style="font-size: 2rem; opacity: 0.75;">📅</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('events.upcoming') }}" class="text-decoration-none">
                <div class="stats-card upcoming">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $stats['upcoming'] }}</h4>
                            <small>Upcoming</small>
                        </div>
                        <span style="font-size: 2rem; opacity: 0.75;">🕒</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="stats-card today">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['today'] }}</h4>
                        <small>Today</small>
                    </div>
                    <span style="font-size: 2rem; opacity: 0.75;">📅</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card completed">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                        <small>Completed</small>
                    </div>
                    <span style="font-size: 2rem; opacity: 0.75;">✅</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('events.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search events..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="meeting" {{ request('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                    <option value="appointment" {{ request('type') == 'appointment' ? 'selected' : '' }}>Appointment</option>
                    <option value="deadline" {{ request('type') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                    <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                    <option value="personal" {{ request('type') == 'personal' ? 'selected' : '' }}>Personal</option>
                    <option value="holiday" {{ request('type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>Postponed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    🔍
                </button>
                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                    ❌
                </a>
            </div>
        </form>
    </div>

    <!-- Events List -->
    @if($events->count() > 0)
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card event-card h-100" style="border-left-color: {{ $event->getEventColor() }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ $event->title }}</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        ⋮
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('events.show', $event) }}">👁️ View</a></li>
                                        @can('update', $event)
                                            <li><a class="dropdown-item" href="{{ route('events.edit', $event) }}">Edit</a></li>
                                        @endcan
                                        <li><a class="dropdown-item" href="{{ route('events.duplicate', $event) }}">📋 Duplicate</a></li>
                                        @can('delete', $event)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <span class="badge event-type-badge" style="background-color: {{ $event->getEventColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                </span>
                                <span class="badge priority-badge 
                                    @if($event->priority == 'urgent') bg-danger
                                    @elseif($event->priority == 'high') bg-warning
                                    @elseif($event->priority == 'medium') bg-info
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($event->priority) }}
                                </span>
                                <span class="badge status-badge 
                                    @if($event->status == 'completed') bg-success
                                    @elseif($event->status == 'in_progress') bg-primary
                                    @elseif($event->status == 'cancelled') bg-danger
                                    @elseif($event->status == 'postponed') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                </span>
                            </div>
                            
                            @if($event->description)
                                <p class="card-text text-muted small mb-2">{{ Str::limit($event->description, 80) }}</p>
                            @endif
                            
                            <div class="event-time mb-1">
                                🕒
                                {{ $event->formatted_start_date }}
                                @if(!$event->is_all_day && $event->start_time != $event->end_time)
                                    - {{ $event->formatted_end_date }}
                                @endif
                            </div>
                            
                            @if($event->location)
                                <div class="event-location mb-2">
                                    📍
                                    {{ $event->location }}
                                </div>
                            @endif
                            
                            @if($event->isRecurring())
                                <div class="text-muted small mb-2">
                                    🔄
                                    Recurring {{ $event->recurring_type }}
                                </div>
                            @endif
                            
                            @if($event->attendees && is_array($event->attendees) && count($event->attendees) > 0)
                                <div class="text-muted small mb-2">
                                    👥
                                    {{ is_array($event->attendees) ? count($event->attendees) : 0 }} attendee(s)
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    @if($event->is_past)
                                        📜 Past
                                    @elseif($event->is_today)
                                        📅 Today
                                    @else
                                        📅 Upcoming
                                    @endif
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">
                                        👁️
                                    </a>
                                    @can('update', $event)
                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                                            Edit
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
        <div class="d-flex justify-content-center mt-4">
            @if($events->hasPages())
                {{ $events->appends(request()->query())->links('vendor.pagination.custom-3d') }}
            @endif
        </div>
    @else
        <div class="text-center py-5">
            <span style="font-size: 4rem;" class="text-muted mb-3">📅</span>
            <h4 class="text-muted">No Events Found</h4>
            <p class="text-muted mb-4">You haven't created any events yet or no events match your filters.</p>
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                Create Your First Event
            </a>
        </div>
    @endif
</div>

<script>
// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('select[name="type"], select[name="status"], select[name="priority"]');
    const dateInputs = document.querySelectorAll('input[name="date_from"], input[name="date_to"]');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection