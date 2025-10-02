@extends('layouts.app')

@section('title', $location->name . ' - Location Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <span class="text-primary me-2">📍</span>
                        {{ $location->name }}
                        @if($location->is_favorite)
                        <span class="badge bg-warning text-dark ms-2">
                            ⭐ Favorite
                        </span>
                        @endif
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                            <li class="breadcrumb-item active">{{ $location->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="checkIn({{ $location->id }})">
                        🚪 Check In
                    </button>
                    <button type="button" class="btn btn-primary" onclick="getDirections({{ $location->latitude }}, {{ $location->longitude }})">
                        🧭 Directions
                    </button>
                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-outline-primary">
                        ✏️ Edit
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            ⋮
                        </button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" onclick="toggleFavorite({{ $location->id }})">⭐ {{ $location->is_favorite ? 'Remove from' : 'Add to' }} Favorites</button></li>
                            <li><a class="dropdown-item" href="{{ route('locations.analytics', $location) }}">📊 Analytics</a></li>
                            <li><a class="dropdown-item" href="{{ route('locations.export', $location) }}">📥 Export Data</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="deleteLocation({{ $location->id }})">🗑️ Delete</button></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Location Info -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                ℹ️
                                Location Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Type</label>
                                        <div>
                                            <span class="badge bg-{{ $location->type === 'home' ? 'success' : ($location->type === 'office' ? 'primary' : 'secondary') }} fs-6">
                                                {{ ucfirst($location->type) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div>
                                            <span class="badge bg-{{ $location->status === 'active' ? 'success' : 'secondary' }} fs-6">
                                                {{ ucfirst($location->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($location->description)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <p class="mb-0">{{ $location->description }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Coordinates</label>
                                        <p class="mb-0">
                                            <span class="text-muted me-1">📍</span>
                                            {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}
                                        </p>
                                    </div>
                                    @if($location->address)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Address</label>
                                        <p class="mb-0">{{ $location->address }}</p>
                                        @if($location->city || $location->state || $location->country)
                                        <small class="text-muted">
                                            {{ collect([$location->city, $location->state, $location->country])->filter()->implode(', ') }}
                                        </small>
                                        @endif
                                    </div>
                                    @endif
                                    @if($location->notes)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Notes</label>
                                        <p class="mb-0">{{ $location->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                🗺️
                                Location Map
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="locationMap" style="height: 400px;"></div>
                        </div>
                    </div>

                    <!-- Recent Visits -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                📜
                                Recent Visits
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadMoreVisits()">
                                🔄 Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            @if($location->visits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Duration</th>
                                            <th>Purpose</th>
                                            <th>Check-in Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($location->visits->take(10) as $visit)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $visit->visited_at->format('M j, Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $visit->visited_at->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($visit->duration_minutes)
                                                <span class="badge bg-info">
                                                    {{ $visit->duration_formatted }}
                                                </span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $visit->purpose ?: '-' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($visit->check_in_method ?: 'manual') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!$visit->left_at)
                                                <button class="btn btn-sm btn-outline-danger" onclick="checkOut({{ $visit->id }})">
                                                    🚪 Check Out
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($location->visits->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('locations.analytics', $location) }}" class="btn btn-outline-primary">
                                    View All Visits
                                </a>
                            </div>
                            @endif
                            @else
                            <div class="text-center py-4">
                                <span style="font-size: 2rem;" class="text-muted mb-3">🕒</span>
                                <h6 class="text-muted">No visits recorded yet</h6>
                                <p class="text-muted">Check in to start tracking your visits to this location.</p>
                                <button type="button" class="btn btn-primary" onclick="checkIn({{ $location->id }})">
                                    🚪 Check In Now
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                📊
                                Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border-end">
                                        <h3 class="text-primary mb-1">{{ $location->visit_count }}</h3>
                                        <small class="text-muted">Total Visits</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h3 class="text-success mb-1" id="distanceFromUser">-</h3>
                                    <small class="text-muted">Distance</small>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h6 class="text-info mb-1">{{ $location->last_visited_at ? $location->last_visited_at->diffForHumans() : 'Never' }}</h6>
                                        <small class="text-muted">Last Visit</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-warning mb-1">{{ $location->created_at->diffForHumans() }}</h6>
                                    <small class="text-muted">Added</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                ⚡
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success" onclick="checkIn({{ $location->id }})">
                                    🚪 Check In
                                </button>
                                <button type="button" class="btn btn-primary" onclick="getDirections({{ $location->latitude }}, {{ $location->longitude }})">
                                    🧭 Get Directions
                                </button>
                                <button type="button" class="btn btn-info" onclick="shareLocation()">
                                    📤 Share Location
                                </button>
                                <button type="button" class="btn btn-warning" onclick="toggleFavorite({{ $location->id }})">
                                    ⭐ {{ $location->is_favorite ? 'Remove from' : 'Add to' }} Favorites
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Weather Info (if available) -->
                    @if($location->visits->where('weather', '!=', null)->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                🌤️
                                Recent Weather
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $recentVisitWithWeather = $location->visits->where('weather', '!=', null)->first();
                            @endphp
                            @if($recentVisitWithWeather)
                            <div class="text-center">
                                <h4 class="text-primary">{{ $recentVisitWithWeather->weather }}</h4>
                                @if($recentVisitWithWeather->temperature)
                                <p class="mb-0">{{ $recentVisitWithWeather->temperature }}°</p>
                                @endif
                                <small class="text-muted">{{ $recentVisitWithWeather->visited_at->diffForHumans() }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Nearby Locations -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                🗺️
                                Nearby Locations
                            </h5>
                        </div>
                        <div class="card-body" id="nearbyLocations">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Finding nearby locations...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#locationMap {
    border-radius: 0 0 0.375rem 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}
</style>

<script>
let map;
let userLocation = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Get user's current location for distance calculation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            updateDistance();
        });
    }

    // Initialize map
    initializeMap();
    
    // Load nearby locations
    loadNearbyLocations();
});

// Distance calculation
function updateDistance() {
    if (!userLocation) return;

    const locationLat = {{ $location->latitude }};
    const locationLng = {{ $location->longitude }};
    
    const distance = calculateDistance(userLocation.lat, userLocation.lng, locationLat, locationLng);
    const distanceText = distance < 1 ? 
        Math.round(distance * 1000) + 'm' : 
        distance.toFixed(1) + 'km';
    
    document.getElementById('distanceFromUser').textContent = distanceText;
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the Earth in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Map functionality
function initializeMap() {
    // This would integrate with Google Maps or another mapping service
    const mapElement = document.getElementById('locationMap');
    mapElement.innerHTML = `
        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
            <div class="text-center p-4">
                <span style="font-size: 3rem;" class="text-muted mb-3">🗺️</span>
                <h5 class="text-muted">Interactive Map</h5>
                <p class="text-muted mb-3">Location: {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}</p>
                <button class="btn btn-primary" onclick="openInMaps()">
                    🔗 Open in Maps
                </button>
            </div>
        </div>
    `;
}

function openInMaps() {
    const lat = {{ $location->latitude }};
    const lng = {{ $location->longitude }};
    const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    window.open(url, '_blank');
}

// Location actions
function checkIn(locationId) {
    if (confirm('Check in to this location?')) {
        fetch(`/locations/${locationId}/check-in`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Checked in successfully!');
                location.reload();
            } else {
                alert('Error checking in: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error checking in.');
        });
    }
}

function checkOut(visitId) {
    if (confirm('Check out from this visit?')) {
        fetch(`/location-visits/${visitId}/check-out`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Checked out successfully!');
                ScrollPreserver.preserveAndReload();
            } else {
                alert('Error checking out.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error checking out.');
        });
    }
}

function toggleFavorite(locationId) {
    fetch(`/locations/${locationId}/toggle-favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            ScrollPreserver.preserveAndReload();
        } else {
            alert('Error updating favorite status.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating favorite status.');
    });
}

function deleteLocation(locationId) {
    if (confirm('Are you sure you want to delete this location? This action cannot be undone.')) {
        fetch(`/locations/${locationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("locations.index") }}';
            } else {
                alert('Error deleting location.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting location.');
        });
    }
}

function getDirections(lat, lng) {
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    window.open(url, '_blank');
}

function shareLocation() {
    const url = window.location.href;
    const text = `Check out this location: {{ $location->name }}`;
    
    if (navigator.share) {
        navigator.share({
            title: '{{ $location->name }}',
            text: text,
            url: url
        });
    } else {
        // Fallback to copying to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Location URL copied to clipboard!');
        });
    }
}

function loadNearbyLocations() {
    fetch(`/locations/nearby?lat={{ $location->latitude }}&lng={{ $location->longitude }}&exclude={{ $location->id }}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('nearbyLocations');
        
        if (data.locations && data.locations.length > 0) {
            let html = '';
            data.locations.forEach(location => {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <div>
                            <strong>${location.name}</strong>
                            <br>
                            <small class="text-muted">${location.distance}</small>
                        </div>
                        <a href="/locations/${location.id}" class="btn btn-sm btn-outline-primary">
                            👁️
                        </a>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="text-center py-3">
                    <span class="text-muted mb-2">📍</span>
                    <p class="text-muted mb-0">No nearby locations found</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading nearby locations:', error);
        document.getElementById('nearbyLocations').innerHTML = `
            <div class="text-center py-3">
                <span class="text-warning mb-2">⚠️</span>
                <p class="text-muted mb-0">Error loading nearby locations</p>
            </div>
        `;
    });
}

function loadMoreVisits() {
    // Reload the page to refresh visits
    location.reload();
}
</script>
@endsection