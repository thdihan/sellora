<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'shop_name',
        'full_address',
        'phone',
        'email',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the orders for the customer.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_name', 'name');
    }

    /**
     * Scope a query to search customers by name, shop name, or phone.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('shop_name', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get the customer's outstanding due amount.
     *
     * @return float
     */
    public function getOutstandingDueAttribute(): float
    {
        // Calculate outstanding due from unpaid orders/bills
        return Order::where('customer_name', $this->name)
            ->where('status', '!=', 'paid')
            ->sum('total_amount') ?? 0.0;
    }

    /**
     * Get the last 5 orders for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLastFiveOrders()
    {
        return $this->orders()
            ->latest()
            ->take(5)
            ->get();
    }
}
