@extends('layouts.app')

@section('title', 'Visits Calendar')

@push('styles')
<link href="{{ asset('assets/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">
<style>
.calendar-header {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}
.calendar-container {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.fc-event {
    border: none !important;
    padding: 2px 4px;
    font-size: 0.85rem;
}
.fc-event-scheduled {
    background-color: #2196f3 !important;
    color: white !important;
}
.fc-event-in_progress {
    background-color: #ff9800 !important;
    color: white !important;
}
.fc-event-completed {
    background-color: #4caf50 !important;
    color: white !important;
}
.fc-event-cancelled {
    background-color: #f44336 !important;
    color: white !important;
}
.fc-event-rescheduled {
    background-color: #9c27b0 !important;
    color: white !important;
}
.legend {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}
.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}
.quick-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="calendar-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Visits Calendar</h1>
                <p class="text-muted mb-0">View and manage visits in calendar format</p>
            </div>
            <div class="quick-actions">
                <a href="{{ route('visits.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> List View
                </a>
                <a href="{{ route('visits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Schedule Visit
                </a>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="legend">
            <span class="fw-semibold me-2">Status Legend:</span>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #2196f3;"></div>
                <span>Scheduled</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #ff9800;"></div>
                <span>In Progress</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #4caf50;"></div>
                <span>Completed</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #f44336;"></div>
                <span>Cancelled</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #9c27b0;"></div>
                <span>Rescheduled</span>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <div id="calendar"></div>
    </div>
</div>

<!-- Visit Details Modal -->
<div class="modal fade" id="visitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="visitDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <div id="visitActions"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/fullcalendar/fullcalendar.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const visitModal = new bootstrap.Modal(document.getElementById('visitModal'));
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch(`{{ route('visits.calendar') }}?ajax=1&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    const events = data.map(visit => ({
                        id: visit.id,
                        title: `${visit.customer_name} - ${visit.visit_type}`,
                        start: visit.scheduled_at,
                        end: visit.estimated_end_time,
                        className: `fc-event-${visit.status}`,
                        extendedProps: {
                            visit: visit
                        }
                    }));
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showVisitDetails(info.event.extendedProps.visit);
        },
        dateClick: function(info) {
            // Redirect to create visit with pre-filled date
            window.location.href = `{{ route('visits.create') }}?date=${info.dateStr}`;
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', 
                `${info.event.extendedProps.visit.customer_name}\n` +
                `Type: ${info.event.extendedProps.visit.visit_type}\n` +
                `Status: ${info.event.extendedProps.visit.status}\n` +
                `Time: ${new Date(info.event.extendedProps.visit.scheduled_at).toLocaleTimeString()}`
            );
        }
    });
    
    calendar.render();
    
    function showVisitDetails(visit) {
        const statusColors = {
            'scheduled': 'primary',
            'in_progress': 'warning',
            'completed': 'success',
            'cancelled': 'danger',
            'rescheduled': 'secondary'
        };
        
        const priorityColors = {
            'low': 'success',
            'medium': 'info',
            'high': 'warning',
            'urgent': 'danger'
        };
        
        const scheduledDate = new Date(visit.scheduled_at);
        const duration = visit.estimated_duration || 1;
        const endTime = new Date(scheduledDate.getTime() + (duration * 60 * 60 * 1000));
        
        document.getElementById('visitDetails').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <p><strong>Name:</strong> ${visit.customer_name}</p>
                    <p><strong>Phone:</strong> ${visit.customer_phone || 'N/A'}</p>
                    <p><strong>Email:</strong> ${visit.customer_email || 'N/A'}</p>
                    <p><strong>Address:</strong> ${visit.customer_address}</p>
                </div>
                <div class="col-md-6">
                    <h6>Visit Details</h6>
                    <p><strong>Type:</strong> <span class="badge bg-info">${visit.visit_type}</span></p>
                    <p><strong>Status:</strong> <span class="badge bg-${statusColors[visit.status]}">${visit.status.replace('_', ' ')}</span></p>
                    <p><strong>Priority:</strong> <span class="badge bg-${priorityColors[visit.priority]}">${visit.priority}</span></p>
                    <p><strong>Scheduled:</strong> ${scheduledDate.toLocaleString()}</p>
                    <p><strong>Duration:</strong> ${duration} hour(s)</p>
                </div>
            </div>
            ${visit.purpose ? `
                <div class="mt-3">
                    <h6>Purpose</h6>
                    <p>${visit.purpose}</p>
                </div>
            ` : ''}
            ${visit.notes ? `
                <div class="mt-3">
                    <h6>Notes</h6>
                    <p>${visit.notes}</p>
                </div>
            ` : ''}
        `;
        
        // Generate action buttons based on visit status
        let actions = `<a href="/visits/${visit.id}" class="btn btn-primary">View Details</a>`;
        
        if (visit.status === 'scheduled') {
            actions += ` <button class="btn btn-success" onclick="updateVisitStatus(${visit.id}, 'start')">Start Visit</button>`;
            actions += ` <a href="/visits/${visit.id}/edit" class="btn btn-warning">Reschedule</a>`;
            actions += ` <button class="btn btn-danger" onclick="updateVisitStatus(${visit.id}, 'cancel')">Cancel</button>`;
        } else if (visit.status === 'in_progress') {
            actions += ` <button class="btn btn-primary" onclick="updateVisitStatus(${visit.id}, 'complete')">Complete Visit</button>`;
        }
        
        document.getElementById('visitActions').innerHTML = actions;
        visitModal.show();
    }
    
    // Global function for status updates
    window.updateVisitStatus = function(visitId, action) {
        if (action === 'cancel' && !confirm('Are you sure you want to cancel this visit?')) {
            return;
        }
        
        fetch(`/visits/${visitId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                visitModal.hide();
                calendar.refetchEvents();
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.calendar-header'));
                
                // Auto dismiss after 3 seconds
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the visit.');
        });
    };
});
</script>
@endpush