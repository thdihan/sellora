@extends('layouts.app')

@section('title', 'Team Location Map')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Team Location Map</h1>
            <p class="text-muted">View team member locations and activity</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('locations.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> All Locations
            </a>
            <button type="button" class="btn btn-primary" onclick="refreshMap()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Team Map Container -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Team Members Map</h6>
                </div>
                <div class="card-body">
                    <!-- Map would go here in a real implementation -->
                    <div id="team-map" style="height: 400px; background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 0.35rem;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-map fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Interactive map will be displayed here</p>
                                <small class="text-muted">Requires map integration (Google Maps, OpenStreetMap, etc.)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members List -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Team Members</h6>
                </div>
                <div class="card-body">
                    @if($teamMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Last Location</th>
                                        <th>Last Update</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamMembers as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                            {{ substr($member->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">{{ $member->name }}</div>
                                                        <div class="text-muted small">{{ $member->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($member->role)
                                                    <span class="badge badge-info">{{ $member->role }}</span>
                                                @else
                                                    <span class="text-muted">No role assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->locations->count() > 0)
                                                    @php $lastLocation = $member->locations->first() @endphp
                                                    <div class="font-weight-bold">{{ $lastLocation->name }}</div>
                                                    <div class="text-muted small">{{ $lastLocation->address ?? 'No address' }}</div>
                                                @else
                                                    <span class="text-muted">No location data</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->locations->count() > 0)
                                                    {{ $member->locations->first()->updated_at->diffForHumans() }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->locations->count() > 0)
                                                    @php 
                                                        $lastUpdate = $member->locations->first()->updated_at;
                                                        $hoursAgo = $lastUpdate->diffInHours(now());
                                                    @endphp
                                                    @if($hoursAgo < 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @elseif($hoursAgo < 8)
                                                        <span class="badge badge-warning">Recent</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($member->locations->count() > 0)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOnMap({{ $member->locations->first()->latitude ?? 0 }}, {{ $member->locations->first()->longitude ?? 0 }})">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('users.show', $member) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No team members found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Your Locations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Your Recent Locations</h6>
                </div>
                <div class="card-body">
                    @if($userLocations->count() > 0)
                        <div class="row">
                            @foreach($userLocations->take(6) as $location)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-success">
                                        <div class="card-body">
                                            <div class="font-weight-bold">{{ $location->name }}</div>
                                            <div class="text-muted small">{{ $location->address ?? 'No address' }}</div>
                                            <div class="text-success small mt-2">
                                                <i class="fas fa-clock"></i>
                                                {{ $location->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-map-pin fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted">No locations recorded yet</p>
                            <a href="{{ route('locations.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Location
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshMap() {
    // Refresh the page to get latest data
    window.location.reload();
}

function viewOnMap(lat, lng) {
    if (lat && lng) {
        // In a real implementation, this would center the map on the location
        alert(`Would show location at coordinates: ${lat}, ${lng}`);
    } else {
        alert('Location coordinates not available');
    }
}

// Auto-refresh every 5 minutes
setInterval(function() {
    refreshMap();
}, 300000); // 5 minutes
</script>
@endsection
