<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'name',
        'description',
        'category',
        'allocated_amount',
        'spent_amount',
        'remaining_amount',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'utilization_percentage',
        'status_color',
    ];

    /**
     * Get the budget that owns this item
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get utilization percentage
     */
    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->allocated_amount <= 0) {
            return 0;
        }
        
        return round(($this->spent_amount / $this->allocated_amount) * 100, 2);
    }

    /**
     * Get status color based on utilization
     */
    public function getStatusColorAttribute(): string
    {
        $percentage = $this->utilization_percentage;
        
        if ($percentage >= 100) {
            return 'danger';
        } elseif ($percentage >= 80) {
            return 'warning';
        } elseif ($percentage >= 50) {
            return 'info';
        }
        
        return 'success';
    }

    /**
     * Update spent amount and recalculate remaining
     */
    public function updateSpentAmount(float $amount): void
    {
        $this->spent_amount += $amount;
        $this->remaining_amount = $this->allocated_amount - $this->spent_amount;
        $this->save();
    }

    /**
     * Check if item can accommodate additional spending
     */
    public function canSpend(float $amount): bool
    {
        return ($this->spent_amount + $amount) <= $this->allocated_amount;
    }
}
