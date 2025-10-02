@extends('layouts.app')

@section('title', 'Location Tracker')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        📍
                        Location Tracker
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                            Add Location
                        </button>
                        <button type="button" class="btn btn-success" onclick="getCurrentLocation()">
                            🎯 Current Location
                        </button>
                        <button type="button" class="btn btn-info" onclick="toggleMapView()">
                            🗺️ Map View
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Map Container -->
                    <div id="mapContainer" class="mb-4" style="display: none;">
                        <div id="map" style="height: 400px; border-radius: 8px;"></div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                                <option value="client">Client</option>
                                <option value="meeting">Meeting</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="favoritesOnly">
                                <label class="form-check-label" for="favoritesOnly">
                                    Favorites Only
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search locations...">
                        </div>
                    </div>

                    <!-- Locations Grid -->
                    <div class="row" id="locationsGrid">
                        @forelse($locations as $location)
                        <div class="col-md-6 col-lg-4 mb-4 location-card" 
                             data-type="{{ $location->type }}" 
                             data-status="{{ $location->status }}"
                             data-favorite="{{ $location->is_favorite ? 'true' : 'false' }}">
                            <div class="card h-100 {{ $location->is_favorite ? 'border-warning' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <span class="text-primary me-1">📍</span>
                                            {{ $location->name }}
                                        </h5>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                ⋮
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('locations.show', $location) }}">👁️ View</a></li>
                                                <li><a class="dropdown-item" href="{{ route('locations.edit', $location) }}">Edit</a></li>
                                                <li><button class="dropdown-item" onclick="toggleFavorite({{ $location->id }})">⭐ {{ $location->is_favorite ? 'Remove from' : 'Add to' }} Favorites</button></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button class="dropdown-item text-danger" onclick="deleteLocation({{ $location->id }})">Delete</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $location->type === 'home' ? 'success' : ($location->type === 'office' ? 'primary' : 'secondary') }} me-1">
                                            {{ ucfirst($location->type) }}
                                        </span>
                                        <span class="badge bg-{{ $location->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($location->status) }}
                                        </span>
                                        @if($location->is_favorite)
                                        <span class="badge bg-warning text-dark">
                                            ⭐ Favorite
                                        </span>
                                        @endif
                                    </div>

                                    @if($location->description)
                                    <p class="card-text text-muted small mb-2">{{ Str::limit($location->description, 100) }}</p>
                                    @endif

                                    @if($location->address)
                                    <p class="card-text small mb-2">
                                        <span class="text-muted me-1">📍</span>
                                        {{ $location->address }}
                                    </p>
                                    @endif

                                    <div class="row text-center mt-3">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Visits</small>
                                            <strong>{{ $location->visit_count }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Distance</small>
                                            <strong class="distance-text" data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">-</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Last Visit</small>
                                            <strong>{{ $location->last_visited_at ? $location->last_visited_at->diffForHumans() : 'Never' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-outline-primary btn-sm" onclick="checkIn({{ $location->id }})">
                                            🚪 Check In
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="getDirections({{ $location->latitude }}, {{ $location->longitude }})">
                                            🧭 Directions
                                        </button>
                                        <a href="{{ route('locations.analytics', $location) }}" class="btn btn-outline-info btn-sm">
                                            📊 Analytics
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <span class="text-muted mb-3" style="font-size: 3rem;">📍</span>
                                <h4 class="text-muted">No Locations Found</h4>
                                <p class="text-muted">Start by adding your first location or get your current location.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                    Add First Location
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($locations->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $locations->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('locations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Location Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="home">Home</option>
                                    <option value="office">Office</option>
                                    <option value="client">Client</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude *</label>
                                <input type="number" step="any" class="form-control" id="latitude" name="latitude" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude *</label>
                                <input type="number" step="any" class="form-control" id="longitude" name="longitude" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" value="1">
                        <label class="form-check-label" for="is_favorite">
                            Add to favorites
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.location-card {
    transition: transform 0.2s;
}

.location-card:hover {
    transform: translateY(-2px);
}

.card.border-warning {
    border-width: 2px !important;
}

#map {
    border: 1px solid #dee2e6;
}

.distance-text {
    font-size: 0.9em;
}
</style>

<script>
let map;
let userLocation = null;
let locationMarkers = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Get user's current location for distance calculations
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            updateDistances();
        });
    }

    // Setup filters
    setupFilters();
});

// Filter functionality
function setupFilters() {
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const favoritesOnly = document.getElementById('favoritesOnly');
    const searchInput = document.getElementById('searchInput');

    [typeFilter, statusFilter, favoritesOnly, searchInput].forEach(element => {
        element.addEventListener('change', filterLocations);
        element.addEventListener('input', filterLocations);
    });
}

function filterLocations() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const favoritesOnly = document.getElementById('favoritesOnly').checked;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();

    const cards = document.querySelectorAll('.location-card');
    
    cards.forEach(card => {
        const type = card.dataset.type;
        const status = card.dataset.status;
        const isFavorite = card.dataset.favorite === 'true';
        const cardText = card.textContent.toLowerCase();

        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        const favoriteMatch = !favoritesOnly || isFavorite;
        const searchMatch = !searchTerm || cardText.includes(searchTerm);

        if (typeMatch && statusMatch && favoriteMatch && searchMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Distance calculation
function updateDistances() {
    if (!userLocation) return;

    document.querySelectorAll('.distance-text').forEach(element => {
        const lat = parseFloat(element.dataset.lat);
        const lng = parseFloat(element.dataset.lng);
        
        if (lat && lng) {
            const distance = calculateDistance(userLocation.lat, userLocation.lng, lat, lng);
            element.textContent = distance < 1 ? 
                Math.round(distance * 1000) + 'm' : 
                distance.toFixed(1) + 'km';
        }
    });
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
function toggleMapView() {
    const mapContainer = document.getElementById('mapContainer');
    if (mapContainer.style.display === 'none') {
        mapContainer.style.display = 'block';
        initializeMap();
    } else {
        mapContainer.style.display = 'none';
    }
}

function initializeMap() {
    // This would integrate with Google Maps or another mapping service
    // For now, we'll show a placeholder
    const mapElement = document.getElementById('map');
    if (!map) {
        mapElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                <div class="text-center">
                    <span class="text-muted mb-3" style="font-size: 3rem;">🗺️</span>
                    <h5 class="text-muted">Map Integration</h5>
                    <p class="text-muted">Integrate with Google Maps or OpenStreetMap</p>
                </div>
            </div>
        `;
    }
}

// Location actions
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Fill the modal form with current location
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('name').value = 'Current Location';
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('addLocationModal')).show();
            
            // Optionally reverse geocode to get address
            reverseGeocode(lat, lng);
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

function reverseGeocode(lat, lng) {
    // This would integrate with a geocoding service
    // For now, we'll just set a placeholder
    document.getElementById('address').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
}

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
    if (confirm('Are you sure you want to delete this location?')) {
        fetch(`/locations/${locationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ScrollPreserver.preserveAndReload();
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
    // Open directions in default map app
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    window.open(url, '_blank');
}
</script>
@endsection