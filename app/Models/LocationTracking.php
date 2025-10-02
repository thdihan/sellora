<?php

/**
 * LocationTracking Model
 *
 * Model for real-time location tracking of pharma sales force.
 * Stores GPS coordinates with timestamps for tracking purposes.
 *
 * @category Model
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * LocationTracking Model
 *
 * Handles real-time location tracking for pharma sales force
 *
 * @category Model
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class LocationTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'location_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'captured_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'accuracy' => 'decimal:2',
        'captured_at' => 'datetime',
    ];

    /**
     * Get the user that owns the location tracking record.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get latest location per user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestPerUser($query)
    {
        return $query->whereIn(
            'id',
            function ($subQuery) {
                $subQuery->selectRaw('MAX(id)')
                    ->from('location_tracking')
                    ->groupBy('user_id');
            }
        );
    }

    /**
     * Scope to get locations within a time range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query Query builder instance
     * @param  Carbon                                $from  Start time
     * @param  Carbon                                $to    End time
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinTimeRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('captured_at', [$from, $to]);
    }

    /**
     * Scope to get recent locations (within specified minutes).
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query   Query builder instance
     * @param  int                                   $minutes Minutes to look back
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('captured_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get formatted location string.
     *
     * @return string
     */
    public function getFormattedLocationAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Get time ago string.
     *
     * @return string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->captured_at->diffForHumans();
    }

    /**
     * Check if location is recent (within 10 minutes).
     *
     * @return bool
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->captured_at->diffInMinutes(now()) <= 10;
    }
}