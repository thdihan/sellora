<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_code_id',
        'label',
        'percent',
        'country',
        'region',
        'effective_from',
        'effective_to',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'percent' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tax code that owns this tax rate.
     */
    public function taxCode(): BelongsTo
    {
        return $this->belongsTo(TaxCode::class);
    }

    /**
     * Get the tax rules for this tax rate.
     */
    public function taxRules(): HasMany
    {
        return $this->hasMany(TaxRule::class);
    }

    /**
     * Scope a query to only include active tax rates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include effective tax rates.
     */
    public function scopeEffective(Builder $query): Builder
    {
        return $query->where('effective_from', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', now());
                    });
    }

    /**
     * Check if this tax rate is currently effective.
     */
    public function isEffective(): bool
    {
        $now = now();
        return $this->effective_from <= $now && 
               ($this->effective_to === null || $this->effective_to >= $now);
    }

    /**
     * Calculate tax amount for a given base amount.
     */
    public function calculateTax(float $baseAmount): float
    {
        return round($baseAmount * ($this->percent / 100), 2);
    }

    /**
     * Calculate total amount including tax.
     */
    public function calculateTotal(float $baseAmount): float
    {
        return $baseAmount + $this->calculateTax($baseAmount);
    }
}
