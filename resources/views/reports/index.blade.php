@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reports Dashboard</h1>
            <p class="text-muted">Comprehensive analytics and insights for your business</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.custom') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Custom Report
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportReport('overview', 'pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReport('overview', 'excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReport('overview', 'csv')">CSV Data</a></li>
                    @php
                        $nsmPlusRoles = ['NSM', 'NSM+', 'RSM', 'ASM', 'Author'];
                    @endphp
                    @if(in_array(auth()->user()->role, $nsmPlusRoles))
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="generatePresentationFromReport('overview')"><i class="fas fa-file-powerpoint me-2"></i>Generate Presentation</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($stats['total_sales']['amount'], 2) }}</div>
                            <div class="text-xs text-muted">{{ $stats['total_sales']['count'] }} orders</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        @php
                            $salesGrowth = $stats['total_sales']['last_month'] > 0
                                ? (($stats['total_sales']['this_month'] - $stats['total_sales']['last_month']) / $stats['total_sales']['last_month']) * 100
                                : 0;
                        @endphp
                        <span class="text-{{ $salesGrowth >= 0 ? 'success' : 'danger' }} small">
                            <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($salesGrowth), 1) }}% from last month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($stats['total_expenses']['amount'], 2) }}</div>
                            <div class="text-xs text-muted">{{ $stats['total_expenses']['count'] }} expenses</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        @php
                            $expenseGrowth = $stats['total_expenses']['last_month'] > 0
                                ? (($stats['total_expenses']['this_month'] - $stats['total_expenses']['last_month']) / $stats['total_expenses']['last_month']) * 100
                                : 0;
                        @endphp
                        <span class="text-{{ $expenseGrowth <= 0 ? 'success' : 'warning' }} small">
                            <i class="fas fa-arrow-{{ $expenseGrowth <= 0 ? 'down' : 'up' }}"></i>
                            {{ number_format(abs($expenseGrowth), 1) }}% from last month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_visits']['count'] }}</div>
                            <div class="text-xs text-muted">{{ $stats['total_visits']['completed'] }} completed</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-success small">
                            <i class="fas fa-check-circle"></i>
                            {{ $stats['total_visits']['success_rate'] }}% success rate
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Budgets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_budgets']['count'] }}</div>
                            <div class="text-xs text-muted">৳{{ number_format($stats['active_budgets']['total_amount'], 2) }} allocated</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" role="progressbar"
                                 style="width: {{ $stats['active_budgets']['utilization'] }}%"
                                 aria-valuenow="{{ $stats['active_budgets']['utilization'] }}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="text-muted small">{{ $stats['active_budgets']['utilization'] }}% utilized</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access to Report Types -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Categories</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('reports.sales') }}" class="text-decoration-none">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">Sales Reports</h5>
                                        <p class="card-text text-muted">Order analytics, revenue trends, and customer insights</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
                                <div class="card border-left-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-receipt fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">Expense Reports</h5>
                                        <p class="card-text text-muted">Cost analysis, category breakdown, and spending trends</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('reports.visits') }}" class="text-decoration-none">
                                <div class="card border-left-info h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-map-marker-alt fa-3x text-info mb-3"></i>
                                        <h5 class="card-title">Visit Reports</h5>
                                        <p class="card-text text-muted">Visit analytics, success rates, and location insights</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('reports.budgets') }}" class="text-decoration-none">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                                        <h5 class="card-title">Budget Reports</h5>
                                        <p class="card-text text-muted">Budget utilization, allocation analysis, and forecasting</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if(isset($recentActivity) && is_countable($recentActivity) && count($recentActivity) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($recentActivity as $activity)
                                        <tr>
                                            <td class="text-center" style="width: 50px;">
                                                @switch($activity['type'])
                                                    @case('order')
                                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-shopping-cart text-white"></i>
                                                        </div>
                                                        @break
                                                    @case('expense')
                                                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-receipt text-white"></i>
                                                        </div>
                                                        @break
                                                    @case('visit')
                                                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-map-marker-alt text-white"></i>
                                                        </div>
                                                        @break
                                                    @case('budget')
                                                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-chart-pie text-white"></i>
                                                        </div>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $activity['title'] }}</div>
                                                <div class="text-muted small">{{ $activity['description'] }}</div>
                                            </td>
                                            <td class="text-right">
                                                @if(isset($activity['amount']))
                                                    <div class="font-weight-bold">৳{{ number_format($activity['amount'], 2) }}</div>
                                                @endif
                                                <div class="text-muted small">{{ $activity['date']->diffForHumans() }}</div>
                                            </td>
                                            <td class="text-center" style="width: 100px;">
                                                @if(isset($activity['status']))
                                                    <span class="badge badge-{{ $activity['status'] === 'completed' || $activity['status'] === 'approved' ? 'success' : ($activity['status'] === 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($activity['status']) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No recent activity to display</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport(type, format) {
    const url = `{{ route('reports.export') }}?type=${type}&format=${format}`;
    window.open(url, '_blank');
}

function generatePresentationFromReport(reportType) {
    if (confirm('Generate a PowerPoint presentation from this report?')) {
        // Create a form to submit the request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/presentations/generate-from-report/${reportType}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}
</script>
@endsection