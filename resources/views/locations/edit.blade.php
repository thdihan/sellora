@extends('layouts.app')

@section('title', 'Edit Location')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Location: {{ $location->name }}
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.show', $location) }}">{{ $location->name }}</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('locations.update', $location) }}" method="POST" id="locationForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Location Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $location->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="home" {{ old('type', $location->type) === 'home' ? 'selected' : '' }}>Home</option>
                                        <option value="office" {{ old('type', $location->type) === 'office' ? 'selected' : '' }}>Office</option>
                                        <option value="client" {{ old('type', $location->type) === 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="meeting" {{ old('type', $location->type) === 'meeting' ? 'selected' : '' }}>Meeting</option>
                                        <option value="other" {{ old('type', $location->type) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of this location...">{{ old('description', $location->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location Coordinates -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-crosshairs me-2"></i>
                                    Location Coordinates
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                                   id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" required>
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                                   id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" required>
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-primary" onclick="getCurrentLocation()">
                                            <i class="fas fa-crosshairs me-1"></i> Use Current Location
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-info" onclick="showOnMap()">
                                            <i class="fas fa-map me-1"></i> Show on Map
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Map Preview -->
                                <div id="mapPreview" class="mt-3" style="display: none;">
                                    <div id="previewMap" style="height: 300px; border: 1px solid #dee2e6; border-radius: 0.375rem;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Address Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $location->address) }}" 
                                           placeholder="123 Main Street">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city', $location->city) }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" value="{{ old('state', $location->state) }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country', $location->country) }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                   id="postal_code" name="postal_code" value="{{ old('postal_code', $location->postal_code) }}">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-secondary mt-4" onclick="reverseGeocode()">
                                            <i class="fas fa-search me-1"></i> Auto-fill from Coordinates
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Additional Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="active" {{ old('status', $location->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $location->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="archived" {{ old('status', $location->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="is_favorite" 
                                                       name="is_favorite" value="1" {{ old('is_favorite', $location->is_favorite) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_favorite">
                                                    <i class="fas fa-star text-warning me-1"></i> Add to favorites
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes about this location...">{{ old('notes', $location->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Location Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="border rounded p-3">
                                            <h5 class="text-primary mb-1">{{ $location->visit_count }}</h5>
                                            <small class="text-muted">Total Visits</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border rounded p-3">
                                            <h5 class="text-success mb-1">{{ $location->created_at->format('M d, Y') }}</h5>
                                            <small class="text-muted">Created</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border rounded p-3">
                                            <h5 class="text-info mb-1">{{ $location->last_visited_at ? $location->last_visited_at->format('M d, Y') : 'Never' }}</h5>
                                            <small class="text-muted">Last Visit</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border rounded p-3">
                                            <h5 class="text-warning mb-1">{{ $location->updated_at->format('M d, Y') }}</h5>
                                            <small class="text-muted">Last Updated</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('locations.show', $location) }}" class="btn btn-secondary">
                                    ← Back to Location
                                </a>
                                <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-list me-1"></i> All Locations
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger me-2" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1"></i> Delete Location
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    💾 Update Location
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span style="color: #ffc107;">⚠</span>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the location <strong>"{{ $location->name }}"</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-1"></i>
                    This action cannot be undone. All associated visit data will also be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    × Cancel
                </button>
                <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this location? This action cannot be undone and will also delete all associated visit data.')">
                        <i class="fas fa-trash me-1"></i> Delete Location
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.text-danger {
    font-size: 0.875rem;
}

#previewMap {
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.border {
    border: 1px solid #dee2e6 !important;
}
</style>

<script>
let map;
let marker;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Show map preview if coordinates exist
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (lat && lng) {
        showOnMap();
    }
});

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting Location...';
        btn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                
                // Try to reverse geocode
                reverseGeocode();
                
                // Update map if visible
                if (document.getElementById('mapPreview').style.display !== 'none') {
                    updateMapPreview(lat, lng);
                }
                
                // Restore button
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                // Show success message
                showAlert('success', 'Location coordinates updated successfully!');
            },
            function(error) {
                // Restore button
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                let errorMessage = 'Error getting location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                showAlert('danger', errorMessage);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    } else {
        showAlert('warning', 'Geolocation is not supported by this browser.');
    }
}

// Show map preview
function showOnMap() {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (!lat || !lng) {
        showAlert('warning', 'Please enter latitude and longitude coordinates first.');
        return;
    }
    
    const mapPreview = document.getElementById('mapPreview');
    if (mapPreview.style.display === 'none') {
        mapPreview.style.display = 'block';
        updateMapPreview(lat, lng);
    } else {
        mapPreview.style.display = 'none';
    }
}

function updateMapPreview(lat, lng) {
    // This would integrate with Google Maps or another mapping service
    const mapElement = document.getElementById('previewMap');
    mapElement.innerHTML = `
        <div class="text-center p-4">
            <span style="font-size: 3rem; color: #0d6efd;" class="mb-3 d-block">📍</span>
            <h6 class="text-muted">Map Preview</h6>
            <p class="text-muted mb-3">Coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
            <button class="btn btn-sm btn-primary" onclick="openInMaps(${lat}, ${lng})">
                <i class="fas fa-external-link-alt me-1"></i> Open in Maps
            </button>
        </div>
    `;
}

function openInMaps(lat, lng) {
    const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    window.open(url, '_blank');
}

// Reverse geocoding
function reverseGeocode() {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (!lat || !lng) {
        showAlert('warning', 'Please enter latitude and longitude coordinates first.');
        return;
    }
    
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
    btn.disabled = true;
    
    // This would integrate with a geocoding service
    // For now, we'll simulate the process
    setTimeout(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        showAlert('info', 'Reverse geocoding completed. Please verify the address information.');
    }, 1500);
}

// Form validation
document.getElementById('locationForm').addEventListener('submit', function(e) {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (lat < -90 || lat > 90) {
        e.preventDefault();
        showAlert('danger', 'Latitude must be between -90 and 90 degrees.');
        return;
    }
    
    if (lng < -180 || lng > 180) {
        e.preventDefault();
        showAlert('danger', 'Longitude must be between -180 and 180 degrees.');
        return;
    }
});

// Delete confirmation
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
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

// Coordinate input validation
document.getElementById('latitude').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value < -90 || value > 90) {
        this.setCustomValidity('Latitude must be between -90 and 90 degrees');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('longitude').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value < -180 || value > 180) {
        this.setCustomValidity('Longitude must be between -180 and 180 degrees');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsection