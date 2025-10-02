<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_attempt_id',
        'question_index',
        'question_text',
        'user_answer',
        'correct_answer',
        'is_correct',
        'points_earned',
        'max_points',
        'time_spent',
        'feedback'
    ];

    protected $casts = [
        'user_answer' => 'array',
        'correct_answer' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'max_points' => 'decimal:2',
        'time_spent' => 'integer'
    ];

    // Relationships
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(AssessmentAttempt::class, 'assessment_attempt_id');
    }

    // Accessors
    public function getPercentageAttribute(): float
    {
        if ($this->max_points == 0) {
            return 0;
        }
        return ($this->points_earned / $this->max_points) * 100;
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    // Methods
    public function getFormattedTimeSpent(): string
    {
        if (!$this->time_spent) {
            return '0s';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;

        if ($minutes > 0) {
            return $minutes . 'm ' . $seconds . 's';
        }

        return $seconds . 's';
    }

    public function getUserAnswerText(): string
    {
        if (is_array($this->user_answer)) {
            return implode(', ', $this->user_answer);
        }
        return $this->user_answer ?? 'No answer';
    }

    public function getCorrectAnswerText(): string
    {
        if (is_array($this->correct_answer)) {
            return implode(', ', $this->correct_answer);
        }
        return $this->correct_answer ?? 'N/A';
    }
}
