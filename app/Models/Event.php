<?php

/**
 * Event Model
 *
 * PHP version 8.0
 *
 * @category Model
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Event Model Class
 *
 * @category Model
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'is_all_day',
        'priority',
        'status',
        'color',
        'reminder_minutes',
        'attendees',
        'notes',
        'created_by',
        'recurring_type',
        'recurring_end_date',
        'recurring_days',
        'attachments'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_all_day' => 'boolean',
        'reminder_minutes' => 'integer',
        'attendees' => 'array',
        'recurring_days' => 'array',
        'attachments' => 'array',
        'recurring_end_date' => 'date'
    ];

    protected $appends = [
        'formatted_start_date',
        'formatted_end_date',
        'duration_hours',
        'is_past',
        'is_today',
        'is_upcoming'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    /**
     * Format start date for display
     *
     * @return string|null
     */
    public function getFormattedStartDateAttribute(): ?string
    {
        if ($this->is_all_day) {
            return $this->start_date ? $this->start_date->format('M j, Y') : null;
        }
        return $this->start_time ? $this->start_time->format('M j, Y \a\t g:i A') : null;
    }

    /**
     * Format end date for display
     *
     * @return string|null
     */
    public function getFormattedEndDateAttribute(): ?string
    {
        if ($this->is_all_day) {
            return $this->end_date ? $this->end_date->format('M j, Y') : null;
        }
        return $this->end_time ? $this->end_time->format('M j, Y \a\t g:i A') : null;
    }

    /**
     * Get duration in hours
     *
     * @return float|null
     */
    public function getDurationHoursAttribute(): ?float
    {
        if ($this->is_all_day) {
            return 24;
        }
        if (!$this->start_time || !$this->end_time) {
            return null;
        }
        return $this->start_time->diffInHours($this->end_time);
    }

    /**
     * Check if event is in the past
     *
     * @return bool
     */
    public function getIsPastAttribute(): bool
    {
        $compareDate = $this->is_all_day ? $this->end_date : $this->end_time;
        return $compareDate ? $compareDate->isPast() : false;
    }

    /**
     * Check if event is today
     *
     * @return bool
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->start_date ? $this->start_date->isToday() : false;
    }

    /**
     * Check if event is upcoming
     *
     * @return bool
     */
    public function getIsUpcomingAttribute(): bool
    {
        $compareDate = $this->is_all_day ? $this->start_date : $this->start_time;
        return $compareDate ? $compareDate->isFuture() : false;
    }

    /**
     * Scope for upcoming events
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where(
            function ($q) {
                $q->where('is_all_day', true)
                    ->where('start_date', '>=', now()->startOfDay())
                    ->orWhere(
                        function ($subQuery) {
                            $subQuery->where('is_all_day', false)
                                ->where('start_time', '>=', now());
                        }
                    );
            }
        );
    }

    /**
     * Scope for events happening today
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_date', today());
    }

    /**
     * Scope events by type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param string                                $type  Event type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope events by status
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  Query builder
     * @param string                                $status Event status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope events by priority
     *
     * @param \Illuminate\Database\Eloquent\Builder $query    Query builder
     * @param string                                $priority Event priority
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope events by date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query     Query builder
     * @param string                                $startDate Start date
     * @param string                                $endDate   End date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(
            function ($q) use ($startDate, $endDate) {
                $q->where('is_all_day', true)
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhere(
                        function ($subQuery) use ($startDate, $endDate) {
                            $subQuery->where('is_all_day', false)
                                ->whereBetween('start_time', [$startDate, $endDate]);
                        }
                    );
            }
        );
    }

    /**
     * Get event color based on type
     *
     * @return string
     */
    public function getEventColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        return match ($this->event_type) {
            'meeting' => '#007bff',
            'appointment' => '#28a745',
            'deadline' => '#dc3545',
            'reminder' => '#ffc107',
            'personal' => '#6f42c1',
            'holiday' => '#fd7e14',
            default => '#6c757d'
        };
    }

    /**
     * Check if event is recurring
     *
     * @return bool
     */
    public function isRecurring(): bool
    {
        return !empty($this->recurring_type) && $this->recurring_type !== 'none';
    }

    /**
     * Check if current user can edit this event
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user ? $user->can('update', $this) : false;
    }

    /**
     * Check if current user can delete this event
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user ? $user->can('delete', $this) : false;
    }
}
