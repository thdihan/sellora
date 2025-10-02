@extends('layouts.app')

@section('title', 'Add New Location')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            📍 
                            Add New Location
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                <li class="breadcrumb-item active">Add New</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6>⚠️ Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('locations.store') }}" method="POST" id="locationForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Location Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
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
                                        <option value="home" {{ old('type') === 'home' ? 'selected' : '' }}>Home</option>
                                        <option value="office" {{ old('type') === 'office' ? 'selected' : '' }}>Office</option>
                                        <option value="client" {{ old('type') === 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="meeting" {{ old('type') === 'meeting' ? 'selected' : '' }}>Meeting</option>
                                        <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
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
                                      placeholder="Brief description of this location...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location Coordinates -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    🎯 
                                    Location Coordinates
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                                   id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                                   id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-primary" onclick="getCurrentLocation()">
                                            🎯 Use Current Location
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-info" onclick="showOnMap()">
                                            🗺️ Show on Map
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
                                    📍 
                                    Address Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}" 
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
                                                   id="city" name="city" value="{{ old('city') }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" value="{{ old('state') }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country') }}">
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
                                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-secondary mt-4" onclick="reverseGeocode()">
                                            🔍 Auto-fill from Coordinates
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    ℹ️ 
                                    Additional Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
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
                                                       name="is_favorite" value="1" {{ old('is_favorite') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_favorite">
                                                    ⭐ Add to favorites
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes about this location...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                Back to Locations
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="saveDraft()">
                                    💾 Save as Draft
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    ➕ Add Location
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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

.btn-group .btn {
    border-radius: 0.375rem;
}
</style>

<script>
let map;
let marker;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill current location if coordinates are empty
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    if (!latInput.value && !lngInput.value) {
        // Optionally get current location on page load
        // getCurrentLocation();
    }
});

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '🔄 Getting Location...';
        btn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                
                // Auto-fill name if empty
                const nameInput = document.getElementById('name');
                if (!nameInput.value) {
                    nameInput.value = 'Current Location';
                }
                
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
            <span style="font-size: 3rem; color: var(--bs-primary);" class="mb-3 d-block">📍</span>
            <h6 class="text-muted">Map Preview</h6>
            <p class="text-muted mb-3">Coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
            <button class="btn btn-sm btn-primary" onclick="openInMaps(${lat}, ${lng})">
                🔗 Open in Maps
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
    btn.innerHTML = '🔄 Loading...';
    btn.disabled = true;
    
    // This would integrate with a geocoding service
    // For now, we'll simulate the process
    setTimeout(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Simulate address data
        document.getElementById('address').value = `Address near ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        document.getElementById('city').value = 'Sample City';
        document.getElementById('state').value = 'Sample State';
        document.getElementById('country').value = 'Sample Country';
        
        showAlert('info', 'Address information has been auto-filled. Please verify and update as needed.');
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

// Save as draft
function saveDraft() {
    const formData = new FormData(document.getElementById('locationForm'));
    formData.append('status', 'inactive'); // Save drafts as inactive
    
    fetch('{{ route("locations.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Location saved as draft successfully!');
            setTimeout(() => {
                window.location.href = '{{ route("locations.index") }}';
            }, 1500);
        } else {
            showAlert('danger', 'Error saving draft: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error saving draft.');
    });
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