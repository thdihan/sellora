@extends('layouts.app')

@section('title', 'Budget Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $budget->name }}</h1>
            <p class="text-muted">{{ $budget->description }}</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                ← Back to Budgets
            </a>
            @if($budget->status === 'draft')
                <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-primary">
                    ✏️ Edit
                </a>
            @endif
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    ⚙️ Actions
                </button>
                <ul class="dropdown-menu">
                    @if($budget->status === 'pending')
                        <li><a class="dropdown-item" href="#" onclick="approveBudget({{ $budget->id }})">
                            ✅ Approve
                        </a></li>
                    @endif
                    @if($budget->status === 'approved')
                        <li><a class="dropdown-item" href="#" onclick="activateBudget({{ $budget->id }})">
                            ▶️ Activate
                        </a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('budgets.analytics', $budget) }}">
                        📈 Analytics
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Budget Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Budget</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($budget->total_amount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="text-gray-300" style="font-size: 2rem;">💰</span>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Spent Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($budget->spent_amount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="text-gray-300" style="font-size: 2rem;">🛒</span>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Remaining</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($budget->remaining_amount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="text-gray-300" style="font-size: 2rem;">🐷</span>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Utilization</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($budget->utilization_percentage, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <span class="text-gray-300" style="font-size: 2rem;">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Details and Charts -->
    <div class="row mb-4">
        <!-- Budget Information -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Budget Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge badge-{{ $budget->status_color }}">{{ ucfirst($budget->status) }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Period:</strong></div>
                        <div class="col-sm-8">{{ ucfirst($budget->period_type) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Start Date:</strong></div>
                        <div class="col-sm-8">{{ $budget->start_date->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>End Date:</strong></div>
                        <div class="col-sm-8">{{ $budget->end_date->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created By:</strong></div>
                        <div class="col-sm-8">{{ $budget->creator->name ?? 'N/A' }}</div>
                    </div>
                    @if($budget->approver)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Approved By:</strong></div>
                        <div class="col-sm-8">{{ $budget->approver->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Budget Utilization Chart -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Budget Utilization</h6>
                </div>
                <div class="card-body">
                    <canvas id="utilizationChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Breakdown -->
    <div class="row mb-4">
        <!-- Monthly Spending Trend -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Spending Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Category Breakdown</h6>
                </div>
                <div class="card-body">
                    @if($categoryBreakdown->count() > 0)
                        <canvas id="categoryChart" width="400" height="300"></canvas>
                    @else
                        <div class="text-center py-4">
                            <span class="text-gray-300 mb-3" style="font-size: 3rem;">📊</span>
                            <p class="text-muted">No expense data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Expense Breakdown -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Breakdown</h6>
                </div>
                <div class="card-body">
                    @if($budget->expenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="expenseTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($budget->expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($expense->category) }}</span>
                                            </td>
                                            <td>৳{{ number_format($expense->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $expense->status_color }}">{{ ucfirst($expense->status) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    👁️
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span class="text-gray-300 mb-3" style="font-size: 3rem;">🧾</span>
                            <p class="text-muted">No expenses recorded for this budget</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                ➕ Add Expense
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Budget Utilization Chart
const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
const utilizationChart = new Chart(utilizationCtx, {
    type: 'doughnut',
    data: {
        labels: ['Spent', 'Remaining'],
        datasets: [{
            data: [{{ $utilizationData['spent'] }}, {{ $utilizationData['remaining'] }}],
            backgroundColor: ['#e74a3b', '#1cc88a'],
            borderWidth: 2
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

// Monthly Spending Chart
@if($monthlySpending->count() > 0)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlySpending->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Spending',
            data: {!! json_encode($monthlySpending->pluck('total')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
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
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
@endif

// Category Breakdown Chart
@if($categoryBreakdown->count() > 0)
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($categoryBreakdown->pluck('category')) !!},
        datasets: [{
            data: {!! json_encode($categoryBreakdown->pluck('total')) !!},
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#858796', '#5a5c69', '#6f42c1', '#e83e8c', '#fd7e14'
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
@endif

// Action Functions
function approveBudget(budgetId) {
    if (confirm('Are you sure you want to approve this budget?')) {
        fetch(`/budgets/${budgetId}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to approve budget');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the budget');
        });
    }
}

function activateBudget(budgetId) {
    if (confirm('Are you sure you want to activate this budget?')) {
        fetch(`/budgets/${budgetId}/activate`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to activate budget');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while activating the budget');
        });
    }
}
</script>
@endpush
@endsection