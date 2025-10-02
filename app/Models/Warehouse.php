<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'status',
        'is_main',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_main' => 'boolean',
    ];

    /**
     * Get the stock balances for the warehouse.
     */
    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    /**
     * Get the stock transactions for the warehouse.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Scope a query to only include active warehouses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include the main warehouse.
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Get the main warehouse.
     */
    public static function getMain()
    {
        return static::main()->first();
    }
}