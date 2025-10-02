@extends('layouts.app')

@section('title', 'Presentation Auto Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Presentation Auto Reports</h1>
                <div>
                    <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                        ← Back to Presentations
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-presentation fa-2x text-primary mb-2"></i>
                            <h4 class="card-title">{{ $totalPresentations }}</h4>
                            <p class="card-text text-muted">Total Presentations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-eye fa-2x text-success mb-2"></i>
                            <h4 class="card-title">{{ number_format($totalViews) }}</h4>
                            <p class="card-text text-muted">Total Views</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-download fa-2x text-info mb-2"></i>
                            <h4 class="card-title">{{ number_format($totalDownloads) }}</h4>
                            <p class="card-text text-muted">Total Downloads</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-star fa-2x text-warning mb-2"></i>
                            <h4 class="card-title">{{ number_format($avgRating, 1) }}</h4>
                            <p class="card-text text-muted">Average Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto Reports Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Presentation Performance Reports</h5>
                </div>
                <div class="card-body">
                    @if($presentations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Created</th>
                                        <th>Views</th>
                                        <th>Downloads</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($presentations as $presentation)
                                        <tr>
                                            <td>
                                                <strong>{{ $presentation->title }}</strong>
                                                @if($presentation->description)
                                                    <br><small class="text-muted">{{ Str::limit($presentation->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $presentation->user->name ?? 'Unknown' }}</td>
                                            <td>{{ $presentation->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ number_format($presentation->view_count ?? 0) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($presentation->download_count ?? 0) }}</span>
                                            </td>
                                            <td>
                                                @if($presentation->rating)
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $presentation->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                        <span class="ms-1 small">({{ number_format($presentation->rating, 1) }})</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No rating</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($presentation->is_public)
                                                    <span class="badge bg-success">Public</span>
                                                @else
                                                    <span class="badge bg-secondary">Private</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('presentations.show', $presentation) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('presentations.analytics', $presentation) }}" 
                                                       class="btn btn-outline-info" title="Analytics">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                    <a href="{{ route('presentations.download', $presentation) }}" 
                                                       class="btn btn-outline-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($presentations->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $presentations->links('vendor.pagination.custom-3d') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No presentation data available</h5>
                            <p class="text-muted">Create some presentations to see auto-generated reports here.</p>
                            <a href="{{ route('presentations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Presentation
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Export Options -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Export Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Export Formats</h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="exportReport('pdf')">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="exportReport('csv')">
                                    <i class="fas fa-file-csv"></i> CSV
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Report Options</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeAnalytics" checked>
                                <label class="form-check-label" for="includeAnalytics">
                                    Include Analytics Data
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeComments">
                                <label class="form-check-label" for="includeComments">
                                    Include Comments
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Export report functionality
function exportReport(format) {
    const includeAnalytics = document.getElementById('includeAnalytics').checked;
    const includeComments = document.getElementById('includeComments').checked;
    
    const params = new URLSearchParams({
        format: format,
        include_analytics: includeAnalytics,
        include_comments: includeComments
    });
    
    // Placeholder for export functionality
    alert(`Exporting report as ${format.toUpperCase()}...\nThis feature will be implemented soon.`);
    
    // TODO: Implement actual export functionality
    // window.location.href = `/presentations/auto-reports/export?${params.toString()}`;
}

// Refresh data every 30 seconds
setInterval(function() {
    // Placeholder for auto-refresh functionality
    console.log('Auto-refreshing presentation data...');
}, 30000);
</script>
@endsection