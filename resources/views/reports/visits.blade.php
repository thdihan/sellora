@extends('layouts.app')

@section('title', 'Visit Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Visit Reports</h1>
            <p class="text-muted">Comprehensive visit analytics and performance insights</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                ← Back to Reports
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportVisitReport('pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportVisitReport('excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportVisitReport('csv')">CSV Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Filters & Options</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.visits') }}" id="filtersForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="visit_type" class="form-label">Visit Type</label>
                        <select class="form-control" id="visit_type" name="visit_type">
                            <option value="">All Types</option>
                            <option value="client_meeting" {{ request('visit_type') === 'client_meeting' ? 'selected' : '' }}>Client Meeting</option>
                            <option value="site_visit" {{ request('visit_type') === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                            <option value="delivery" {{ request('visit_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="inspection" {{ request('visit_type') === 'inspection' ? 'selected' : '' }}>Inspection</option>
                            <option value="follow_up" {{ request('visit_type') === 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                            <option value="other" {{ request('visit_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="period" class="form-label">Period</label>
                        <select class="form-control" id="period" name="period">
                            <option value="day" {{ request('period') === 'day' ? 'selected' : '' }}>Daily</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Quarterly</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-info">
                            🔍 Apply Filters
                        </button>
                        <a href="{{ route('reports.visits') }}" class="btn btn-outline-secondary">
                            × Clear Filters
                        </a>
                        <button type="button" class="btn btn-success" onclick="refreshData()">
                            🔄 Refresh
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($visitData['stats']['total_visits']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">📍</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($visitData['stats']['completed_visits']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">✅</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Scheduled Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($visitData['stats']['scheduled_visits']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">📅</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Success Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $visitData['stats']['success_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">📈</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $visitData['stats']['success_rate'] }}%" 
                                 aria-valuenow="{{ $visitData['stats']['success_rate'] }}" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Visit Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Visit Trend</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Chart Options
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('line')">Line Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('bar')">Bar Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('area')">Area Chart</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="visitTrendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Visit Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Type Distribution & Performance Metrics -->
    <div class="row mb-4">
        <!-- Visit Type Distribution -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Visit Type Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-success">{{ $visitData['stats']['success_rate'] }}%</div>
                                <div class="text-muted small">Success Rate</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-warning">{{ $visitData['stats']['cancelled_visits'] }}</div>
                                <div class="text-muted small">Cancelled</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @if(count($visitData['type_breakdown']) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($visitData['type_breakdown'] as $type)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $type->visit_type)) }}</span>
                                            </td>
                                            <td class="text-center">{{ $type->count }}</td>
                                            <td class="text-center">
                                                {{ $visitData['stats']['total_visits'] > 0 ? number_format(($type->count / $visitData['stats']['total_visits']) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span style="font-size: 3rem; color: #6c757d;" class="mb-3 d-block">📊</span>
                            <p class="text-muted">No visit type data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown Tables -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                Status Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="type-tab" data-bs-toggle="tab" data-bs-target="#type" type="button" role="tab">
                                Type Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                                Timeline Data
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="analyticsTabContent">
                        <div class="tab-pane fade show active" id="status" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-center">Percentage</th>
                                            <th class="text-center">Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitData['status_breakdown'] as $status)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $status->status === 'completed' ? 'success' : ($status->status === 'scheduled' ? 'warning' : ($status->status === 'in_progress' ? 'info' : 'secondary')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($status->count) }}</td>
                                                <td class="text-center">
                                                    {{ $visitData['stats']['total_visits'] > 0 ? number_format(($status->count / $visitData['stats']['total_visits']) * 100, 1) : 0 }}%
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $status->status === 'completed' ? 'success' : ($status->status === 'scheduled' ? 'warning' : 'info') }}" 
                                                             style="width: {{ $visitData['stats']['total_visits'] > 0 ? ($status->count / $visitData['stats']['total_visits']) * 100 : 0 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="type" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Visit Type</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-center">Percentage</th>
                                            <th class="text-center">Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitData['type_breakdown'] as $type)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $type->visit_type)) }}</span>
                                                </td>
                                                <td class="text-center">{{ number_format($type->count) }}</td>
                                                <td class="text-center">
                                                    {{ $visitData['stats']['total_visits'] > 0 ? number_format(($type->count / $visitData['stats']['total_visits']) * 100, 1) : 0 }}%
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-info" 
                                                             style="width: {{ $visitData['stats']['total_visits'] > 0 ? ($type->count / $visitData['stats']['total_visits']) * 100 : 0 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="timeline" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Period</th>
                                            <th class="text-center">Total Visits</th>
                                            <th class="text-center">Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $previousValue = 0; @endphp
                                        @foreach($visitData['time_series'] as $index => $period)
                                            <tr>
                                                <td class="font-weight-bold">{{ $period->period }}</td>
                                                <td class="text-center">{{ number_format($period->value) }}</td>
                                                <td class="text-center">
                                                    @if($index > 0 && $previousValue > 0)
                                                        @php
                                                            $change = (($period->value - $previousValue) / $previousValue) * 100;
                                                        @endphp
                                                        <span class="text-{{ $change >= 0 ? 'success' : 'danger' }}">
                                                            {{ $change >= 0 ? '↗️' : '↘️' }}
                                                            {{ number_format(abs($change), 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $previousValue = $period->value; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart data from backend
const timeSeriesData = @json($visitData['time_series']);
const statusData = @json($visitData['status_breakdown']);
const typeData = @json($visitData['type_breakdown']);

// Visit Trend Chart
const visitCtx = document.getElementById('visitTrendChart').getContext('2d');
const visitChart = new Chart(visitCtx, {
    type: 'line',
    data: {
        labels: timeSeriesData.map(item => item.period),
        datasets: [{
            label: 'Visits',
            data: timeSeriesData.map(item => item.value),
            borderColor: 'rgb(23, 162, 184)',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Visits: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1).replace('_', ' ')),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#17a2b8',
                '#6c757d',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Type Chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'bar',
    data: {
        labels: typeData.map(item => item.visit_type.charAt(0).toUpperCase() + item.visit_type.slice(1).replace('_', ' ')),
        datasets: [{
            label: 'Count',
            data: typeData.map(item => item.count),
            backgroundColor: [
                '#17a2b8',
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#fd7e14'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Count: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Functions
function exportVisitReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('type', 'visits');
    params.set('format', format);
    const url = `{{ route('reports.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function changeChartType(type) {
    visitChart.config.type = type;
    visitChart.update();
}

function refreshData() {
    location.reload();
}

// Auto-submit form on filter change
document.querySelectorAll('#filtersForm select, #filtersForm input').forEach(element => {
    element.addEventListener('change', function() {
        if (this.type !== 'date' || (document.getElementById('date_from').value && document.getElementById('date_to').value)) {
            document.getElementById('filtersForm').submit();
        }
    });
});
</script>
@endsection