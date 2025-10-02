@extends('layouts.app')

@section('content')
<style>
    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .calendar-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 0.5rem;
    }
    .quick-actions {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        border: none !important;
    }
    .fc-event:hover {
        opacity: 0.8;
    }
    .event-modal .modal-body {
        padding: 1.5rem;
    }
    .event-detail-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .event-detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .priority-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<!-- Include FullCalendar CSS -->
<link href="{{ asset('assets/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" />

<div class="container-fluid">
    <!-- Header -->
    <div class="calendar-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Events Calendar</h1>
                <p class="mb-0 opacity-75">Visual calendar view of your events</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('events.index') }}" class="btn btn-light">
                    🔍 List View
                </a>
                <a href="{{ route('events.create') }}" class="btn btn-warning">
                    📅 Create Event
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calendar -->
        <div class="col-lg-9">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h6 class="mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="goToToday()">
                        📅 Today
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="changeView('dayGridMonth')">
                        📅 Month View
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="changeView('timeGridWeek')">
                        📅 Week View
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="changeView('timeGridDay')">
                        📅 Day View
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="changeView('listWeek')">
                        📋 Agenda
                    </button>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Event Types</h6>
                </div>
                <div class="card-body">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #007bff;"></div>
                        <span>Meeting</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <span>Appointment</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Deadline</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span>Reminder</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6f42c1;"></div>
                        <span>Personal</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fd7e14;"></div>
                        <span>Holiday</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6c757d;"></div>
                        <span>Other</span>
                    </div>
                </div>
            </div>
            
            <!-- Mini Calendar Stats -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">This Month</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Events:</span>
                        <span id="monthlyTotal">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed:</span>
                        <span id="monthlyCompleted">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Upcoming:</span>
                        <span id="monthlyUpcoming">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="editEventBtn" class="btn btn-primary" style="display: none;">✏️ Edit Event</a>
                <div class="dropdown" id="statusDropdown" style="display: none;">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Status
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="updateEventStatus('scheduled')">Scheduled</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateEventStatus('in_progress')">In Progress</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateEventStatus('completed')">Completed</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateEventStatus('cancelled')">Cancelled</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateEventStatus('postponed')">Postponed</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include FullCalendar JS -->
<script src="{{ asset('assets/fullcalendar/fullcalendar.min.js') }}"></script>

<script>
let calendar;
let currentEvent = null;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch(`{{ route('events.getEvents') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                    updateMonthlySummary(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            // Redirect to create event with pre-filled date
            window.location.href = `{{ route('events.create') }}?date=${info.dateStr}`;
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', info.event.title);
            
            // Add priority indicator
            if (info.event.extendedProps.priority === 'urgent') {
                info.el.style.border = '2px solid #dc3545';
            } else if (info.event.extendedProps.priority === 'high') {
                info.el.style.border = '2px solid #ffc107';
            }
        },
        eventMouseEnter: function(info) {
            // Show tooltip on hover
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip bs-tooltip-top show';
            tooltip.innerHTML = `
                <div class="tooltip-arrow"></div>
                <div class="tooltip-inner">
                    <strong>${info.event.title}</strong><br>
                    ${info.event.extendedProps.description || ''}<br>
                    <small>${info.event.extendedProps.location || ''}</small>
                </div>
            `;
            document.body.appendChild(tooltip);
            
            const rect = info.el.getBoundingClientRect();
            tooltip.style.position = 'absolute';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            tooltip.style.zIndex = '9999';
            
            info.el._tooltip = tooltip;
        },
        eventMouseLeave: function(info) {
            if (info.el._tooltip) {
                document.body.removeChild(info.el._tooltip);
                delete info.el._tooltip;
            }
        }
    });
    
    calendar.render();
});

function showEventDetails(event) {
    currentEvent = event;
    
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    const modalTitle = document.getElementById('eventModalTitle');
    const modalBody = document.getElementById('eventModalBody');
    const editBtn = document.getElementById('editEventBtn');
    const statusDropdown = document.getElementById('statusDropdown');
    
    modalTitle.textContent = event.title;
    editBtn.href = `/events/${event.id}/edit`;
    
    // Check permissions via AJAX
    fetch(`/events/${event.id}/permissions`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(permissions => {
        // Show/hide edit button based on update permission
        if (permissions.canUpdate) {
            editBtn.style.display = 'inline-block';
        } else {
            editBtn.style.display = 'none';
        }
        
        // Show/hide status dropdown based on update permission
        if (permissions.canUpdate) {
            statusDropdown.style.display = 'inline-block';
        } else {
            statusDropdown.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error checking permissions:', error);
        // Hide buttons by default if permission check fails
        editBtn.style.display = 'none';
        statusDropdown.style.display = 'none';
    });
    
    modalBody.innerHTML = `
        <div class="event-detail-item">
            <h6>ℹ️ Description</h6>
            <p class="mb-0">${event.extendedProps.description || 'No description provided'}</p>
        </div>
        
        <div class="event-detail-item">
            <h6>🕒 Time</h6>
            <p class="mb-0">
                ${event.allDay ? 'All Day' : formatEventTime(event.start, event.end)}
            </p>
        </div>
        
        ${event.extendedProps.location ? `
        <div class="event-detail-item">
            <h6>📍 Location</h6>
            <p class="mb-0">${event.extendedProps.location}</p>
        </div>
        ` : ''}
        
        <div class="event-detail-item">
            <h6>🏷️ Type & Priority</h6>
            <div>
                <span class="badge" style="background-color: ${event.backgroundColor}">
                    ${event.extendedProps.type.charAt(0).toUpperCase() + event.extendedProps.type.slice(1)}
                </span>
                <span class="badge priority-badge ms-2 ${
                    event.extendedProps.priority === 'urgent' ? 'bg-danger' :
                    event.extendedProps.priority === 'high' ? 'bg-warning' :
                    event.extendedProps.priority === 'medium' ? 'bg-info' : 'bg-secondary'
                }">
                    ${event.extendedProps.priority.charAt(0).toUpperCase() + event.extendedProps.priority.slice(1)}
                </span>
            </div>
        </div>
        
        <div class="event-detail-item">
            <h6>🚩 Status</h6>
            <span class="badge status-badge ${
                event.extendedProps.status === 'completed' ? 'bg-success' :
                event.extendedProps.status === 'in_progress' ? 'bg-primary' :
                event.extendedProps.status === 'cancelled' ? 'bg-danger' :
                event.extendedProps.status === 'postponed' ? 'bg-warning' : 'bg-secondary'
            }">
                ${event.extendedProps.status.replace('_', ' ').charAt(0).toUpperCase() + event.extendedProps.status.replace('_', ' ').slice(1)}
            </span>
        </div>
    `;
    
    modal.show();
}

function formatEventTime(start, end) {
    const startTime = new Date(start).toLocaleString();
    const endTime = new Date(end).toLocaleString();
    return `${startTime} - ${endTime}`;
}

function updateEventStatus(status) {
    if (!currentEvent) return;
    
    fetch(`/events/${currentEvent.id}/status`, {
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
            // Update event in calendar
            currentEvent.setExtendedProp('status', status);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            
            // Refresh calendar
            calendar.refetchEvents();
            
            // Show success message
            showAlert('success', data.message);
        } else {
            showAlert('error', 'Failed to update event status');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        showAlert('error', 'An error occurred while updating the status');
    });
}

function updateMonthlySummary(events) {
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();
    
    const monthlyEvents = events.filter(event => {
        const eventDate = new Date(event.start);
        return eventDate.getMonth() === currentMonth && eventDate.getFullYear() === currentYear;
    });
    
    const completed = monthlyEvents.filter(event => event.extendedProps.status === 'completed').length;
    const upcoming = monthlyEvents.filter(event => {
        const eventDate = new Date(event.start);
        return eventDate > now && event.extendedProps.status !== 'completed' && event.extendedProps.status !== 'cancelled';
    }).length;
    
    document.getElementById('monthlyTotal').textContent = monthlyEvents.length;
    document.getElementById('monthlyCompleted').textContent = completed;
    document.getElementById('monthlyUpcoming').textContent = upcoming;
}

function goToToday() {
    calendar.today();
}

function changeView(viewName) {
    calendar.changeView(viewName);
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endsection