<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TaxCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tax rates for this tax code.
     */
    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * Get active tax rates for this tax code.
     */
    public function activeTaxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class)->where('is_active', true);
    }

    /**
     * Scope a query to only include active tax codes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the current effective tax rate for this code.
     */
    public function getCurrentTaxRate(string $country = null, string $region = null): ?TaxRate
    {
        $query = $this->activeTaxRates()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            });

        if ($country) {
            $query->where(function ($q) use ($country) {
                $q->whereNull('country')
                  ->orWhere('country', $country);
            });
        }

        if ($region) {
            $query->where(function ($q) use ($region) {
                $q->whereNull('region')
                  ->orWhere('region', $region);
            });
        }

        return $query->orderBy('is_default', 'desc')
                    ->orderBy('effective_from', 'desc')
                    ->first();
    }
}
