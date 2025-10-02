@extends('layouts.app')

@section('title', 'Budget Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Budget Reports</h1>
            <p class="text-muted">Financial planning and budget performance analysis</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                ← Back to Reports
            </a>
            <div class="btn-group" role="group">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportBudgetReport('pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportBudgetReport('excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportBudgetReport('csv')">CSV Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Filters & Options</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.budgets') }}" id="filtersForm">
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
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="exceeded" {{ request('status') === 'exceeded' ? 'selected' : '' }}>Exceeded</option>
                            <option value="on_track" {{ request('status') === 'on_track' ? 'selected' : '' }}>On Track</option>
                            <option value="at_risk" {{ request('status') === 'at_risk' ? 'selected' : '' }}>At Risk</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">All Categories</option>
                            <option value="marketing" {{ request('category') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="operations" {{ request('category') === 'operations' ? 'selected' : '' }}>Operations</option>
                            <option value="travel" {{ request('category') === 'travel' ? 'selected' : '' }}>Travel</option>
                            <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="office" {{ request('category') === 'office' ? 'selected' : '' }}>Office</option>
                            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="period" class="form-label">Period</label>
                        <select class="form-control" id="period" name="period">
                            <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Quarterly</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            🔍 Apply Filters
                        </button>
                        <a href="{{ route('reports.budgets') }}" class="btn btn-outline-secondary">
                            × Clear Filters
                        </a>
                        <button type="button" class="btn btn-info" onclick="refreshData()">
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Budget</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($budgetData['stats']['total_budget'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">💰</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Spent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($budgetData['stats']['total_spent'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">💳</span>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Remaining</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($budgetData['stats']['remaining_budget'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">🐷</span>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Utilization</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $budgetData['stats']['utilization_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">📊</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-{{ $budgetData['stats']['utilization_rate'] > 90 ? 'danger' : ($budgetData['stats']['utilization_rate'] > 75 ? 'warning' : 'success') }}" 
                                 role="progressbar" 
                                 style="width: {{ min($budgetData['stats']['utilization_rate'], 100) }}%" 
                                 aria-valuenow="{{ $budgetData['stats']['utilization_rate'] }}" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Budget vs Actual Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Budget vs Actual Spending</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Chart Options
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeBudgetChartType('line')">Line Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeBudgetChartType('bar')">Bar Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeBudgetChartType('area')">Area Chart</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="budgetChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Category Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Performance & Variance Analysis -->
    <div class="row mb-4">
        <!-- Budget Performance -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Budget Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Variance Analysis -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Variance Analysis</h6>
                </div>
                <div class="card-body">
                    @if(count($budgetData['category_breakdown']) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-center">Budget</th>
                                        <th class="text-center">Actual</th>
                                        <th class="text-center">Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($budgetData['category_breakdown'] as $category)
                                        @php
                                            $variance = $category->actual_amount - $category->budget_amount;
                                            $variancePercent = $category->budget_amount > 0 ? ($variance / $category->budget_amount) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge badge-success">{{ ucfirst($category->category) }}</span>
                                            </td>
                                            <td class="text-center">${{ number_format($category->budget_amount, 0) }}</td>
                                            <td class="text-center">${{ number_format($category->actual_amount, 0) }}</td>
                                            <td class="text-center">
                                                <span class="text-{{ $variance >= 0 ? 'danger' : 'success' }}">
                                                    {{ $variance >= 0 ? '↗️' : '↘️' }}
                                                    {{ $variance >= 0 ? '+' : '' }}{{ number_format($variancePercent, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span style="font-size: 3rem; color: #6c757d;" class="mb-3 d-block">📊</span>
                            <p class="text-muted">No budget data available</p>
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
                    <h6 class="m-0 font-weight-bold text-success">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab">
                                Category Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                Status Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                                Timeline Data
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="analyticsTabContent">
                        <div class="tab-pane fade show active" id="category" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-center">Budget Amount</th>
                                            <th class="text-center">Actual Spent</th>
                                            <th class="text-center">Remaining</th>
                                            <th class="text-center">Utilization</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgetData['category_breakdown'] as $category)
                                            @php
                                                $remaining = $category->budget_amount - $category->actual_amount;
                                                $utilization = $category->budget_amount > 0 ? ($category->actual_amount / $category->budget_amount) * 100 : 0;
                                                $status = $utilization > 100 ? 'exceeded' : ($utilization > 90 ? 'at_risk' : ($utilization > 75 ? 'on_track' : 'under_budget'));
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="badge badge-success">{{ ucfirst($category->category) }}</span>
                                                </td>
                                                <td class="text-center">${{ number_format($category->budget_amount, 2) }}</td>
                                                <td class="text-center">${{ number_format($category->actual_amount, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="text-{{ $remaining >= 0 ? 'success' : 'danger' }}">
                                                        ${{ number_format($remaining, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $utilization > 100 ? 'danger' : ($utilization > 90 ? 'warning' : 'success') }}" 
                                                             style="width: {{ min($utilization, 100) }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($utilization, 1) }}%</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $status === 'exceeded' ? 'danger' : ($status === 'at_risk' ? 'warning' : 'success') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="status" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-center">Total Budget</th>
                                            <th class="text-center">Total Spent</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgetData['status_breakdown'] as $status)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $status->status === 'active' ? 'success' : ($status->status === 'exceeded' ? 'danger' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($status->count) }}</td>
                                                <td class="text-center">${{ number_format($status->total_budget, 2) }}</td>
                                                <td class="text-center">${{ number_format($status->total_spent, 2) }}</td>
                                                <td class="text-center">
                                                    {{ $budgetData['stats']['total_budget'] > 0 ? number_format(($status->total_budget / $budgetData['stats']['total_budget']) * 100, 1) : 0 }}%
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
                                            <th class="text-center">Budget</th>
                                            <th class="text-center">Actual</th>
                                            <th class="text-center">Variance</th>
                                            <th class="text-center">Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgetData['time_series'] as $period)
                                            @php
                                                $variance = $period->actual - $period->budget;
                                                $variancePercent = $period->budget > 0 ? ($variance / $period->budget) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td class="font-weight-bold">{{ $period->period }}</td>
                                                <td class="text-center">${{ number_format($period->budget, 2) }}</td>
                                                <td class="text-center">${{ number_format($period->actual, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="text-{{ $variance >= 0 ? 'danger' : 'success' }}">
                                                        {{ $variance >= 0 ? '+' : '' }}${{ number_format($variance, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="text-{{ $variancePercent >= 0 ? 'danger' : 'success' }}">
                                                        <i class="fas fa-arrow-{{ $variancePercent >= 0 ? 'up' : 'down' }}"></i>
                                                        {{ number_format(abs($variancePercent), 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
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
const timeSeriesData = @json($budgetData['time_series']);
const categoryData = @json($budgetData['category_breakdown']);
const statusData = @json($budgetData['status_breakdown']);

// Budget vs Actual Chart
const budgetCtx = document.getElementById('budgetChart').getContext('2d');
const budgetChart = new Chart(budgetCtx, {
    type: 'line',
    data: {
        labels: timeSeriesData.map(item => item.period),
        datasets: [
            {
                label: 'Budget',
                data: timeSeriesData.map(item => item.budget),
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.1
            },
            {
                label: 'Actual',
                data: timeSeriesData.map(item => item.actual),
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.category.charAt(0).toUpperCase() + item.category.slice(1)),
        datasets: [{
            data: categoryData.map(item => item.budget_amount),
            backgroundColor: [
                '#28a745',
                '#17a2b8',
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
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});

// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'bar',
    data: {
        labels: categoryData.map(item => item.category.charAt(0).toUpperCase() + item.category.slice(1)),
        datasets: [
            {
                label: 'Budget',
                data: categoryData.map(item => item.budget_amount),
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            },
            {
                label: 'Actual',
                data: categoryData.map(item => item.actual_amount),
                backgroundColor: 'rgba(220, 53, 69, 0.8)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Functions
function exportBudgetReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('type', 'budgets');
    params.set('format', format);
    const url = `{{ route('reports.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function changeBudgetChartType(type) {
    budgetChart.config.type = type;
    budgetChart.update();
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