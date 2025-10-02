<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxHead extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'kind',
        'percentage',
        'visible_to_client',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'percentage' => 'decimal:2',
        'visible_to_client' => 'boolean',
    ];

    /**
     * Get the user who created this tax head.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the order tax lines for this tax head.
     */
    public function orderTaxLines(): HasMany
    {
        return $this->hasMany(OrderTaxLine::class);
    }

    /**
     * Scope a query to only include visible tax heads.
     */
    public function scopeVisible($query)
    {
        return $query->where('visible_to_client', true);
    }

    /**
     * Scope a query to filter by kind.
     */
    public function scopeOfKind($query, $kind)
    {
        return $query->where('kind', $kind);
    }
}
