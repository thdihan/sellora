<?php

/**
 * Visit model for managing customer visits and appointments
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Visit model
 */
class Visit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'visit_type',
        'purpose',
        'scheduled_at',
        'actual_start_time',
        'actual_end_time',
        'status',
        'priority',
        'notes',
        'outcome',
        'latitude',
        'longitude',
        'location_address',
        'attachments',
        'estimated_duration',
        'actual_duration',
        'requires_follow_up',
        'follow_up_date',
        'cancellation_reason',
        'rescheduled_from',
        'rescheduled_to',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'follow_up_date' => 'datetime',
        'rescheduled_from' => 'datetime',
        'rescheduled_to' => 'datetime',
        'attachments' => 'array',
        'requires_follow_up' => 'boolean',
        'estimated_duration' => 'decimal:2',
        'actual_duration' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user that owns the visit
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include upcoming visits
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include today's visits
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope a query to filter visits by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter visits by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('visit_type', $type);
    }

    /**
     * Get the status color attribute
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'planned' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'rescheduled' => 'purple',
            default => 'gray'
        };
    }

    /**
     * Get the priority color attribute
     */
    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get the formatted duration attribute
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->actual_duration) {
            $hours = floor($this->actual_duration);
            $minutes = ($this->actual_duration - $hours) * 60;
            return $hours . 'h ' . round($minutes) . 'm';
        }
        
        if ($this->estimated_duration) {
            $hours = floor($this->estimated_duration);
            $minutes = ($this->estimated_duration - $hours) * 60;
            return $hours . 'h ' . round($minutes) . 'm (est.)';
        }
        
        return 'N/A';
    }

    /**
     * Check if the visit is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'planned' && 
               now()->greaterThan($this->scheduled_at->addMinutes($this->estimated_duration ?? 60));
    }

    /**
     * Check if the visit can be completed
     */
    public function canBeCompleted()
    {
        return $this->status === 'planned';
    }

    /**
     * Check if the visit can be rescheduled
     */
    public function canBeRescheduled()
    {
        return $this->status === 'planned';
    }

    /**
     * Check if the visit can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status === 'planned';
    }

    /**
     * Check if the visit can be started
     */
    public function canBeStarted()
    {
        return $this->status === 'planned' && 
               $this->scheduled_at <= now();
    }

    /**
     * Check if the visit can be deleted
     */
    public function canBeDeleted()
    {
        // Visits can be deleted if they are not completed or in progress
        return in_array($this->status, ['scheduled', 'planned', 'cancelled']);
    }
}
