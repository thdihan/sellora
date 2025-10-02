@extends('layouts.app')

@section('title', 'Visit Details')

@push('styles')
<style>
.visit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}
.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    height: 100%;
}
.info-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}
.info-value {
    color: #212529;
    margin-bottom: 1rem;
}
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    font-size: 0.9rem;
}
.status-scheduled { background-color: #e3f2fd; color: #1976d2; }
.status-in_progress { background-color: #fff3e0; color: #f57c00; }
.status-completed { background-color: #e8f5e8; color: #388e3c; }
.status-cancelled { background-color: #ffebee; color: #d32f2f; }
.status-rescheduled { background-color: #f3e5f5; color: #7b1fa2; }
.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}
.priority-low { background-color: #d4edda; color: #155724; }
.priority-medium { background-color: #d1ecf1; color: #0c5460; }
.priority-high { background-color: #fff3cd; color: #856404; }
.priority-urgent { background-color: #f8d7da; color: #721c24; }
.timeline {
    position: relative;
    padding-left: 2rem;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #007bff;
}
.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}
.attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    border: 1px solid #e9ecef;
}
.attachment-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.file-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: white;
    font-size: 0.9rem;
}
.file-pdf { background-color: #dc3545; }
.file-doc { background-color: #0d6efd; }
.file-image { background-color: #198754; }
.file-default { background-color: #6c757d; }
.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.btn-action {
    min-width: 120px;
}
.location-map {
    height: 200px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    border: 1px solid #dee2e6;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Visit Header -->
    <div class="visit-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="h2 mb-2">{{ $visit->customer_name }}</h1>
                <p class="mb-2 opacity-75">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ $visit->scheduled_at->format('l, F j, Y \a\t g:i A') }}
                </p>
                <div class="d-flex gap-2 align-items-center">
                    <span class="status-badge status-{{ $visit->status }}">
                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                    </span>
                    <span class="priority-badge priority-{{ $visit->priority }}">
                        {{ ucfirst($visit->priority) }} Priority
                    </span>
                    <span class="badge bg-light text-dark">
                        {{ ucfirst($visit->visit_type) }}
                    </span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('visits.index') }}" class="btn btn-light btn-action">
                    Back to List
                </a>
                @if($visit->canBeStarted())
                    <form method="POST" action="{{ route('visits.start', $visit) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-action">
                            <i class="fas fa-play"></i> Start Visit
                        </button>
                    </form>
                @endif
                @if($visit->canBeCompleted())
                    <form method="POST" action="{{ route('visits.complete', $visit) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-action">
                            Complete
                        </button>
                    </form>
                @endif
                @if($visit->canBeRescheduled())
                    <a href="{{ route('visits.edit', $visit) }}" class="btn btn-warning btn-action">
                        <i class="fas fa-calendar"></i> Reschedule
                    </a>
                @endif
                @if($visit->canBeCancelled())
                    <form method="POST" action="{{ route('visits.cancel', $visit) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-action" 
                                onclick="return confirm('Are you sure you want to cancel this visit?')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </form>
                @endif
                @if($visit->canBeDeleted())
                    <form method="POST" action="{{ route('visits.destroy', $visit) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-action" 
                                onclick="return confirm('Are you sure you want to delete this visit? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-6">
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-user text-primary me-2"></i>
                    Customer Information
                </h5>
                
                <div class="info-label">Name</div>
                <div class="info-value">{{ $visit->customer_name }}</div>
                
                @if($visit->customer_phone)
                    <div class="info-label">Phone</div>
                    <div class="info-value">
                        <a href="tel:{{ $visit->customer_phone }}" class="text-decoration-none">
                            <i class="fas fa-phone me-1"></i> {{ $visit->customer_phone }}
                        </a>
                    </div>
                @endif
                
                @if($visit->customer_email)
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        <a href="mailto:{{ $visit->customer_email }}" class="text-decoration-none">
                            <i class="fas fa-envelope me-1"></i> {{ $visit->customer_email }}
                        </a>
                    </div>
                @endif
                
                <div class="info-label">Address</div>
                <div class="info-value">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $visit->customer_address }}
                </div>
            </div>
        </div>

        <!-- Visit Details -->
        <div class="col-lg-6">
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-check text-primary me-2"></i>
                    Visit Details
                </h5>
                
                <div class="info-label">Scheduled Date & Time</div>
                <div class="info-value">
                    <i class="fas fa-clock me-1"></i>
                    {{ $visit->scheduled_at->format('l, F j, Y \a\t g:i A') }}
                </div>
                
                <div class="info-label">Estimated Duration</div>
                <div class="info-value">
                    <i class="fas fa-hourglass-half me-1"></i>
                    {{ $visit->estimated_duration }} hour(s)
                </div>
                
                @if($visit->actual_start_time)
                    <div class="info-label">Actual Start Time</div>
                    <div class="info-value">
                        <i class="fas fa-play me-1"></i>
                        {{ $visit->actual_start_time->format('M j, Y \a\t g:i A') }}
                    </div>
                @endif
                
                @if($visit->actual_end_time)
                    <div class="info-label">Actual End Time</div>
                    <div class="info-value">
                        <i class="fas fa-stop me-1"></i>
                        {{ $visit->actual_end_time->format('M j, Y \a\t g:i A') }}
                    </div>
                    
                    <div class="info-label">Actual Duration</div>
                    <div class="info-value">
                        <i class="fas fa-clock me-1"></i>
                        {{ $visit->getFormattedDuration() }}
                    </div>
                @endif
                
                @if($visit->isOverdue())
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This visit is overdue!
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Purpose & Notes -->
        <div class="col-lg-8">
            @if($visit->purpose)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-bullseye text-primary me-2"></i>
                        Purpose of Visit
                    </h5>
                    <p class="mb-0">{{ $visit->purpose }}</p>
                </div>
            @endif
            
            @if($visit->notes)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-sticky-note text-primary me-2"></i>
                        Notes
                    </h5>
                    <p class="mb-0">{{ $visit->notes }}</p>
                </div>
            @endif
            
            @if($visit->outcome)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-flag-checkered text-primary me-2"></i>
                        Visit Outcome
                    </h5>
                    <p class="mb-0">{{ $visit->outcome }}</p>
                </div>
            @endif
            
            @if($visit->cancellation_reason)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Cancellation Reason
                    </h5>
                    <p class="mb-0">{{ $visit->cancellation_reason }}</p>
                </div>
            @endif
        </div>

        <!-- Location & Follow-up -->
        <div class="col-lg-4">
            @if($visit->location_address || ($visit->latitude && $visit->longitude))
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Location
                    </h5>
                    
                    @if($visit->location_address)
                        <div class="info-label">Specific Address</div>
                        <div class="info-value">{{ $visit->location_address }}</div>
                    @endif
                    
                    @if($visit->latitude && $visit->longitude)
                        <div class="info-label">Coordinates</div>
                        <div class="info-value">
                            {{ $visit->latitude }}, {{ $visit->longitude }}
                            <a href="https://maps.google.com/?q={{ $visit->latitude }},{{ $visit->longitude }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-external-link-alt"></i> View on Map
                            </a>
                        </div>
                        
                        <div class="location-map mt-3">
                            <div class="text-center">
                                <i class="fas fa-map fa-2x mb-2"></i>
                                <p class="mb-0">Map integration would go here</p>
                                <small>Click coordinates above to view on Google Maps</small>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            
            @if($visit->requires_follow_up)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Follow-up Required
                    </h5>
                    
                    @if($visit->follow_up_date)
                        <div class="info-label">Follow-up Date</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $visit->follow_up_date->format('M j, Y \a\t g:i A') }}
                        </div>
                        
                        @if($visit->follow_up_date->isPast())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Follow-up is overdue!
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Follow-up date not set</p>
                    @endif
                    
                    <a href="{{ route('visits.create') }}?follow_up={{ $visit->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Schedule Follow-up
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Attachments -->
    @if($visit->attachments && count(json_decode($visit->attachments, true)) > 0)
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-paperclip text-primary me-2"></i>
                Attachments
            </h5>
            
            <div class="row">
                @foreach(json_decode($visit->attachments, true) as $attachment)
                    @php
                        $extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);
                        $iconClass = match(strtolower($extension)) {
                            'pdf' => 'file-pdf',
                            'doc', 'docx' => 'file-doc',
                            'jpg', 'jpeg', 'png', 'gif' => 'file-image',
                            default => 'file-default'
                        };
                        $icon = match(strtolower($extension)) {
                            'pdf' => 'fas fa-file-pdf',
                            'doc', 'docx' => 'fas fa-file-word',
                            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
                            default => 'fas fa-file'
                        };
                    @endphp
                    
                    <div class="col-md-6 mb-3">
                        <div class="attachment-item">
                            <div class="attachment-info">
                                <div class="file-icon {{ $iconClass }}">
                                    <i class="{{ $icon }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $attachment['name'] }}</div>
                                    <small class="text-muted">
                                        {{ number_format($attachment['size'] / 1024, 1) }} KB
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ Storage::url($attachment['path']) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Visit Timeline -->
    <div class="info-card">
        <h5 class="mb-3">
            <i class="fas fa-history text-primary me-2"></i>
            Visit Timeline
        </h5>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Visit Scheduled</h6>
                            <p class="mb-0 text-muted">Visit was scheduled for {{ $visit->scheduled_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                        <small class="text-muted">{{ $visit->created_at->format('M j, g:i A') }}</small>
                    </div>
                </div>
            </div>
            
            @if($visit->actual_start_time)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Visit Started</h6>
                                <p class="mb-0 text-muted">Visit was started by {{ $visit->user->name }}</p>
                            </div>
                            <small class="text-muted">{{ $visit->actual_start_time->format('M j, g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($visit->actual_end_time)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Visit Completed</h6>
                                <p class="mb-0 text-muted">Visit was completed successfully</p>
                            </div>
                            <small class="text-muted">{{ $visit->actual_end_time->format('M j, g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($visit->status === 'cancelled')
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Visit Cancelled</h6>
                                <p class="mb-0 text-muted">
                                    @if($visit->cancellation_reason)
                                        Reason: {{ $visit->cancellation_reason }}
                                    @else
                                        Visit was cancelled
                                    @endif
                                </p>
                            </div>
                            <small class="text-muted">{{ $visit->updated_at->format('M j, g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($visit->rescheduled_from)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Visit Rescheduled</h6>
                                <p class="mb-0 text-muted">
                                    Rescheduled from {{ $visit->rescheduled_from->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <small class="text-muted">{{ $visit->updated_at->format('M j, g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection