<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'period_type',
        'start_date',
        'end_date',
        'total_amount',
        'allocated_amount',
        'spent_amount',
        'remaining_amount',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
        'categories',
        'currency',
        'auto_approve_limit',
        'notification_threshold',
        'is_recurring',
        'recurring_frequency'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'auto_approve_limit' => 'decimal:2',
        'notification_threshold' => 'decimal:2',
        'categories' => 'array',
        'is_recurring' => 'boolean'
    ];

    protected $appends = [
        'utilization_percentage',
        'days_remaining',
        'status_color',
        'period_label'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    // Accessors
    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        return round(($this->spent_amount / $this->total_amount) * 100, 2);
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'active' => 'primary',
            'completed' => 'info',
            'cancelled' => 'danger',
            'exceeded' => 'danger',
            default => 'secondary'
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        return match($this->period_type) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'half_yearly' => 'Half Yearly',
            'yearly' => 'Yearly',
            'custom' => 'Custom Period',
            default => 'Unknown'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPeriod($query, $periodType)
    {
        return $query->where('period_type', $periodType);
    }

    public function scopeCurrentPeriod($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeExceeded($query)
    {
        return $query->whereRaw('spent_amount > total_amount');
    }

    public function scopeNearLimit($query, $threshold = 80)
    {
        return $query->whereRaw('(spent_amount / total_amount) * 100 >= ?', [$threshold]);
    }

    // Methods
    public function updateSpentAmount(): void
    {
        $this->spent_amount = $this->expenses()->sum('amount');
        $this->remaining_amount = $this->total_amount - $this->spent_amount;
        
        // Update status based on spending
        if ($this->spent_amount >= $this->total_amount) {
            $this->status = 'exceeded';
        } elseif ($this->utilization_percentage >= ($this->notification_threshold ?? 80)) {
            // Keep current status but could trigger notifications
        }
        
        $this->save();
    }

    public function canAddExpense($amount): bool
    {
        return ($this->spent_amount + $amount) <= $this->total_amount;
    }

    public function approve($userId): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = Carbon::now();
        
        return $this->save();
    }

    public function activate(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }
        
        $this->status = 'active';
        return $this->save();
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['active', 'exceeded'])) {
            return false;
        }
        
        $this->status = 'completed';
        return $this->save();
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        
        $this->status = 'cancelled';
        return $this->save();
    }

    public static function createRecurringBudgets(): void
    {
        $recurringBudgets = self::where('is_recurring', true)
                               ->where('status', 'completed')
                               ->get();
        
        foreach ($recurringBudgets as $budget) {
            $newStartDate = match($budget->recurring_frequency) {
                'monthly' => $budget->end_date->addMonth(),
                'quarterly' => $budget->end_date->addMonths(3),
                'half_yearly' => $budget->end_date->addMonths(6),
                'yearly' => $budget->end_date->addYear(),
                default => null
            };
            
            if ($newStartDate) {
                $newEndDate = match($budget->recurring_frequency) {
                    'monthly' => $newStartDate->copy()->endOfMonth(),
                    'quarterly' => $newStartDate->copy()->addMonths(3)->subDay(),
                    'half_yearly' => $newStartDate->copy()->addMonths(6)->subDay(),
                    'yearly' => $newStartDate->copy()->addYear()->subDay(),
                    default => null
                };
                
                if ($newEndDate) {
                    self::create([
                        'name' => $budget->name . ' (' . $newStartDate->format('M Y') . ')',
                        'description' => $budget->description,
                        'period_type' => $budget->period_type,
                        'start_date' => $newStartDate,
                        'end_date' => $newEndDate,
                        'total_amount' => $budget->total_amount,
                        'allocated_amount' => 0,
                        'spent_amount' => 0,
                        'remaining_amount' => $budget->total_amount,
                        'status' => 'draft',
                        'created_by' => $budget->created_by,
                        'categories' => $budget->categories,
                        'currency' => $budget->currency,
                        'auto_approve_limit' => $budget->auto_approve_limit,
                        'notification_threshold' => $budget->notification_threshold,
                        'is_recurring' => true,
                        'recurring_frequency' => $budget->recurring_frequency
                    ]);
                }
            }
        }
    }
}
