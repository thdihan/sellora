@extends('layouts.app')

@section('title', 'Location Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <span class="me-2 text-primary">📍</span>
                        Location Dashboard
                    </h2>
                    <p class="text-muted mb-0">Track and manage your location activities</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="getCurrentLocation()">
                        <span class="me-1">🎯</span> Current Location
                    </button>
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">
                        <span class="me-1">➕</span> Add Location
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" id="totalLocations">{{ $stats['total_locations'] ?? 0 }}</h3>
                            <p class="mb-0">Total Locations</p>
                        </div>
                        <span style="font-size: 2rem; opacity: 0.75;">📍</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" id="todayVisits">{{ $stats['today_visits'] ?? 0 }}</h3>
                            <p class="mb-0">Today's Visits</p>
                        </div>
                        <span style="font-size: 2rem; opacity: 0.75;">📅</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" id="currentlyAt">{{ $stats['current_location'] ?? 'Unknown' }}</h3>
                            <p class="mb-0">Currently At</p>
                        </div>
                        <span style="font-size: 2rem; opacity: 0.75;">🎯</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" id="totalTime">{{ $stats['total_time'] ?? '0h' }}</h3>
                            <p class="mb-0">Total Time Today</p>
                        </div>
                        <span style="font-size: 2rem; opacity: 0.75;">🕒</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Map View -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map me-2"></i>
                            Location Map
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="mapType" id="roadmap" value="roadmap" checked>
                            <label class="btn btn-outline-secondary" for="roadmap">Road</label>
                            
                            <input type="radio" class="btn-check" name="mapType" id="satellite" value="satellite">
                            <label class="btn btn-outline-secondary" for="satellite">Satellite</label>
                            
                            <input type="radio" class="btn-check" name="mapType" id="hybrid" value="hybrid">
                            <label class="btn btn-outline-secondary" for="hybrid">Hybrid</label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <div class="d-flex align-items-center">
                                <div class="legend-marker bg-primary"></div>
                                <small class="text-muted">Current Location</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="legend-marker bg-success"></div>
                                <small class="text-muted">Saved Locations</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="legend-marker bg-warning"></div>
                                <small class="text-muted">Recent Visits</small>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="centerMapOnUser()">
                            <i class="fas fa-crosshairs me-1"></i> Center on Me
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="quickCheckIn()">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Quick Check-in
                        </button>
                        <button class="btn btn-danger" onclick="quickCheckOut()">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Quick Check-out
                        </button>
                        <a href="{{ route('locations.nearby') }}" class="btn btn-info">
                            <i class="fas fa-search-location me-2"></i>
                            Find Nearby
                        </a>
                        <a href="{{ route('locations.analytics') }}" class="btn btn-warning">
                            <i class="fas fa-chart-bar me-2"></i>
                            View Analytics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Recent Activity
                        </h5>
                        <a href="{{ route('locations.visits') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="recentActivity">
                        <!-- Activity items will be loaded here -->
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Loading recent activity...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Timeline -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-timeline me-2"></i>
                            Today's Timeline
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="loadTimeline('today')">Today</button>
                            <button type="button" class="btn btn-outline-primary" onclick="loadTimeline('yesterday')">Yesterday</button>
                            <button type="button" class="btn btn-outline-primary" onclick="loadTimeline('week')">This Week</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="timeline" class="timeline">
                        <!-- Timeline items will be loaded here -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Loading timeline...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Check-in Modal -->
<div class="modal fade" id="quickCheckinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Quick Check-in
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickCheckinForm">
                    <div class="mb-3">
                        <label for="checkinLocation" class="form-label">Location</label>
                        <select class="form-select" id="checkinLocation" name="location_id" required>
                            <option value="">Select a location...</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="checkinPurpose" class="form-label">Purpose (Optional)</label>
                        <input type="text" class="form-control" id="checkinPurpose" name="purpose" placeholder="e.g., Meeting, Work, Personal">
                    </div>
                    <div class="mb-3">
                        <label for="checkinNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="checkinNotes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitQuickCheckin()">
                    <i class="fas fa-sign-in-alt me-1"></i> Check In
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Check-out Modal -->
<div class="modal fade" id="quickCheckoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Quick Check-out
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="currentCheckin">
                    <!-- Current check-in info will be loaded here -->
                </div>
                <form id="quickCheckoutForm">
                    <div class="mb-3">
                        <label for="checkoutNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="checkoutNotes" name="notes" rows="2" placeholder="Any additional notes about your visit..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="moodRating" class="form-label">Mood Rating</label>
                        <div class="rating-stars">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="moodRatingValue" name="mood_rating" value="5">
                    </div>
                    <div class="mb-3">
                        <label for="productivityRating" class="form-label">Productivity Rating</label>
                        <div class="rating-stars">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="productivityRatingValue" name="productivity_rating" value="5">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitQuickCheckout()">
                    <i class="fas fa-sign-out-alt me-1"></i> Check Out
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.legend-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 25px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.checkin::before {
    background: #28a745;
}

.timeline-item.checkout::before {
    background: #dc3545;
}

.timeline-item.visit::before {
    background: #ffc107;
}

.rating-stars {
    display: flex;
    gap: 5px;
    margin-bottom: 10px;
}

.rating-stars i {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-stars i:hover,
.rating-stars i.active {
    color: #ffc107;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group .btn-check:checked + .btn {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}
</style>

<script>
let map;
let userMarker;
let locationMarkers = [];
let currentPosition = null;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    loadRecentActivity();
    loadTimeline('today');
    setupRatingStars();
    
    // Setup map type change handlers
    document.querySelectorAll('input[name="mapType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (map) {
                map.setMapTypeId(this.value);
            }
        });
    });
});

// Initialize Google Map
function initializeMap() {
    if (typeof google === 'undefined') {
        console.error('Google Maps API not loaded');
        return;
    }
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 23.8103, lng: 90.4125 }, // Default to Dhaka
        mapTypeId: 'roadmap'
    });
    
    // Get user's current location
    getCurrentLocation();
    
    // Load saved locations
    loadSavedLocations();
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Center map on user location
                if (map) {
                    map.setCenter(currentPosition);
                    
                    // Add user marker
                    if (userMarker) {
                        userMarker.setMap(null);
                    }
                    
                    userMarker = new google.maps.Marker({
                        position: currentPosition,
                        map: map,
                        title: 'Your Current Location',
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#007bff">
                                    <circle cx="12" cy="12" r="8" stroke="white" stroke-width="2"/>
                                </svg>
                            `),
                            scaledSize: new google.maps.Size(24, 24)
                        }
                    });
                }
                
                // Update current location display
                updateCurrentLocationDisplay(currentPosition);
            },
            function(error) {
                console.error('Error getting location:', error);
                showAlert('warning', 'Could not get your current location. Please enable location services.');
            }
        );
    } else {
        showAlert('error', 'Geolocation is not supported by this browser.');
    }
}

// Load saved locations
function loadSavedLocations() {
    // This would typically fetch from the server
    // For demo purposes, we'll use sample data
    const sampleLocations = [
        { id: 1, name: 'Office', lat: 23.8103, lng: 90.4125, type: 'work' },
        { id: 2, name: 'Home', lat: 23.8200, lng: 90.4200, type: 'home' },
        { id: 3, name: 'Gym', lat: 23.8000, lng: 90.4000, type: 'fitness' }
    ];
    
    sampleLocations.forEach(location => {
        const marker = new google.maps.Marker({
            position: { lat: location.lat, lng: location.lng },
            map: map,
            title: location.name,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#28a745">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(20, 20)
            }
        });
        
        locationMarkers.push(marker);
        
        // Add click listener
        marker.addListener('click', function() {
            showLocationInfo(location);
        });
    });
}

// Update current location display
function updateCurrentLocationDisplay(position) {
    // Reverse geocoding to get address
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: position }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const address = results[0].formatted_address;
            document.getElementById('currentlyAt').textContent = address.split(',')[0];
        }
    });
}

// Center map on user
function centerMapOnUser() {
    if (currentPosition && map) {
        map.setCenter(currentPosition);
        map.setZoom(15);
    } else {
        getCurrentLocation();
    }
}

// Quick check-in
function quickCheckIn() {
    // Load available locations
    loadLocationsForCheckin();
    
    const modal = new bootstrap.Modal(document.getElementById('quickCheckinModal'));
    modal.show();
}

// Quick check-out
function quickCheckOut() {
    // Load current check-in info
    loadCurrentCheckin();
    
    const modal = new bootstrap.Modal(document.getElementById('quickCheckoutModal'));
    modal.show();
}

// Load locations for check-in
function loadLocationsForCheckin() {
    const select = document.getElementById('checkinLocation');
    select.innerHTML = '<option value="">Select a location...</option>';
    
    // Sample locations
    const locations = [
        { id: 1, name: 'Office' },
        { id: 2, name: 'Home' },
        { id: 3, name: 'Gym' },
        { id: 4, name: 'Coffee Shop' }
    ];
    
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location.id;
        option.textContent = location.name;
        select.appendChild(option);
    });
}

// Load current check-in info
function loadCurrentCheckin() {
    const container = document.getElementById('currentCheckin');
    
    // Sample current check-in
    const currentCheckin = {
        location: 'Office',
        checkedInAt: '2024-01-15 09:30:00',
        duration: '2h 30m'
    };
    
    if (currentCheckin) {
        container.innerHTML = `
            <div class="alert alert-info">
                <h6 class="mb-1">Currently checked in at:</h6>
                <p class="mb-1"><strong>${currentCheckin.location}</strong></p>
                <small class="text-muted">Since ${currentCheckin.checkedInAt} (${currentCheckin.duration})</small>
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="alert alert-warning">
                <p class="mb-0">You are not currently checked in anywhere.</p>
            </div>
        `;
    }
}

// Submit quick check-in
function submitQuickCheckin() {
    const form = document.getElementById('quickCheckinForm');
    const formData = new FormData(form);
    
    // Add current location if available
    if (currentPosition) {
        formData.append('latitude', currentPosition.lat);
        formData.append('longitude', currentPosition.lng);
    }
    
    // Simulate API call
    showAlert('success', 'Checked in successfully!');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('quickCheckinModal'));
    modal.hide();
    
    // Refresh data
    loadRecentActivity();
    loadTimeline('today');
}

// Submit quick check-out
function submitQuickCheckout() {
    const form = document.getElementById('quickCheckoutForm');
    const formData = new FormData(form);
    
    // Simulate API call
    showAlert('success', 'Checked out successfully!');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('quickCheckoutModal'));
    modal.hide();
    
    // Refresh data
    loadRecentActivity();
    loadTimeline('today');
}

// Load recent activity
function loadRecentActivity() {
    const container = document.getElementById('recentActivity');
    
    // Simulate loading
    setTimeout(() => {
        const activities = [
            {
                type: 'checkin',
                location: 'Office',
                time: '09:30 AM',
                icon: 'fas fa-sign-in-alt text-success'
            },
            {
                type: 'checkout',
                location: 'Coffee Shop',
                time: '08:45 AM',
                icon: 'fas fa-sign-out-alt text-danger'
            },
            {
                type: 'visit',
                location: 'Gym',
                time: 'Yesterday',
                icon: 'fas fa-map-marker-alt text-warning'
            }
        ];
        
        container.innerHTML = activities.map(activity => `
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <i class="${activity.icon} fa-lg"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-0">${activity.location}</h6>
                    <small class="text-muted">${activity.time}</small>
                </div>
            </div>
        `).join('');
    }, 1000);
}

// Load timeline
function loadTimeline(period) {
    const container = document.getElementById('timeline');
    
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Show loading
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading timeline...</p>
        </div>
    `;
    
    // Simulate loading
    setTimeout(() => {
        const timelineItems = [
            {
                type: 'checkin',
                location: 'Office',
                time: '09:30 AM',
                description: 'Started work day'
            },
            {
                type: 'visit',
                location: 'Coffee Shop',
                time: '08:45 AM',
                description: 'Quick coffee break'
            },
            {
                type: 'checkout',
                location: 'Home',
                time: '08:00 AM',
                description: 'Left for work'
            }
        ];
        
        container.innerHTML = timelineItems.map(item => `
            <div class="timeline-item ${item.type}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${item.location}</h6>
                        <p class="text-muted mb-0">${item.description}</p>
                    </div>
                    <small class="text-muted">${item.time}</small>
                </div>
            </div>
        `).join('');
    }, 1000);
}

// Setup rating stars
function setupRatingStars() {
    document.querySelectorAll('.rating-stars').forEach(container => {
        const stars = container.querySelectorAll('i');
        const input = container.nextElementSibling;
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = index + 1;
                input.value = rating;
                
                // Update star display
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseover', function() {
                const rating = index + 1;
                
                // Highlight stars on hover
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        container.addEventListener('mouseleave', function() {
            const currentRating = parseInt(input.value) || 0;
            
            // Reset to current rating
            stars.forEach((s, i) => {
                if (i < currentRating) {
                    s.style.color = '#ffc107';
                    s.classList.add('active');
                } else {
                    s.style.color = '#ddd';
                    s.classList.remove('active');
                }
            });
        });
    });
}

// Show location info
function showLocationInfo(location) {
    // This would show a popup or navigate to location details
    console.log('Show info for location:', location);
}

// Utility function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initializeMap"></script>
@endsection