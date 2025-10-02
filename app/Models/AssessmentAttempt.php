<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'answers',
        'score',
        'status',
        'started_at',
        'completed_at',
        'duration',
        'ip_address',
        'user_agent',
        'notes'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration' => 'integer'
    ];

    protected $appends = [
        'is_completed',
        'is_in_progress',
        'time_taken',
        'percentage_score'
    ];

    // Relationships
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getTimeTakenAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    public function getPercentageScoreAttribute(): float
    {
        if ($this->assessment && $this->assessment->max_score > 0) {
            return ($this->score / $this->assessment->max_score) * 100;
        }
        return 0;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
    }

    public function complete($answers, $score): void
    {
        $this->update([
            'answers' => $answers,
            'score' => $score,
            'status' => 'completed',
            'completed_at' => now(),
            'duration' => $this->started_at ? $this->started_at->diffInMinutes(now()) : null
        ]);
    }

    public function abandon(): void
    {
        $this->update([
            'status' => 'abandoned',
            'completed_at' => now()
        ]);
    }

    public function isTimeExpired(): bool
    {
        if (!$this->assessment->time_limit || !$this->started_at) {
            return false;
        }

        $timeLimit = $this->assessment->time_limit; // in minutes
        $elapsed = $this->started_at->diffInMinutes(now());
        
        return $elapsed >= $timeLimit;
    }

    public function getRemainingTime(): ?int
    {
        if (!$this->assessment->time_limit || !$this->started_at) {
            return null;
        }

        $timeLimit = $this->assessment->time_limit; // in minutes
        $elapsed = $this->started_at->diffInMinutes(now());
        
        return max(0, $timeLimit - $elapsed);
    }

    public function canContinue(): bool
    {
        return $this->status === 'in_progress' && !$this->isTimeExpired();
    }

    public function getResults(): array
    {
        if (!$this->is_completed || !$this->assessment) {
            return [];
        }

        return $this->assessment->calculateScore($this->answers);
    }

    public function hasPassed(): bool
    {
        if (!$this->is_completed || !$this->assessment) {
            return false;
        }

        return $this->score >= $this->assessment->passing_score;
    }
}
