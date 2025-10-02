@extends('layouts.app')

@section('title', 'Team Map')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            👥 
                            Team Map
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Location Tracker</a></li>
                                <li class="breadcrumb-item active">Team Map</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Team Sidebar -->
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">Team Members</h6>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">🔍</span>
                                    <input type="text" class="form-control" id="searchTeam" placeholder="Search team...">
                                </div>
                                
                                <!-- Team List -->
                                <div id="teamList" class="list-group list-group-flush">
                                    <!-- TODO: Implement team member list -->
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-center">
                                            <div class="status-dot bg-success me-2"></div>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">Loading team...</div>
                                                <small class="text-muted">Please wait</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Map Container -->
                        <div class="col-md-9">
                            <div id="teamMap" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin="" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<style>
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}
</style>

<script>
// TODO: Implement team map functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('teamMap').setView([23.8103, 90.4125], 10); // Default to Dhaka, Bangladesh
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // TODO: Load team member locations
    // This should integrate with the LocationTrackingController
    console.log('Team Map initialized - TODO: Load team member locations');
    
    // Placeholder marker
    const marker = L.marker([23.8103, 90.4125]).addTo(map)
        .bindPopup('Team Map - Implementation Required')
        .openPopup();
});
</script>
@endsection