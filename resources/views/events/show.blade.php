@extends('layouts.app')

@section('content')
<style>
    .event-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .event-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.1);
        z-index: 1;
    }
    .event-header-content {
        position: relative;
        z-index: 2;
    }
    .event-color-indicator {
        width: 6px;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        border-radius: 3px 0 0 3px;
    }
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        border-left: 4px solid #007bff;
        position: relative;
    }
    .info-card h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .info-card h5 i {
        margin-right: 0.5rem;
        color: #007bff;
    }
    .priority-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    .attendee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .attachment-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    .attachment-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .timeline-item {
        border-left: 3px solid #007bff;
        padding-left: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 12px;
        height: 12px;
        background: #007bff;
        border-radius: 50%;
    }
    .action-buttons {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    .recurring-info {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .event-color-preview {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #dee2e6;
    }
</style>

<div class="container-fluid">
    <!-- Event Header -->
    <div class="event-header" @if($event->color) style="background: linear-gradient(135deg, {{ $event->color }} 0%, {{ $event->color }}dd 100%);" @endif>
        @if($event->color)
            <div class="event-color-indicator" style="background-color: {{ $event->color }};"></div>
        @endif
        <div class="event-header-content">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h2 mb-2">{{ $event->title }}</h1>
                    <p class="mb-3 opacity-75">{{ $event->description ?: 'No description provided' }}</p>
                    <div class="d-flex flex-wrap align-items-center">
                        <span class="badge bg-light text-dark me-2 mb-2">
                            <i class="fas fa-tag me-1"></i>{{ ucfirst($event->event_type) }}
                        </span>
                        @if($event->priority)
                            <span class="priority-badge me-2 mb-2 
                                @if($event->priority === 'low') bg-secondary
                                @elseif($event->priority === 'medium') bg-info
                                @elseif($event->priority === 'high') bg-warning
                                @elseif($event->priority === 'urgent') bg-danger
                                @endif">
                                <i class="fas fa-flag me-1"></i>{{ ucfirst($event->priority) }} Priority
                            </span>
                        @endif
                        <span class="status-badge me-2 mb-2 
                            @if($event->status === 'scheduled') bg-primary
                            @elseif($event->status === 'in_progress') bg-warning
                            @elseif($event->status === 'completed') bg-success
                            @elseif($event->status === 'cancelled') bg-danger
                            @endif">
                            <i class="fas fa-circle me-1"></i>{{ ucfirst($event->status) }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="btn-group" role="group">
                        @can('update', $event)
                            <a href="{{ route('events.edit', $event) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        @endcan
                        <button type="button" class="btn btn-light btn-sm" onclick="duplicateEvent()">
                            <i class="fas fa-copy me-1"></i>Duplicate
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportEvent()"><i class="fas fa-download me-2"></i>Export</a></li>
                                <li><a class="dropdown-item" href="#" onclick="shareEvent()"><i class="fas fa-share me-2"></i>Share</a></li>
                                @can('delete', $event)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteEvent()"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                @endcan
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Events
            </a>
            <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar me-1"></i> Calendar View
            </a>
        </div>
        <div>
            @if($event->status === 'scheduled')
                <button class="btn btn-warning me-2" onclick="updateStatus('in_progress')">
                    <i class="fas fa-play me-1"></i> Start Event
                </button>
            @endif
            @if($event->status === 'in_progress')
                <button class="btn btn-success me-2" onclick="updateStatus('completed')">
                    <i class="fas fa-check me-1"></i> Mark Complete
                </button>
            @endif
            @if($event->status !== 'cancelled')
                <button class="btn btn-outline-danger" onclick="updateStatus('cancelled')">
                    <i class="fas fa-times me-1"></i> Cancel Event
                </button>
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Date & Time Information -->
            <div class="info-card">
                <h5><i class="fas fa-clock"></i>Date & Time</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="timeline-item">
                            <h6 class="mb-1">Start</h6>
                            <p class="mb-0">
                                <strong>{{ $event->start_date->format('l, F j, Y') }}</strong><br>
                                @if(!$event->is_all_day && $event->start_time)
                                    <span class="text-muted">{{ $event->start_time->format('g:i A') }}</span>
                                @else
                                    <span class="text-muted">All Day</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="timeline-item">
                            <h6 class="mb-1">End</h6>
                            <p class="mb-0">
                                <strong>{{ $event->end_date->format('l, F j, Y') }}</strong><br>
                                @if(!$event->is_all_day && $event->end_time)
                                    <span class="text-muted">{{ $event->end_time->format('g:i A') }}</span>
                                @else
                                    <span class="text-muted">All Day</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($event->duration)
                    <div class="mt-3 p-3 bg-light rounded">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Duration:</strong> {{ $event->duration }}
                    </div>
                @endif
                
                @if($event->recurring_type && $event->recurring_type !== 'none')
                    <div class="recurring-info">
                        <h6><i class="fas fa-redo me-2"></i>Recurring Event</h6>
                        <p class="mb-2">
                            <strong>Repeats:</strong> {{ ucfirst($event->recurring_type) }}
                            @if($event->recurring_end_date)
                                until {{ $event->recurring_end_date->format('F j, Y') }}
                            @endif
                        </p>
                        @if($event->recurring_days)
                            <p class="mb-0">
                                <strong>On:</strong> 
                                @php
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $recurringDays = json_decode($event->recurring_days, true) ?: [];
                                @endphp
                                {{ implode(', ', array_map(fn($day) => $days[$day], $recurringDays)) }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Location -->
            @if($event->location)
                <div class="info-card">
                    <h5><i class="fas fa-map-marker-alt"></i>Location</h5>
                    <p class="mb-0">{{ $event->location }}</p>
                    <a href="https://maps.google.com/?q={{ urlencode($event->location) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-external-link-alt me-1"></i>View on Map
                    </a>
                </div>
            @endif
            
            <!-- Description & Notes -->
            @if($event->description || $event->notes)
                <div class="info-card">
                    <h5><i class="fas fa-file-text"></i>Details</h5>
                    @if($event->description)
                        <div class="mb-3">
                            <h6>Description</h6>
                            <p>{{ $event->description }}</p>
                        </div>
                    @endif
                    @if($event->notes)
                        <div>
                            <h6>Additional Notes</h6>
                            <p class="mb-0">{{ $event->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Attendees -->
            @if($event->attendees && count(json_decode($event->attendees, true) ?: []) > 0)
                <div class="info-card">
                    <h5><i class="fas fa-users"></i>Attendees ({{ count(json_decode($event->attendees, true)) }})</h5>
                    <div class="d-flex flex-wrap">
                        @foreach(json_decode($event->attendees, true) as $attendee)
                            <div class="d-flex align-items-center me-3 mb-2">
                                <div class="attendee-avatar">
                                    {{ strtoupper(substr($attendee, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $attendee }}</div>
                                    <small class="text-muted">Invited</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Attachments -->
            @if($event->attachments && count(json_decode($event->attachments, true) ?: []) > 0)
                <div class="info-card">
                    <h5><i class="fas fa-paperclip"></i>Attachments ({{ count(json_decode($event->attachments, true)) }})</h5>
                    @foreach(json_decode($event->attachments, true) as $attachment)
                        <div class="attachment-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">{{ basename($attachment) }}</div>
                                        <small class="text-muted">{{ $attachment }}</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('events.download', ['event' => $event, 'file' => basename($attachment)]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="info-card action-buttons">
                <h5><i class="fas fa-bolt"></i>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Event
                    </a>
                    <button class="btn btn-outline-secondary" onclick="duplicateEvent()">
                        <i class="fas fa-copy me-2"></i>Duplicate Event
                    </button>
                    <button class="btn btn-outline-info" onclick="shareEvent()">
                        <i class="fas fa-share me-2"></i>Share Event
                    </button>
                    <button class="btn btn-outline-success" onclick="exportEvent()">
                        <i class="fas fa-download me-2"></i>Export to Calendar
                    </button>
                    <hr>
                    <button class="btn btn-outline-danger" onclick="deleteEvent()">
                        <i class="fas fa-trash me-2"></i>Delete Event
                    </button>
                </div>
            </div>
            
            <!-- Event Details -->
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i>Event Details</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted d-block">Created by</small>
                        <strong>{{ $event->creator->name ?? 'Unknown' }}</strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Created on</small>
                        <strong>{{ $event->created_at->format('M j, Y g:i A') }}</strong>
                    </div>
                    @if($event->updated_at != $event->created_at)
                        <div class="col-12">
                            <small class="text-muted d-block">Last updated</small>
                            <strong>{{ $event->updated_at->format('M j, Y g:i A') }}</strong>
                        </div>
                    @endif
                    @if($event->color)
                        <div class="col-12">
                            <small class="text-muted d-block">Color</small>
                            <div class="d-flex align-items-center">
                                <div class="event-color-preview" style="background-color: {{ $event->color }};"></div>
                                <span>{{ $event->color }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Reminder -->
            @if($event->reminder_minutes)
                <div class="info-card">
                    <h5><i class="fas fa-bell"></i>Reminder</h5>
                    <p class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        @if($event->reminder_minutes < 60)
                            {{ $event->reminder_minutes }} minutes before
                        @elseif($event->reminder_minutes < 1440)
                            {{ $event->reminder_minutes / 60 }} hour(s) before
                        @else
                            {{ $event->reminder_minutes / 1440 }} day(s) before
                        @endif
                    </p>
                    @if($event->reminder_sent_at)
                        <small class="text-success">
                            <i class="fas fa-check me-1"></i>Reminder sent on {{ $event->reminder_sent_at->format('M j, Y g:i A') }}
                        </small>
                    @else
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Reminder pending
                        </small>
                    @endif
                </div>
            @endif
            
            <!-- Related Events -->
            @if($relatedEvents && $relatedEvents->count() > 0)
                <div class="info-card">
                    <h5><i class="fas fa-link"></i>Related Events</h5>
                    @foreach($relatedEvents as $related)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <div class="fw-bold">{{ $related->title }}</div>
                                <small class="text-muted">{{ $related->start_date->format('M j, Y') }}</small>
                            </div>
                            <a href="{{ route('events.show', $related) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('delete', $event)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
                @if($event->recurring_type && $event->recurring_type !== 'none')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This is a recurring event. Deleting it will remove all future occurrences.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">Delete Event</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

<script>
function updateStatus(status) {
    if (confirm(`Are you sure you want to ${status.replace('_', ' ')} this event?`)) {
        fetch(`{{ route('events.update-status', $event) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating event status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating event status');
        });
    }
}

function duplicateEvent() {
    if (confirm('Create a copy of this event?')) {
        fetch(`{{ route('events.duplicate', $event) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Error duplicating event');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error duplicating event');
        });
    }
}

function deleteEvent() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function shareEvent() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: '{{ $event->title }}',
            text: '{{ $event->description }}',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('Event link copied to clipboard!');
        });
    }
}

function exportEvent() {
    // Create ICS file content
    const event = {
        title: '{{ $event->title }}',
        description: '{{ $event->description }}',
        location: '{{ $event->location }}',
        startDate: '{{ $event->start_date->format('Ymd') }}',
        endDate: '{{ $event->end_date->format('Ymd') }}',
        startTime: '{{ $event->start_time ? $event->start_time->format('His') : '' }}',
        endTime: '{{ $event->end_time ? $event->end_time->format('His') : '' }}',
        isAllDay: {{ $event->is_all_day ? 'true' : 'false' }}
    };
    
    let icsContent = 'BEGIN:VCALENDAR\n';
    icsContent += 'VERSION:2.0\n';
    icsContent += 'PRODID:-//Sellora//Event//EN\n';
    icsContent += 'BEGIN:VEVENT\n';
    icsContent += `UID:${Date.now()}@sellora.com\n`;
    icsContent += `SUMMARY:${event.title}\n`;
    
    if (event.isAllDay) {
        icsContent += `DTSTART;VALUE=DATE:${event.startDate}\n`;
        icsContent += `DTEND;VALUE=DATE:${event.endDate}\n`;
    } else {
        icsContent += `DTSTART:${event.startDate}T${event.startTime}\n`;
        icsContent += `DTEND:${event.endDate}T${event.endTime}\n`;
    }
    
    if (event.description) {
        icsContent += `DESCRIPTION:${event.description}\n`;
    }
    if (event.location) {
        icsContent += `LOCATION:${event.location}\n`;
    }
    
    icsContent += 'END:VEVENT\n';
    icsContent += 'END:VCALENDAR';
    
    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${event.title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.ics`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection