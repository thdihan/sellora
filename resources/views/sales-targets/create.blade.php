@extends('layouts.app')

@section('title', 'Create Sales Target')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Sales Target</h3>
                    <a href="{{ route('sales-targets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('sales-targets.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Target Assignment Section -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Target Assignment</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="assigned_to_user_id" class="form-label">Target Assigned To <span class="text-danger">*</span></label>
                                            <select name="assigned_to_user_id" id="assigned_to_user_id" class="form-select" required>
                                                <option value="">Select Employee</option>
                                                @foreach($assignableEmployeesGrouped as $roleLevel => $employees)
                                                    @if($employees->count() > 0)
                                                        <optgroup label="{{ $roleLevel }}">
                                                            @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}" {{ old('assigned_to_user_id') == $employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->name }} ({{ $employee->role->name }})
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                You can only assign targets to employees under your role hierarchy.
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="target_year" class="form-label">Target Year <span class="text-danger">*</span></label>
                                            <select name="target_year" id="target_year" class="form-select" required>
                                                @for($year = date('Y'); $year <= date('Y') + 2; $year++)
                                                    <option value="{{ $year }}" {{ old('target_year', date('Y')) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Weekly Targets Section -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Weekly Sales Targets</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label for="week_1_target" class="form-label">Week 1 Target</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number" name="week_1_target" id="week_1_target" 
                                                           class="form-control" step="0.01" min="0" 
                                                           value="{{ old('week_1_target') }}" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="week_2_target" class="form-label">Week 2 Target</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number" name="week_2_target" id="week_2_target" 
                                                           class="form-control" step="0.01" min="0" 
                                                           value="{{ old('week_2_target') }}" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="week_3_target" class="form-label">Week 3 Target</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number" name="week_3_target" id="week_3_target" 
                                                           class="form-control" step="0.01" min="0" 
                                                           value="{{ old('week_3_target') }}" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="week_4_target" class="form-label">Week 4 Target</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number" name="week_4_target" id="week_4_target" 
                                                           class="form-control" step="0.01" min="0" 
                                                           value="{{ old('week_4_target') }}" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Targets Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Monthly Sales Targets</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $months = [
                                            'january' => 'January', 'february' => 'February', 'march' => 'March',
                                            'april' => 'April', 'may' => 'May', 'june' => 'June',
                                            'july' => 'July', 'august' => 'August', 'september' => 'September',
                                            'october' => 'October', 'november' => 'November', 'december' => 'December'
                                        ];
                                    @endphp
                                    @foreach($months as $key => $month)
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <label for="{{ $key }}_target" class="form-label">{{ $month }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" name="{{ $key }}_target" id="{{ $key }}_target" 
                                                       class="form-control monthly-target" step="0.01" min="0" 
                                                       value="{{ old($key . '_target') }}" placeholder="0.00">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Total Yearly Target Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Total Yearly Sales Target</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="total_yearly_target" class="form-label">Total Yearly Target <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" name="total_yearly_target" id="total_yearly_target" 
                                                   class="form-control" step="0.01" min="0" required
                                                   value="{{ old('total_yearly_target') }}" placeholder="0.00">
                                        </div>
                                        <small class="form-text text-muted">
                                            This will be auto-calculated based on monthly targets if left empty.
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6>Auto-calculation:</h6>
                                            <p class="mb-0">Total = Sum of all monthly targets</p>
                                            <p class="mb-0"><strong>Current Total: ৳<span id="calculated-total">0.00</span></strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('sales-targets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Target
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-calculate total yearly target from monthly targets
    function calculateTotal() {
        let total = 0;
        $('.monthly-target').each(function() {
            let value = parseFloat($(this).val()) || 0;
            total += value;
        });
        $('#calculated-total').text(total.toFixed(2));
        
        // Auto-fill total yearly target if empty
        if ($('#total_yearly_target').val() === '') {
            $('#total_yearly_target').val(total.toFixed(2));
        }
    }
    
    function calculateWeeklyTargets() {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth(); // 0-based index
        const monthNames = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];
        
        const currentMonthName = monthNames[currentMonth];
        const monthlyTargetInput = $('#' + currentMonthName + '_target');
        
        if (monthlyTargetInput.length) {
            const monthlyTarget = parseFloat(monthlyTargetInput.val()) || 0;
            
            // Get number of weeks in current month
            const year = currentDate.getFullYear();
            const month = currentMonth + 1;
            const weeksInMonth = getWeeksInMonth(year, month);
            
            // Divide equally among weeks
            const weeklyTarget = monthlyTarget / weeksInMonth;
            
            // Update weekly target inputs
            for (let week = 1; week <= 4; week++) {
                const weekInput = $('#week_' + week + '_target');
                if (weekInput.length && week <= weeksInMonth) {
                    weekInput.val(weeklyTarget.toFixed(2));
                } else if (weekInput.length) {
                    weekInput.val('0.00');
                }
            }
        }
    }
    
    function calculateWeeklyTargetsForAnyMonth(changedInput) {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth(); // 0-based index
        const monthNames = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];
        
        // Get the month from the changed input
        const inputId = changedInput.attr('id');
        const monthMatch = inputId.match(/^(\w+)_target$/);
        
        if (monthMatch) {
            const monthName = monthMatch[1];
            const monthIndex = monthNames.indexOf(monthName);
            
            if (monthIndex !== -1) {
                const monthlyTarget = parseFloat(changedInput.val()) || 0;
                
                // Update weekly targets for any month (not just current month)
                const year = currentDate.getFullYear();
                const month = monthIndex + 1;
                const weeksInMonth = getWeeksInMonth(year, month);
                
                // Divide equally among weeks
                const weeklyTarget = monthlyTarget / weeksInMonth;
                
                // Update weekly target inputs
                for (let week = 1; week <= 4; week++) {
                    const weekInput = $('#week_' + week + '_target');
                    if (weekInput.length && week <= weeksInMonth) {
                        weekInput.val(weeklyTarget.toFixed(2));
                    } else if (weekInput.length) {
                        weekInput.val('0.00');
                    }
                }
                
                // Show a helpful message about which month's target was distributed
                if (monthlyTarget > 0) {
                    const monthDisplayName = monthName.charAt(0).toUpperCase() + monthName.slice(1);
                    console.log(`Weekly targets updated based on ${monthDisplayName} target: ৳${monthlyTarget.toFixed(2)}`);
                }
            }
        }
    }
    
    function getWeeksInMonth(year, month) {
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);
        const daysInMonth = lastDay.getDate();
        
        // Calculate number of complete weeks
        return Math.ceil(daysInMonth / 7);
    }
    
    // Calculate on page load
    calculateTotal();
    
    // Calculate when monthly targets change
    $('.monthly-target').on('input', function() {
        calculateTotal();
        calculateWeeklyTargetsForAnyMonth($(this));
    });
    
    // Update calculated total when total yearly target is manually changed
    $('#total_yearly_target').on('input', function() {
        let manualTotal = parseFloat($(this).val()) || 0;
        $('#calculated-total').text(manualTotal.toFixed(2));
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.text-danger {
    color: #dc3545 !important;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.gap-2 {
    gap: 0.5rem;
}
</style>
@endpush