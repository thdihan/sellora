@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk Assign Sales Targets</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales-targets.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('sales-targets.bulk-store') }}" method="POST" id="bulkTargetForm">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Target Year -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="target_year" class="form-label">Target Year <span class="text-danger">*</span></label>
                                <select class="form-control @error('target_year') is-invalid @enderror" id="target_year" name="target_year" required>
                                    @for ($year = 2020; $year <= 2030; $year++)
                                        <option value="{{ $year }}" {{ old('target_year', date('Y')) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('target_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Monthly Targets Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Monthly Targets</h5>
                                <div class="row">
                                    @php
                                        $months = [
                                            'january' => 'January',
                                            'february' => 'February', 
                                            'march' => 'March',
                                            'april' => 'April',
                                            'may' => 'May',
                                            'june' => 'June',
                                            'july' => 'July',
                                            'august' => 'August',
                                            'september' => 'September',
                                            'october' => 'October',
                                            'november' => 'November',
                                            'december' => 'December'
                                        ];
                                    @endphp
                                    @foreach ($months as $key => $month)
                                        <div class="col-md-3 mb-2">
                                            <label for="{{ $key }}_target" class="form-label">{{ $month }}</label>
                                            <input type="number" 
                                                   class="form-control monthly-target @error($key.'_target') is-invalid @enderror" 
                                                   id="{{ $key }}_target" 
                                                   name="{{ $key }}_target" 
                                                   value="{{ old($key.'_target', 0) }}" 
                                                   min="0" 
                                                   step="0.01"
                                                   data-month="{{ $loop->iteration }}">
                                            @error($key.'_target')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Total Yearly Target Display -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Total Yearly Target</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="total_yearly_display" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Targets Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Weekly Targets (Current Month)</h5>
                                <div class="row" id="weekly-targets-container">
                                    @for ($week = 1; $week <= 6; $week++)
                                        <div class="col-md-2 mb-2 week-input" id="week-{{ $week }}-container">
                                            <label for="week_{{ $week }}_target" class="form-label">Week {{ $week }}</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="week_{{ $week }}_target" 
                                                   name="week_{{ $week }}_target" 
                                                   value="{{ old('week_'.$week.'_target', 0) }}" 
                                                   min="0" 
                                                   step="0.01" 
                                                   readonly>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Optional notes about this bulk assignment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Eligible Employees Preview -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Eligible Employees</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    This will create targets for all employees you can assign targets to based on your role and hierarchy.
                                    Existing targets for the selected year will be skipped.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Bulk Targets
                        </button>
                        <a href="{{ route('sales-targets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-calculate total yearly target
        function calculateTotalYearly() {
            const monthlyInputs = document.querySelectorAll('.monthly-target');
            let total = 0;
            
            monthlyInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            document.getElementById('total_yearly_display').value = total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Calculate weekly targets for any month
        function calculateWeeklyTargetsForAnyMonth(month) {
            const monthlyTarget = parseFloat(document.getElementById(getMonthName(month).toLowerCase() + '_target').value) || 0;
            
            console.log(`Distributing monthly target of $${monthlyTarget} for month ${month}`);
            
            if (monthlyTarget > 0) {
                const weeksInMonth = getWeeksInMonth(new Date().getFullYear(), month);
                const weeklyAmount = monthlyTarget / weeksInMonth;
                
                // Reset all weekly targets first
                for (let week = 1; week <= 6; week++) {
                    const weekInput = document.getElementById(`week_${week}_target`);
                    const weekContainer = document.getElementById(`week-${week}-container`);
                    
                    if (week <= weeksInMonth) {
                        weekInput.value = weeklyAmount.toFixed(2);
                        weekContainer.style.display = 'block';
                    } else {
                        weekInput.value = '0.00';
                        weekContainer.style.display = 'none';
                    }
                }
            }
        }

        function getWeeksInMonth(year, month) {
            const firstDay = new Date(year, month - 1, 1);
            const lastDay = new Date(year, month, 0);
            const daysInMonth = lastDay.getDate();
            const firstDayOfWeek = firstDay.getDay();
            
            return Math.ceil((daysInMonth + firstDayOfWeek) / 7);
        }

        function getMonthName(monthNumber) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[monthNumber - 1];
        }

        // Add event listeners to monthly target inputs
        document.querySelectorAll('.monthly-target').forEach(input => {
            input.addEventListener('input', function() {
                calculateTotalYearly();
                
                // Update weekly targets for current month
                const currentMonth = new Date().getMonth() + 1;
                const inputMonth = parseInt(this.dataset.month);
                
                if (inputMonth === currentMonth) {
                    calculateWeeklyTargetsForAnyMonth(currentMonth);
                }
            });
        });

        // Initial calculations
        calculateTotalYearly();
        const currentMonth = new Date().getMonth() + 1;
        calculateWeeklyTargetsForAnyMonth(currentMonth);
    });
</script>
@endsection