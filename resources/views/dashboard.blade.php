@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@if(session('error'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <span class="me-2">⚠️</span>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<!-- Welcome Section -->
@php
    $roleColors = [
        'Author' => 'linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%)', // Dark executive
        'Admin' => 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)', // Red for admin
        'Chairman' => 'linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%)', // Purple for top executive
        'Director' => 'linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%)', // Deep blue for director
        'ED' => 'linear-gradient(135deg, #059669 0%, #047857 100%)', // Green for executive director
        'GM' => 'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)', // Orange for general manager
        'DGM' => 'linear-gradient(135deg, #0891b2 0%, #0e7490 100%)', // Cyan for deputy GM
        'AGM' => 'linear-gradient(135deg, #7c2d12 0%, #92400e 100%)', // Brown for assistant GM
        'NSM' => 'linear-gradient(135deg, #be185d 0%, #9d174d 100%)', // Pink for national sales
        'ZSM' => 'linear-gradient(135deg, #4338ca 0%, #3730a3 100%)', // Indigo for zonal
        'RSM' => 'linear-gradient(135deg, #0d9488 0%, #0f766e 100%)', // Teal for regional
        'ASM' => 'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)', // Violet for area
        'MPO' => 'linear-gradient(135deg, #059669 0%, #047857 100%)', // Emerald for MPO
        'MR' => 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)', // Blue for medical rep
        'Trainee' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)', // Gray for trainee
    ];
    $currentRoleColor = $roleColors[$role] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
@endphp
<div class="welcome-card" style="background: {{ $currentRoleColor }}; color: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
    <div class="cloud-container">
        <div class="cloud cloud1"></div>
        <div class="cloud cloud2"></div>
        <div class="cloud cloud3"></div>
        <div class="cloud cloud4"></div>
        <div class="cloud cloud5"></div>
    </div>
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2" style="color: white;">Welcome back, {{ $user->name }}!</h2>
            <p class="mb-0" style="color: rgba(255, 255, 255, 0.9);">Role: {{ ucfirst($role) }}</p>
            @if($user->designation)
                <p class="mb-0" style="color: rgba(255, 255, 255, 0.9);">Designation: {{ $user->designation }}</p>
            @endif
            @if($user->employee_id)
                <p class="mb-0" style="color: rgba(255, 255, 255, 0.9);">Employee ID: {{ $user->employee_id }}</p>
            @endif
            <p class="mb-0 mt-2"><small style="color: rgba(255, 255, 255, 0.8);">{{ now()->format('l, F j, Y') }}</small></p>
        </div>
        <div class="col-md-4 text-end">
            <span style="font-size: 4rem; opacity: 0.3; color: white;">👤</span>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-primary text-white me-3">
                    🛒
                </div>
                <div>
                    <h5 class="mb-0">{{ $stats['total_orders']['total'] ?? 0 }}</h5>
                    <small class="text-muted">Total Orders</small>
                    <div class="mt-1">
                        <small class="text-success">{{ $stats['total_orders']['this_month'] ?? 0 }} this month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-success text-white me-3">
                    🧾
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($stats['total_expenses']['total_amount'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Total Expenses</small>
                    <div class="mt-1">
                        <small class="text-info">{{ $stats['total_expenses']['total'] ?? 0 }} items</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-warning text-white me-3">
                    💰
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($stats['total_bills']['total_amount'] ?? 0, 2) }}</h5>
                    <small class="text-muted">Total Bills</small>
                    <div class="mt-1">
                        <small class="text-info">{{ $stats['total_bills']['total'] ?? 0 }} bills</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($role === 'Admin')
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-danger text-white me-3">
                    🕒
                </div>
                <div>
                    <h5 class="mb-0">{{ $stats['pending_approvals'] ?? 0 }}</h5>
                    <small class="text-muted">Pending Approvals</small>
                    <div class="mt-1">
                        <small class="text-warning">Requires attention</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-warning text-white me-3">
                    📅
                </div>
                <div>
                    <h5 class="mb-0">0</h5>
                    <small class="text-muted">Scheduled Visits</small>
                    <div class="mt-1">
                        <small class="text-muted">Coming soon</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-info text-white me-3">
                    📊
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($stats['monthly_budget']['used'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Budget Used</small>
                    <div class="mt-1">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar" style="width: {{ min($stats['monthly_budget']['percentage_used'] ?? 0, 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($stats['monthly_budget']['percentage_used'] ?? 0, 1) }}% of ৳{{ number_format($stats['monthly_budget']['allocated'] ?? 0, 0) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Target Section -->
@if(isset($targetData) && $targetData['has_target'])
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        🎯 My Sales Targets & Achievements
                    </h5>
                    <a href="{{ route('sales-targets.index') }}" class="btn btn-outline-primary btn-sm">
                        View All Targets
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Current Week Target -->
                    <div class="col-md-4 mb-3">
                        <div class="target-card">
                            <div class="target-header">
                                <h6 class="mb-1">Current Week Target</h6>
                                <small class="text-muted">Week {{ \App\Models\SalesTarget::getCurrentWeekOfMonth() }} of {{ date('F Y') }}</small>
                            </div>
                            
                            <div class="progress-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="fw-bold text-primary">{{ $targetData['weekly']['percentage'] }}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ min($targetData['weekly']['percentage'], 100) }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Achieved: ৳{{ number_format($targetData['weekly']['actual'], 0) }}</small>
                                    <small class="text-muted">Target: ৳{{ number_format($targetData['weekly']['target'], 0) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Month Target -->
                    <div class="col-md-4 mb-3">
                        <div class="target-card">
                            <div class="target-header">
                                <h6 class="mb-1">Current Month Target</h6>
                                <small class="text-muted">{{ date('F Y') }}</small>
                            </div>
                            
                            <div class="progress-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="fw-bold text-success">{{ $targetData['monthly']['percentage'] }}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($targetData['monthly']['percentage'], 100) }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Achieved: ৳{{ number_format($targetData['monthly']['actual'], 0) }}</small>
                                    <small class="text-muted">Target: ৳{{ number_format($targetData['monthly']['target'], 0) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Year Target -->
                    <div class="col-md-4 mb-3">
                        <div class="target-card">
                            <div class="target-header">
                                <h6 class="mb-1">Current Year Target</h6>
                                <small class="text-muted">{{ date('Y') }}</small>
                            </div>
                            
                            <div class="progress-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="fw-bold text-warning">{{ $targetData['yearly']['percentage'] }}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min($targetData['yearly']['percentage'], 100) }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Achieved: ৳{{ number_format($targetData['yearly']['actual'], 0) }}</small>
                                    <small class="text-muted">Target: ৳{{ number_format($targetData['yearly']['target'], 0) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@elseif(isset($targetData) && !$targetData['has_target'])
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🎯 Sales Targets</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <span style="font-size: 3rem; opacity: 0.3;">🎯</span>
                    <h6 class="mt-2 text-muted">No sales targets assigned</h6>
                    <p class="text-muted mb-0">{{ $targetData['message'] ?? 'Contact your manager to get sales targets assigned.' }}</p>
                    @if(isset($targetData['error']))
                        <p class="text-danger mt-2">{{ $targetData['error'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Charts Section -->
@if($role === 'Admin')
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Expenses & Bills Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="expensesChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Orders by Status</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Activities -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                @if(count($recentActivities) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentActivities as $activity)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon me-3">
                                    <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No recent activities</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        New Order
                    </a>
                    <a href="{{ route('expenses.create') }}" class="btn btn-success">
                        🧾 Add Expense
                    </a>
                    <a href="{{ route('bills.create') }}" class="btn btn-warning">
                        💰 Add Bill
                    </a>
                    @if($role === 'Admin')
                    <a href="{{ route('expenses.index') }}?status=pending" class="btn btn-warning">
                        🕒 Pending Approvals
                    </a>
                    @endif
                    <a href="{{ route('reports.index') }}" class="btn btn-info">
                        📊 View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .welcome-card {
        position: relative;
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 
            0 20px 40px rgba(0,0,0,0.15),
            0 10px 20px rgba(0,0,0,0.1),
            inset 0 1px 0 rgba(255,255,255,0.2);
        transform: perspective(1000px) rotateX(2deg);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            135deg,
            rgba(255,255,255,0.1) 0%,
            rgba(255,255,255,0.05) 50%,
            rgba(0,0,0,0.05) 100%
        );
        border-radius: inherit;
        pointer-events: none;
    }
    
    .welcome-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255,255,255,0.1) 50%,
            transparent 70%
        );
        transform: translateX(-100%);
        transition: transform 0.6s ease;
        pointer-events: none;
    }
    
    .welcome-card:hover {
        transform: perspective(1000px) rotateX(0deg) translateY(-5px);
        box-shadow: 
            0 30px 60px rgba(0,0,0,0.2),
            0 15px 30px rgba(0,0,0,0.15),
            inset 0 1px 0 rgba(255,255,255,0.3);
    }
    
    .welcome-card:hover::after {
        transform: translateX(100%);
    }
    
    @keyframes welcomeGlow {
        0%, 100% {
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.15),
                0 10px 20px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.2),
                0 0 20px rgba(255,255,255,0.1);
        }
        50% {
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.15),
                0 10px 20px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.2),
                0 0 30px rgba(255,255,255,0.2);
        }
    }
    
    .welcome-card {
        animation: welcomeGlow 4s ease-in-out infinite;
    }
    
    .welcome-card .row {
        position: relative;
        z-index: 2;
    }
    
    .welcome-card h2,
    .welcome-card p {
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .welcome-card .cloud-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
        opacity: 0.15;
        border-radius: inherit;
    }
    
    .welcome-card .cloud {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        opacity: 0.6;
    }
    
    .welcome-card .cloud:before,
    .welcome-card .cloud:after {
        content: '';
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50px;
    }
    
    .welcome-card .cloud1 {
        width: 80px;
        height: 30px;
        top: 20%;
        left: -80px;
        animation: cloudMove1 25s linear infinite;
    }
    
    .welcome-card .cloud1:before {
        width: 40px;
        height: 40px;
        top: -20px;
        left: 10px;
    }
    
    .welcome-card .cloud1:after {
        width: 60px;
        height: 35px;
        top: -15px;
        right: 10px;
    }
    
    .welcome-card .cloud2 {
        width: 60px;
        height: 25px;
        top: 60%;
        left: -60px;
        animation: cloudMove2 30s linear infinite;
        animation-delay: -5s;
    }
    
    .welcome-card .cloud2:before {
        width: 30px;
        height: 30px;
        top: -15px;
        left: 15px;
    }
    
    .welcome-card .cloud2:after {
        width: 45px;
        height: 28px;
        top: -12px;
        right: 8px;
    }
    
    .welcome-card .cloud3 {
        width: 100px;
        height: 35px;
        top: 10%;
        left: -100px;
        animation: cloudMove3 35s linear infinite;
        animation-delay: -10s;
    }
    
    .welcome-card .cloud3:before {
        width: 50px;
        height: 50px;
        top: -25px;
        left: 20px;
    }
    
    .welcome-card .cloud3:after {
        width: 70px;
        height: 40px;
        top: -20px;
        right: 15px;
    }
    
    .welcome-card .cloud4 {
        width: 70px;
        height: 28px;
        top: 80%;
        left: -70px;
        animation: cloudMove4 28s linear infinite;
        animation-delay: -15s;
    }
    
    .welcome-card .cloud4:before {
        width: 35px;
        height: 35px;
        top: -18px;
        left: 12px;
    }
    
    .welcome-card .cloud4:after {
        width: 50px;
        height: 30px;
        top: -15px;
        right: 12px;
    }
    
    .welcome-card .cloud5 {
        width: 90px;
        height: 32px;
        top: 40%;
        left: -90px;
        animation: cloudMove5 32s linear infinite;
        animation-delay: -20s;
    }
    
    .welcome-card .cloud5:before {
        width: 45px;
        height: 45px;
        top: -22px;
        left: 18px;
    }
    
    .welcome-card .cloud5:after {
        width: 65px;
        height: 38px;
        top: -18px;
        right: 12px;
    }
    
    @keyframes cloudMove1 {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(100vw + 80px));
        }
    }
    
    @keyframes cloudMove2 {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(100vw + 60px));
        }
    }
    
    @keyframes cloudMove3 {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(100vw + 100px));
        }
    }
    
    @keyframes cloudMove4 {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(100vw + 70px));
        }
    }
    
    @keyframes cloudMove5 {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(100vw + 90px));
        }
    }

    .stats-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-radius: 10px;
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 10px 10px 0 0 !important;
    }

    .progress {
        background-color: #e9ecef;
    }

    .progress-bar {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    /* Sales Target Styles */
    .target-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .target-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .target-header {
        border-bottom: 1px solid #f1f3f4;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }

    .progress-section {
        margin-bottom: 1rem;
    }

    .progress-section:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($role === 'Admin')
<script>
    // Expenses & Bills Chart
    const expensesCtx = document.getElementById('expensesChart').getContext('2d');
    const expensesChart = new Chart(expensesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['months']) !!},
            datasets: [{
                label: 'Monthly Expenses',
                data: {!! json_encode($chartData['expenses']) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: false
            }, {
                label: 'Monthly Bills',
                data: {!! json_encode($chartData['bills']) !!},
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
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

    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $stats['total_orders']['pending'] ?? 0 }},
                    {{ $stats['total_orders']['approved'] ?? 0 }},
                    {{ $stats['total_orders']['completed'] ?? 0 }},
                    {{ ($stats['total_orders']['total'] ?? 0) - ($stats['total_orders']['pending'] ?? 0) - ($stats['total_orders']['approved'] ?? 0) - ($stats['total_orders']['completed'] ?? 0) }}
                ],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
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
</script>
@endif
@endpush
