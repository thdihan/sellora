@extends('layouts.app')

@section('title', 'Location Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            ⚙️ 
                            Location Settings
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                <li class="breadcrumb-item active">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <form id="settingsForm" onsubmit="saveSettings(event)">
                        <div class="row">
                            <!-- General Settings -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🎛️ 
                                            General Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Auto Location Tracking -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoTracking" name="auto_tracking" checked>
                                                <label class="form-check-label" for="autoTracking">
                                                    <strong>Auto Location Tracking</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Automatically track your location when the app is active</small>
                                        </div>

                                        <!-- Background Tracking -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="backgroundTracking" name="background_tracking">
                                                <label class="form-check-label" for="backgroundTracking">
                                                    <strong>Background Tracking</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Continue tracking location when app is in background</small>
                                        </div>

                                        <!-- Auto Check-in -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoCheckin" name="auto_checkin" checked>
                                                <label class="form-check-label" for="autoCheckin">
                                                    <strong>Auto Check-in</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Automatically check-in when arriving at known locations</small>
                                        </div>

                                        <!-- Auto Check-out -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoCheckout" name="auto_checkout" checked>
                                                <label class="form-check-label" for="autoCheckout">
                                                    <strong>Auto Check-out</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Automatically check-out when leaving locations</small>
                                        </div>

                                        <!-- Location Sharing -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="locationSharing" name="location_sharing">
                                                <label class="form-check-label" for="locationSharing">
                                                    <strong>Location Sharing</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Allow sharing your location with team members</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accuracy & Performance -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🎯 
                                            Accuracy & Performance
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- GPS Accuracy -->
                                        <div class="mb-3">
                                            <label for="gpsAccuracy" class="form-label">
                                                <strong>GPS Accuracy Level</strong>
                                            </label>
                                            <select class="form-select" id="gpsAccuracy" name="gps_accuracy">
                                                <option value="high">High (Best accuracy, more battery usage)</option>
                                                <option value="medium" selected>Medium (Balanced accuracy and battery)</option>
                                                <option value="low">Low (Lower accuracy, less battery usage)</option>
                                            </select>
                                            <small class="text-muted">Higher accuracy uses more battery power</small>
                                        </div>

                                        <!-- Update Frequency -->
                                        <div class="mb-3">
                                            <label for="updateFrequency" class="form-label">
                                                <strong>Location Update Frequency</strong>
                                            </label>
                                            <select class="form-select" id="updateFrequency" name="update_frequency">
                                                <option value="5">Every 5 seconds (High frequency)</option>
                                                <option value="15">Every 15 seconds (Medium frequency)</option>
                                                <option value="30" selected>Every 30 seconds (Balanced)</option>
                                                <option value="60">Every 1 minute (Low frequency)</option>
                                                <option value="300">Every 5 minutes (Very low frequency)</option>
                                            </select>
                                            <small class="text-muted">More frequent updates provide better tracking but use more battery</small>
                                        </div>

                                        <!-- Distance Threshold -->
                                        <div class="mb-3">
                                            <label for="distanceThreshold" class="form-label">
                                                <strong>Minimum Distance for Update</strong>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="distanceThreshold" name="distance_threshold" value="10" min="1" max="1000">
                                                <span class="input-group-text">meters</span>
                                            </div>
                                            <small class="text-muted">Only update location if moved more than this distance</small>
                                        </div>

                                        <!-- Geofence Radius -->
                                        <div class="mb-3">
                                            <label for="geofenceRadius" class="form-label">
                                                <strong>Geofence Radius</strong>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="geofenceRadius" name="geofence_radius" value="50" min="10" max="500">
                                                <span class="input-group-text">meters</span>
                                            </div>
                                            <small class="text-muted">Radius for automatic check-in/check-out detection</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Notifications -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🔔 
                                            Notifications
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Check-in Notifications -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="checkinNotifications" name="checkin_notifications" checked>
                                                <label class="form-check-label" for="checkinNotifications">
                                                    <strong>Check-in Notifications</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Get notified when you check-in to a location</small>
                                        </div>

                                        <!-- Check-out Notifications -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="checkoutNotifications" name="checkout_notifications" checked>
                                                <label class="form-check-label" for="checkoutNotifications">
                                                    <strong>Check-out Notifications</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Get notified when you check-out from a location</small>
                                        </div>

                                        <!-- New Location Notifications -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="newLocationNotifications" name="new_location_notifications" checked>
                                                <label class="form-check-label" for="newLocationNotifications">
                                                    <strong>New Location Suggestions</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Get suggestions to save frequently visited places</small>
                                        </div>

                                        <!-- Daily Summary -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="dailySummary" name="daily_summary">
                                                <label class="form-check-label" for="dailySummary">
                                                    <strong>Daily Summary</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Receive daily summary of your location activity</small>
                                        </div>

                                        <!-- Weekly Report -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="weeklyReport" name="weekly_report">
                                                <label class="form-check-label" for="weeklyReport">
                                                    <strong>Weekly Report</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Receive weekly analytics report via email</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Privacy & Security -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🛡️ 
                                            Privacy & Security
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Data Retention -->
                                        <div class="mb-3">
                                            <label for="dataRetention" class="form-label">
                                                <strong>Data Retention Period</strong>
                                            </label>
                                            <select class="form-select" id="dataRetention" name="data_retention">
                                                <option value="30">30 days</option>
                                                <option value="90">90 days</option>
                                                <option value="180">6 months</option>
                                                <option value="365" selected>1 year</option>
                                                <option value="0">Keep forever</option>
                                            </select>
                                            <small class="text-muted">How long to keep location history data</small>
                                        </div>

                                        <!-- Anonymous Analytics -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="anonymousAnalytics" name="anonymous_analytics" checked>
                                                <label class="form-check-label" for="anonymousAnalytics">
                                                    <strong>Anonymous Analytics</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Help improve the app by sharing anonymous usage data</small>
                                        </div>

                                        <!-- Location History Visibility -->
                                        <div class="mb-3">
                                            <label for="historyVisibility" class="form-label">
                                                <strong>Location History Visibility</strong>
                                            </label>
                                            <select class="form-select" id="historyVisibility" name="history_visibility">
                                                <option value="private" selected>Private (Only you)</option>
                                                <option value="team">Team members</option>
                                                <option value="public">Public (Anyone with link)</option>
                                            </select>
                                            <small class="text-muted">Who can view your location history</small>
                                        </div>

                                        <!-- Export Data -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <strong>Data Export</strong>
                                            </label>
                                            <div class="d-grid">
                                                <a href="{{ route('locations.export') }}" class="btn btn-outline-primary btn-sm">
                                                    📥 
                                                    Export My Data
                                                </a>
                                            </div>
                                            <small class="text-muted">Download all your location data</small>
                                        </div>

                                        <!-- Delete All Data -->
                                        <div class="mb-3">
                                            <label class="form-label text-danger">
                                                <strong>Danger Zone</strong>
                                            </label>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteAllData()">
                                                    🗑️ 
                                                    Delete All Location Data
                                                </button>
                                            </div>
                                            <small class="text-muted">Permanently delete all your location data (cannot be undone)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Map Settings -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🗺️ 
                                            Map Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Default Map Type -->
                                        <div class="mb-3">
                                            <label for="defaultMapType" class="form-label">
                                                <strong>Default Map Type</strong>
                                            </label>
                                            <select class="form-select" id="defaultMapType" name="default_map_type">
                                                <option value="roadmap" selected>Roadmap</option>
                                                <option value="satellite">Satellite</option>
                                                <option value="hybrid">Hybrid</option>
                                                <option value="terrain">Terrain</option>
                                            </select>
                                        </div>

                                        <!-- Default Zoom Level -->
                                        <div class="mb-3">
                                            <label for="defaultZoom" class="form-label">
                                                <strong>Default Zoom Level</strong>
                                            </label>
                                            <input type="range" class="form-range" id="defaultZoom" name="default_zoom" min="1" max="20" value="15" oninput="updateZoomDisplay(this.value)">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">World</small>
                                                <small class="text-muted">Zoom: <span id="zoomDisplay">15</span></small>
                                                <small class="text-muted">Street</small>
                                            </div>
                                        </div>

                                        <!-- Show Traffic -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showTraffic" name="show_traffic">
                                                <label class="form-check-label" for="showTraffic">
                                                    <strong>Show Traffic Layer</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Display real-time traffic information on maps</small>
                                        </div>

                                        <!-- Show Transit -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showTransit" name="show_transit">
                                                <label class="form-check-label" for="showTransit">
                                                    <strong>Show Transit Layer</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Display public transportation routes</small>
                                        </div>

                                        <!-- Cluster Markers -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="clusterMarkers" name="cluster_markers" checked>
                                                <label class="form-check-label" for="clusterMarkers">
                                                    <strong>Cluster Nearby Markers</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Group nearby location markers for better visibility</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Integration Settings -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            🔌 
                                            Integrations
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Google Calendar -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="googleCalendar" name="google_calendar_integration">
                                                <label class="form-check-label" for="googleCalendar">
                                                    <strong>Google Calendar Integration</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Sync location visits with calendar events</small>
                                        </div>

                                        <!-- Slack Notifications -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="slackIntegration" name="slack_integration">
                                                <label class="form-check-label" for="slackIntegration">
                                                    <strong>Slack Notifications</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Send check-in/out notifications to Slack</small>
                                        </div>

                                        <!-- Webhook URL -->
                                        <div class="mb-3">
                                            <label for="webhookUrl" class="form-label">
                                                <strong>Webhook URL</strong>
                                            </label>
                                            <input type="url" class="form-control" id="webhookUrl" name="webhook_url" placeholder="https://your-webhook-url.com">
                                            <small class="text-muted">Send location events to external webhook</small>
                                        </div>

                                        <!-- API Access -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <strong>API Access</strong>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="apiKey" value="{{ $apiKey ?? 'sk_' . Str::random(32) }}" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="regenerateApiKey()">
                                                    🔄
                                                </button>
                                            </div>
                                            <small class="text-muted">API key for accessing your location data programmatically</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                    ← Back to Locations
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetToDefaults()">
                                    ↶ Reset to Defaults
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    💾 Save Settings
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
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    ⚠️ 
                    Delete All Location Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>This action cannot be undone!</strong></p>
                <p>This will permanently delete:</p>
                <ul>
                    <li>All your saved locations</li>
                    <li>All visit history and check-ins</li>
                    <li>All analytics and reports</li>
                    <li>All location-related settings</li>
                </ul>
                <p>Type <strong>DELETE</strong> to confirm:</p>
                <input type="text" class="form-control" id="deleteConfirmation" placeholder="Type DELETE to confirm">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton" onclick="deleteAllData()" disabled>
                    🗑️ Delete All Data
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280,0,0,.25%29'/%3e%3c/svg%3e");
}

.form-switch .form-check-input:checked {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255,255,255,1.0%29'/%3e%3c/svg%3e");
}

.form-range::-webkit-slider-thumb {
    background-color: #007bff;
}

.form-range::-moz-range-thumb {
    background-color: #007bff;
    border: none;
}
</style>

<script>
// Initialize settings
document.addEventListener('DOMContentLoaded', function() {
    // Load saved settings
    loadSettings();
    
    // Setup delete confirmation
    const deleteInput = document.getElementById('deleteConfirmation');
    const deleteButton = document.getElementById('confirmDeleteButton');
    
    deleteInput.addEventListener('input', function() {
        deleteButton.disabled = this.value !== 'DELETE';
    });
});

// Update zoom display
function updateZoomDisplay(value) {
    document.getElementById('zoomDisplay').textContent = value;
}

// Load settings from server
function loadSettings() {
    // This would typically load from the server
    // For demo purposes, we'll use localStorage
    const savedSettings = localStorage.getItem('locationSettings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        
        // Apply saved settings to form
        Object.keys(settings).forEach(key => {
            const element = document.querySelector(`[name="${key}"]`);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = settings[key];
                } else {
                    element.value = settings[key];
                }
            }
        });
    }
}

// Save settings
function saveSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const settings = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        settings[key] = value;
    }
    
    // Handle checkboxes (they won't be in FormData if unchecked)
    const checkboxes = event.target.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        settings[checkbox.name] = checkbox.checked;
    });
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '🔄 Saving...';
    submitButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Save to localStorage for demo
        localStorage.setItem('locationSettings', JSON.stringify(settings));
        
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        showAlert('success', 'Settings saved successfully!');
    }, 1000);
}

// Reset to defaults
function resetToDefaults() {
    if (confirm('Are you sure you want to reset all settings to their default values?')) {
        // Clear saved settings
        localStorage.removeItem('locationSettings');
        
        // Reset form to defaults
        document.getElementById('settingsForm').reset();
        
        // Set specific defaults
        document.getElementById('autoTracking').checked = true;
        document.getElementById('autoCheckin').checked = true;
        document.getElementById('autoCheckout').checked = true;
        document.getElementById('gpsAccuracy').value = 'medium';
        document.getElementById('updateFrequency').value = '30';
        document.getElementById('distanceThreshold').value = '10';
        document.getElementById('geofenceRadius').value = '50';
        document.getElementById('checkinNotifications').checked = true;
        document.getElementById('checkoutNotifications').checked = true;
        document.getElementById('newLocationNotifications').checked = true;
        document.getElementById('dataRetention').value = '365';
        document.getElementById('anonymousAnalytics').checked = true;
        document.getElementById('historyVisibility').value = 'private';
        document.getElementById('defaultMapType').value = 'roadmap';
        document.getElementById('defaultZoom').value = '15';
        document.getElementById('clusterMarkers').checked = true;
        
        updateZoomDisplay('15');
        showAlert('info', 'Settings reset to defaults.');
    }
}

// Confirm delete all data
function confirmDeleteAllData() {
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
    
    // Reset confirmation input
    document.getElementById('deleteConfirmation').value = '';
    document.getElementById('confirmDeleteButton').disabled = true;
}

// Delete all data
function deleteAllData() {
    const confirmation = document.getElementById('deleteConfirmation').value;
    
    if (confirmation === 'DELETE') {
        // Show loading state
        const deleteButton = document.getElementById('confirmDeleteButton');
        deleteButton.innerHTML = '🔄 Deleting...';
        deleteButton.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Clear all data
            localStorage.removeItem('locationSettings');
            
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            modal.hide();
            
            showAlert('success', 'All location data has been deleted.');
            
            // Reset button
            deleteButton.innerHTML = '🗑️ Delete All Data';
            deleteButton.disabled = false;
        }, 2000);
    }
}

// Regenerate API key
function regenerateApiKey() {
    if (confirm('Are you sure you want to regenerate your API key? This will invalidate the current key.')) {
        const apiKeyInput = document.getElementById('apiKey');
        const newKey = 'sk_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        apiKeyInput.value = newKey;
        
        showAlert('info', 'API key regenerated. Make sure to update any applications using the old key.');
    }
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