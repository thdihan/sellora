@extends('layouts.app')

@section('title', 'Location Visits')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            🕒 
                            Location Visits
                            @if(isset($location))
                                - {{ $location->name }}
                            @endif
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                @if(isset($location))
                                    <li class="breadcrumb-item"><a href="{{ route('locations.show', $location) }}">{{ $location->name }}</a></li>
                                @endif
                                <li class="breadcrumb-item active">Visits</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters and Search -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange" onchange="filterVisits()">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="locationFilter" class="form-label">Location</label>
                            <select class="form-select" id="locationFilter" onchange="filterVisits()">
                                <option value="all">All Locations</option>
                                @foreach($locations ?? [] as $loc)
                                    <option value="{{ $loc->id }}" {{ isset($location) && $location->id == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="purposeFilter" class="form-label">Purpose</label>
                            <select class="form-select" id="purposeFilter" onchange="filterVisits()">
                                <option value="all">All Purposes</option>
                                <option value="work">Work</option>
                                <option value="meeting">Meeting</option>
                                <option value="personal">Personal</option>
                                <option value="client">Client Visit</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchVisits" class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchVisits" placeholder="Search visits..." onkeyup="filterVisits()">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()">
                                    ❌
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Date Range (Hidden by default) -->
                    <div class="row mb-4" id="customDateRange" style="display: none;">
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" onchange="filterVisits()">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" onchange="filterVisits()">
                        </div>
                    </div>

                    <!-- Visit Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-1" id="totalVisits">{{ $visits->count() }}</h4>
                                    <small>Total Visits</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-1" id="avgDuration">{{ number_format($visits->avg('duration_minutes') ?? 0, 1) }}m</h4>
                                    <small>Avg Duration</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-1" id="totalDuration">{{ number_format($visits->sum('duration_minutes') / 60, 1) }}h</h4>
                                    <small>Total Time</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-1" id="uniqueLocations">{{ $visits->pluck('location_id')->unique()->count() }}</h4>
                                    <small>Unique Locations</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export and Actions -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button class="btn btn-outline-primary" onclick="exportVisits()">
                                📥 Export Data
                            </button>
                            <button class="btn btn-outline-info" onclick="showAnalytics()">
                                📊 Analytics
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="addManualVisit()">
                                ➕ Add Manual Visit
                            </button>
                        </div>
                    </div>

                    <!-- Visits Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="visitsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Duration</th>
                                    <th>Purpose</th>
                                    <th>Check-in Method</th>
                                    <th>Mood</th>
                                    <th>Productivity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                    <tr data-visit-id="{{ $visit->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="location-icon me-2">
                                                    @switch($visit->location->type)
                                                        @case('home')
                                                            <span class="text-primary">🏠</span>
                                                            @break
                                                        @case('office')
                                                            <span class="text-info">🏢</span>
                                                            @break
                                                        @case('client')
                                                            <span class="text-success">🤝</span>
                                                            @break
                                                        @case('meeting')
                                                            <span class="text-warning">👥</span>
                                                            @break
                                                        @default
                                                            <span class="text-secondary">📍</span>
                                                    @endswitch
                                                </div>
                                                <div>
                                                    <strong>{{ $visit->location->name }}</strong>
                                                    @if($visit->location->address)
                                                        <br><small class="text-muted">{{ $visit->location->address }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $visit->visited_at->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ $visit->visited_at->format('h:i A') }}</small>
                                                @if($visit->left_at)
                                                    <br><small class="text-muted">Left: {{ $visit->left_at->format('h:i A') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($visit->duration_minutes)
                                                <span class="badge bg-info">
                                                    {{ $visit->duration_formatted }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visit->purpose)
                                                <span class="badge bg-secondary">{{ ucfirst($visit->purpose) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ ucfirst($visit->check_in_method ?? 'manual') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($visit->mood_rating)
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= $visit->mood_rating ? 'text-warning' : 'text-muted' }}">⭐</span>
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visit->productivity_rating)
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $visit->productivity_rating * 20 }}%" 
                                                         aria-valuenow="{{ $visit->productivity_rating }}" 
                                                         aria-valuemin="0" aria-valuemax="5">
                                                        {{ $visit->productivity_rating }}/5
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewVisit({{ $visit->id }})" title="View Details">
                                                    👁️
                                                </button>
                                                <button class="btn btn-outline-warning" onclick="editVisit({{ $visit->id }})" title="Edit Visit">
                                                    ✏️
                                                </button>
                                                @if(!$visit->left_at)
                                                    <button class="btn btn-outline-success" onclick="checkOut({{ $visit->id }})" title="Check Out">
                                                        🚪
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-danger" onclick="deleteVisit({{ $visit->id }})" title="Delete Visit">
                                                    🗑️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <span class="mb-3" style="font-size: 3rem;">🕒</span>
                                                <h5>No visits found</h5>
                                                <p>No location visits match your current filters.</p>
                                                <button class="btn btn-primary" onclick="addManualVisit()">
                                                    ➕ Add First Visit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($visits instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center">
                            {{ $visits->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visit Details Modal -->
<div class="modal fade" id="visitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    ℹ️ 
                    Visit Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="visitModalBody">
                <!-- Visit details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editCurrentVisit()">Edit Visit</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Manual Visit Modal -->
<div class="modal fade" id="addVisitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    ➕ 
                    Add Manual Visit
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addVisitForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="visitLocation" class="form-label">Location</label>
                        <select class="form-select" id="visitLocation" required>
                            <option value="">Select Location</option>
                            @foreach($locations ?? [] as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="visitDate" class="form-label">Visit Date</label>
                                <input type="date" class="form-control" id="visitDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="visitTime" class="form-label">Visit Time</label>
                                <input type="time" class="form-control" id="visitTime" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="leftDate" class="form-label">Left Date (Optional)</label>
                                <input type="date" class="form-control" id="leftDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="leftTime" class="form-label">Left Time (Optional)</label>
                                <input type="time" class="form-control" id="leftTime">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="visitPurpose" class="form-label">Purpose</label>
                        <select class="form-select" id="visitPurpose">
                            <option value="">Select Purpose</option>
                            <option value="work">Work</option>
                            <option value="meeting">Meeting</option>
                            <option value="personal">Personal</option>
                            <option value="client">Client Visit</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="visitNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="visitNotes" rows="3" placeholder="Any additional notes about this visit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.location-icon {
    width: 20px;
    text-align: center;
}

.progress {
    background-color: #e9ecef;
}

.table th {
    font-weight: 600;
    border-top: none;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>

<script>
let currentVisitId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default date for manual visit
    document.getElementById('visitDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('visitTime').value = new Date().toTimeString().slice(0, 5);
});

// Filter visits
function filterVisits() {
    const dateRange = document.getElementById('dateRange').value;
    const locationFilter = document.getElementById('locationFilter').value;
    const purposeFilter = document.getElementById('purposeFilter').value;
    const searchTerm = document.getElementById('searchVisits').value;
    
    // Show/hide custom date range
    const customDateRange = document.getElementById('customDateRange');
    if (dateRange === 'custom') {
        customDateRange.style.display = 'block';
    } else {
        customDateRange.style.display = 'none';
    }
    
    // Apply filters to table rows
    const rows = document.querySelectorAll('#visitsTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip empty state row
        
        let visible = true;
        
        // Apply filters here (this would typically be done server-side)
        // For demo purposes, we'll just show all rows
        
        if (visible) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update statistics based on visible rows
    updateStatistics(visibleCount);
}

// Clear all filters
function clearFilters() {
    document.getElementById('dateRange').value = 'all';
    document.getElementById('locationFilter').value = 'all';
    document.getElementById('purposeFilter').value = 'all';
    document.getElementById('searchVisits').value = '';
    document.getElementById('customDateRange').style.display = 'none';
    filterVisits();
}

// Update statistics
function updateStatistics(visibleCount) {
    // This would calculate statistics based on filtered data
    // For now, we'll keep the original values
}

// View visit details
function viewVisit(visitId) {
    currentVisitId = visitId;
    
    // Load visit details via AJAX
    fetch(`/locations/visits/${visitId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('visitModalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Location Information</h6>
                        <p><strong>Name:</strong> ${data.location.name}</p>
                        <p><strong>Type:</strong> ${data.location.type}</p>
                        <p><strong>Address:</strong> ${data.location.address || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Visit Information</h6>
                        <p><strong>Date:</strong> ${new Date(data.visited_at).toLocaleDateString()}</p>
                        <p><strong>Time:</strong> ${new Date(data.visited_at).toLocaleTimeString()}</p>
                        <p><strong>Duration:</strong> ${data.duration_formatted || 'In Progress'}</p>
                        <p><strong>Purpose:</strong> ${data.purpose || 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Additional Details</h6>
                        <p><strong>Check-in Method:</strong> ${data.check_in_method || 'Manual'}</p>
                        <p><strong>Mood Rating:</strong> ${data.mood_rating ? data.mood_rating + '/5' : 'N/A'}</p>
                        <p><strong>Productivity Rating:</strong> ${data.productivity_rating ? data.productivity_rating + '/5' : 'N/A'}</p>
                        <p><strong>Notes:</strong> ${data.notes || 'No notes'}</p>
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('visitModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading visit details:', error);
            showAlert('danger', 'Error loading visit details.');
        });
}

// Edit visit
function editVisit(visitId) {
    window.location.href = `/locations/visits/${visitId}/edit`;
}

// Edit current visit from modal
function editCurrentVisit() {
    if (currentVisitId) {
        editVisit(currentVisitId);
    }
}

// Check out from visit
function checkOut(visitId) {
    if (confirm('Are you sure you want to check out from this visit?')) {
        fetch(`/locations/visits/${visitId}/checkout`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Checked out successfully!');
                location.reload();
            } else {
                showAlert('danger', 'Error checking out: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error checking out.');
        });
    }
}

// Delete visit
function deleteVisit(visitId) {
    if (confirm('Are you sure you want to delete this visit? This action cannot be undone.')) {
        fetch(`/locations/visits/${visitId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Visit deleted successfully!');
                document.querySelector(`tr[data-visit-id="${visitId}"]`).remove();
            } else {
                showAlert('danger', 'Error deleting visit: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error deleting visit.');
        });
    }
}

// Add manual visit
function addManualVisit() {
    const modal = new bootstrap.Modal(document.getElementById('addVisitModal'));
    modal.show();
}

// Handle add visit form submission
document.getElementById('addVisitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        location_id: document.getElementById('visitLocation').value,
        visited_at: document.getElementById('visitDate').value + ' ' + document.getElementById('visitTime').value,
        left_at: document.getElementById('leftDate').value && document.getElementById('leftTime').value ? 
                 document.getElementById('leftDate').value + ' ' + document.getElementById('leftTime').value : null,
        purpose: document.getElementById('visitPurpose').value,
        notes: document.getElementById('visitNotes').value,
        check_in_method: 'manual'
    };
    
    fetch('/locations/visits', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Visit added successfully!');
            bootstrap.Modal.getInstance(document.getElementById('addVisitModal')).hide();
            location.reload();
        } else {
            showAlert('danger', 'Error adding visit: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error adding visit.');
    });
});

// Export visits
function exportVisits() {
    const params = new URLSearchParams({
        date_range: document.getElementById('dateRange').value,
        location: document.getElementById('locationFilter').value,
        purpose: document.getElementById('purposeFilter').value,
        search: document.getElementById('searchVisits').value
    });
    
    window.open(`/locations/visits/export?${params.toString()}`, '_blank');
}

// Show analytics
function showAnalytics() {
    window.location.href = '/locations/analytics';
}

// Utility function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection