@extends('layouts.app')

@section('title', 'Export Locations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            Export Locations
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                <li class="breadcrumb-item active">Export</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <form id="exportForm" onsubmit="handleExport(event)">
                        <div class="row">
                            <!-- Export Format -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            Export Format
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="format" id="formatCSV" value="csv" checked>
                                            <label class="form-check-label" for="formatCSV">
                                                <span class="text-success me-2">📊</span>
                                                <strong>CSV (Comma Separated Values)</strong>
                                                <br>
                                                <small class="text-muted">Best for spreadsheet applications like Excel</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="format" id="formatJSON" value="json">
                                            <label class="form-check-label" for="formatJSON">
                                                <span class="text-info me-2">📄</span>
                                                <strong>JSON (JavaScript Object Notation)</strong>
                                                <br>
                                                <small class="text-muted">Best for developers and API integration</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="format" id="formatXML" value="xml">
                                            <label class="form-check-label" for="formatXML">
                                                <span class="text-warning me-2">📄</span>
                                                <strong>XML (Extensible Markup Language)</strong>
                                                <br>
                                                <small class="text-muted">Best for system integration and data exchange</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="format" id="formatPDF" value="pdf">
                                            <label class="form-check-label" for="formatPDF">
                                                <span class="text-danger me-2">📄</span>
                                                <strong>PDF (Portable Document Format)</strong>
                                                <br>
                                                <small class="text-muted">Best for reports and documentation</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" id="formatKML" value="kml">
                                            <label class="form-check-label" for="formatKML">
                                                <span class="text-primary me-2">🗺️</span>
                                                <strong>KML (Keyhole Markup Language)</strong>
                                                <br>
                                                <small class="text-muted">Best for Google Earth and mapping applications</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Export Options -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            ⚙️
                                            Export Options
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Date Range -->
                                        <div class="mb-3">
                                            <label class="form-label">Date Range</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="date" class="form-control" id="startDate" name="start_date">
                                                    <small class="text-muted">Start Date</small>
                                                </div>
                                                <div class="col-6">
                                                    <input type="date" class="form-control" id="endDate" name="end_date">
                                                    <small class="text-muted">End Date</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location Types -->
                                        <div class="mb-3">
                                            <label class="form-label">Location Types</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="typeAll" checked onchange="toggleAllTypes()">
                                                <label class="form-check-label" for="typeAll">
                                                    <strong>All Types</strong>
                                                </label>
                                            </div>
                                            <div class="ms-3">
                                                <div class="form-check">
                                                    <input class="form-check-input location-type" type="checkbox" name="types[]" value="home" id="typeHome" checked>
                                                    <label class="form-check-label" for="typeHome">
                                                        <span class="text-primary me-1">🏠</span> Home
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input location-type" type="checkbox" name="types[]" value="office" id="typeOffice" checked>
                                                    <label class="form-check-label" for="typeOffice">
                                                        <span class="text-info me-1">🏢</span> Office
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input location-type" type="checkbox" name="types[]" value="client" id="typeClient" checked>
                                                    <label class="form-check-label" for="typeClient">
                                                        <span class="text-success me-1">🤝</span> Client
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input location-type" type="checkbox" name="types[]" value="meeting" id="typeMeeting" checked>
                                                    <label class="form-check-label" for="typeMeeting">
                                                        <span class="text-warning me-1">👥</span> Meeting
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input location-type" type="checkbox" name="types[]" value="other" id="typeOther" checked>
                                                    <label class="form-check-label" for="typeOther">
                                                        <span class="text-secondary me-1">📍</span> Other
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="all">All Statuses</option>
                                                <option value="active">Active Only</option>
                                                <option value="inactive">Inactive Only</option>
                                            </select>
                                        </div>

                                        <!-- Favorites Only -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="favorites_only" id="favoritesOnly">
                                            <label class="form-check-label" for="favoritesOnly">
                                                <span class="text-warning me-1">⭐</span>
                                                Favorites Only
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Fields Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            📋
                                            Data Fields to Include
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h6>Basic Information</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="name" id="fieldName" checked>
                                                    <label class="form-check-label" for="fieldName">Name</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="description" id="fieldDescription" checked>
                                                    <label class="form-check-label" for="fieldDescription">Description</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="type" id="fieldType" checked>
                                                    <label class="form-check-label" for="fieldType">Type</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="status" id="fieldStatus" checked>
                                                    <label class="form-check-label" for="fieldStatus">Status</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Location Data</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="latitude" id="fieldLatitude" checked>
                                                    <label class="form-check-label" for="fieldLatitude">Latitude</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="longitude" id="fieldLongitude" checked>
                                                    <label class="form-check-label" for="fieldLongitude">Longitude</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="accuracy" id="fieldAccuracy">
                                                    <label class="form-check-label" for="fieldAccuracy">Accuracy</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="altitude" id="fieldAltitude">
                                                    <label class="form-check-label" for="fieldAltitude">Altitude</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Address Information</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="address" id="fieldAddress" checked>
                                                    <label class="form-check-label" for="fieldAddress">Address</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="city" id="fieldCity" checked>
                                                    <label class="form-check-label" for="fieldCity">City</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="state" id="fieldState">
                                                    <label class="form-check-label" for="fieldState">State</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="country" id="fieldCountry">
                                                    <label class="form-check-label" for="fieldCountry">Country</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Statistics & Metadata</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="visit_count" id="fieldVisitCount" checked>
                                                    <label class="form-check-label" for="fieldVisitCount">Visit Count</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="last_visited_at" id="fieldLastVisited">
                                                    <label class="form-check-label" for="fieldLastVisited">Last Visited</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="is_favorite" id="fieldIsFavorite">
                                                    <label class="form-check-label" for="fieldIsFavorite">Is Favorite</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="created_at" id="fieldCreatedAt">
                                                    <label class="form-check-label" for="fieldCreatedAt">Created Date</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllFields()">
                                                Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNoneFields()">
                                                Select None
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="selectEssentialFields()">
                                                Essential Only
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Include Visit Data -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            📜
                                            Visit Data Options
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="include_visits" id="includeVisits" onchange="toggleVisitOptions()">
                                            <label class="form-check-label" for="includeVisits">
                                                <strong>Include Visit History</strong>
                                                <br>
                                                <small class="text-muted">Export detailed visit records for each location</small>
                                            </label>
                                        </div>
                                        <div id="visitOptions" style="display: none;" class="ms-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="visited_at" checked>
                                                        <label class="form-check-label">Visit Date/Time</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="duration_minutes">
                                                        <label class="form-check-label">Duration</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="purpose">
                                                        <label class="form-check-label">Purpose</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="mood_rating">
                                                        <label class="form-check-label">Mood Rating</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="productivity_rating">
                                                        <label class="form-check-label">Productivity Rating</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="visit_fields[]" value="notes">
                                                        <label class="form-check-label">Notes</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Summary -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            ℹ️
                                            Export Summary
                                        </h6>
                                        <div id="exportSummary">
                                            <p class="mb-1"><strong>Estimated Records:</strong> <span id="estimatedRecords">{{ $totalLocations ?? 0 }}</span> locations</p>
                                            <p class="mb-1"><strong>Selected Format:</strong> <span id="selectedFormat">CSV</span></p>
                                            <p class="mb-1"><strong>Selected Fields:</strong> <span id="selectedFields">4 fields</span></p>
                                            <p class="mb-0"><strong>Estimated File Size:</strong> <span id="estimatedSize">~50 KB</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                    Back to Locations
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-info" onclick="previewExport()">
                                    👁️ Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    📥 Export Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Preview Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            📊 Data Preview
                        </h5>
                        <small class="text-muted">Preview of locations to be exported</small>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($locations) && $locations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Visits</th>
                                        <th>Last Visited</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $location)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($location->is_favorite)
                                                        <span class="text-warning me-2">⭐</span>
                                                    @endif
                                                    <strong>{{ $location->name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $location->type === 'home' ? 'primary' : ($location->type === 'office' ? 'info' : ($location->type === 'client' ? 'success' : ($location->type === 'meeting' ? 'warning' : 'secondary'))) }}">
                                                    {{ ucfirst($location->type) }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($location->address, 30) }}</td>
                                            <td>{{ $location->city }}</td>
                                            <td>
                                                <span class="badge bg-{{ $location->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($location->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $location->visit_count ?? 0 }}</td>
                                            <td>{{ $location->last_visited_at ? $location->last_visited_at->format('M j, Y') : 'Never' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Custom Pagination -->
                @if($locations->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $locations->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                    </div>
                @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No locations found</h5>
                            <p class="text-muted">Adjust your filters to see location data for export.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    👁️
                    Export Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="proceedWithExport()">
                    📥 Proceed with Export
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

.form-check-label {
    cursor: pointer;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

#previewContent {
    max-height: 400px;
    overflow-y: auto;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    font-family: monospace;
    font-size: 12px;
}
</style>

<script>
// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    updateExportSummary();
    
    // Add event listeners for real-time updates
    document.querySelectorAll('input[name="format"]').forEach(radio => {
        radio.addEventListener('change', updateExportSummary);
    });
    
    document.querySelectorAll('input[name="fields[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateExportSummary);
    });
    
    document.querySelectorAll('.location-type').forEach(checkbox => {
        checkbox.addEventListener('change', updateTypeSelection);
    });
});

// Toggle all location types
function toggleAllTypes() {
    const allTypesCheckbox = document.getElementById('typeAll');
    const typeCheckboxes = document.querySelectorAll('.location-type');
    
    typeCheckboxes.forEach(checkbox => {
        checkbox.checked = allTypesCheckbox.checked;
    });
    
    updateExportSummary();
}

// Update type selection
function updateTypeSelection() {
    const typeCheckboxes = document.querySelectorAll('.location-type');
    const allTypesCheckbox = document.getElementById('typeAll');
    
    const checkedTypes = Array.from(typeCheckboxes).filter(cb => cb.checked);
    allTypesCheckbox.checked = checkedTypes.length === typeCheckboxes.length;
    
    updateExportSummary();
}

// Toggle visit options
function toggleVisitOptions() {
    const includeVisits = document.getElementById('includeVisits');
    const visitOptions = document.getElementById('visitOptions');
    
    visitOptions.style.display = includeVisits.checked ? 'block' : 'none';
    updateExportSummary();
}

// Field selection functions
function selectAllFields() {
    document.querySelectorAll('input[name="fields[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateExportSummary();
}

function selectNoneFields() {
    document.querySelectorAll('input[name="fields[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateExportSummary();
}

function selectEssentialFields() {
    const essentialFields = ['name', 'type', 'latitude', 'longitude', 'address', 'city'];
    
    document.querySelectorAll('input[name="fields[]"]').forEach(checkbox => {
        checkbox.checked = essentialFields.includes(checkbox.value);
    });
    updateExportSummary();
}

// Update export summary
function updateExportSummary() {
    const format = document.querySelector('input[name="format"]:checked').value;
    const selectedFields = document.querySelectorAll('input[name="fields[]"]:checked').length;
    const selectedTypes = document.querySelectorAll('.location-type:checked').length;
    
    // Update summary display
    document.getElementById('selectedFormat').textContent = format.toUpperCase();
    document.getElementById('selectedFields').textContent = `${selectedFields} fields`;
    
    // Estimate file size (rough calculation)
    const baseSize = selectedFields * 20; // 20 bytes per field average
    const totalRecords = Math.floor({{ $totalLocations ?? 100 }} * (selectedTypes / 5)); // Adjust based on type selection
    const estimatedBytes = baseSize * totalRecords;
    
    let sizeText;
    if (estimatedBytes < 1024) {
        sizeText = `~${estimatedBytes} B`;
    } else if (estimatedBytes < 1024 * 1024) {
        sizeText = `~${Math.round(estimatedBytes / 1024)} KB`;
    } else {
        sizeText = `~${Math.round(estimatedBytes / (1024 * 1024))} MB`;
    }
    
    document.getElementById('estimatedRecords').textContent = `${totalRecords} locations`;
    document.getElementById('estimatedSize').textContent = sizeText;
}

// Preview export
function previewExport() {
    const formData = new FormData(document.getElementById('exportForm'));
    formData.append('preview', 'true');
    
    // Show loading state
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="text-center">🔄 Generating preview...</div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    // Simulate API call for preview
    setTimeout(() => {
        const format = formData.get('format');
        let previewData = '';
        
        switch (format) {
            case 'csv':
                previewData = generateCSVPreview();
                break;
            case 'json':
                previewData = generateJSONPreview();
                break;
            case 'xml':
                previewData = generateXMLPreview();
                break;
            case 'kml':
                previewData = generateKMLPreview();
                break;
            default:
                previewData = 'Preview not available for this format.';
        }
        
        previewContent.innerHTML = `<pre>${previewData}</pre>`;
    }, 1000);
}

// Generate preview data (sample)
function generateCSVPreview() {
    return `Name,Type,Latitude,Longitude,Address,City\n"Home Office","office",40.7128,-74.0060,"123 Main St","New York"\n"Client Site A","client",40.7589,-73.9851,"456 Park Ave","New York"\n"Coffee Shop","meeting",40.7505,-73.9934,"789 Broadway","New York"\n...`;
}

function generateJSONPreview() {
    return `[\n  {\n    "name": "Home Office",\n    "type": "office",\n    "latitude": 40.7128,\n    "longitude": -74.0060,\n    "address": "123 Main St",\n    "city": "New York"\n  },\n  {\n    "name": "Client Site A",\n    "type": "client",\n    "latitude": 40.7589,\n    "longitude": -73.9851,\n    "address": "456 Park Ave",\n    "city": "New York"\n  }\n]`;
}

function generateXMLPreview() {
    return '&lt;?xml version="1.0" encoding="UTF-8"?&gt;' + '\n&lt;locations&gt;\n  &lt;location&gt;\n    &lt;name&gt;Home Office&lt;/name&gt;\n    &lt;type&gt;office&lt;/type&gt;\n    &lt;latitude&gt;40.7128&lt;/latitude&gt;\n    &lt;longitude&gt;-74.0060&lt;/longitude&gt;\n    &lt;address&gt;123 Main St&lt;/address&gt;\n    &lt;city&gt;New York&lt;/city&gt;\n  &lt;/location&gt;\n  &lt;location&gt;\n    &lt;name&gt;Client Site A&lt;/name&gt;\n    &lt;type&gt;client&lt;/type&gt;\n    &lt;latitude&gt;40.7589&lt;/latitude&gt;\n    &lt;longitude&gt;-73.9851&lt;/longitude&gt;\n    &lt;address&gt;456 Park Ave&lt;/address&gt;\n    &lt;city&gt;New York&lt;/city&gt;\n  &lt;/location&gt;\n&lt;/locations&gt;';
}

function generateKMLPreview() {
    return '&lt;?xml version="1.0" encoding="UTF-8"?&gt;' + '\n&lt;kml xmlns="http://www.opengis.net/kml/2.2"&gt;\n  &lt;Document&gt;\n    &lt;Placemark&gt;\n      &lt;name&gt;Home Office&lt;/name&gt;\n      &lt;description&gt;office&lt;/description&gt;\n      &lt;Point&gt;\n        &lt;coordinates&gt;-74.0060,40.7128,0&lt;/coordinates&gt;\n      &lt;/Point&gt;\n    &lt;/Placemark&gt;\n  &lt;/Document&gt;\n&lt;/kml&gt;';
}

// Handle export form submission
function handleExport(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const selectedFields = document.querySelectorAll('input[name="fields[]"]:checked');
    
    if (selectedFields.length === 0) {
        showAlert('warning', 'Please select at least one field to export.');
        return;
    }
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
    submitButton.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        // Create download link
        const format = formData.get('format');
        const filename = `locations_export_${new Date().toISOString().split('T')[0]}.${format}`;
        
        // In a real application, this would be an actual file download
        showAlert('success', `Export completed! File: ${filename}`);
        
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 2000);
}

// Proceed with export from preview modal
function proceedWithExport() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
    modal.hide();
    
    // Trigger the actual export
    document.getElementById('exportForm').dispatchEvent(new Event('submit'));
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