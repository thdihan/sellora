<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'type',
        'questions',
        'scoring_method',
        'max_score',
        'passing_score',
        'time_limit',
        'attempts_allowed',
        'is_active',
        'start_date',
        'end_date',
        'instructions',
        'tags',
        'difficulty_level',
        'estimated_duration',
        'auto_grade',
        'show_results_immediately',
        'randomize_questions',
        'allow_review',
        'certificate_template',
        'completion_message'
    ];

    protected $casts = [
        'questions' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'auto_grade' => 'boolean',
        'show_results_immediately' => 'boolean',
        'randomize_questions' => 'boolean',
        'allow_review' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'time_limit' => 'integer',
        'attempts_allowed' => 'integer',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'estimated_duration' => 'integer'
    ];

    protected $appends = [
        'total_attempts',
        'average_score',
        'completion_rate',
        'is_available',
        'status_text'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    // Accessors
    public function getTotalAttemptsAttribute(): int
    {
        return $this->attempts()->count();
    }

    public function getAverageScoreAttribute(): float
    {
        return $this->attempts()->where('status', 'completed')
            ->avg('score') ?? 0;
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->attempts()->count();
        if ($total === 0) return 0;
        
        $completed = $this->attempts()->where('status', 'completed')->count();
        return ($completed / $total) * 100;
    }

    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date)) return false;
        
        return true;
    }

    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) return 'Inactive';
        if ($this->start_date && now()->lt($this->start_date)) return 'Scheduled';
        if ($this->end_date && now()->gt($this->end_date)) return 'Expired';
        return 'Active';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    // Methods
    public function canUserAttempt($userId): bool
    {
        if (!$this->is_available) return false;
        
        if ($this->attempts_allowed === null) return true;
        
        $userAttempts = $this->attempts()
            ->where('user_id', $userId)
            ->count();
            
        return $userAttempts < $this->attempts_allowed;
    }

    public function getUserBestScore($userId): ?float
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->max('score');
    }

    public function getUserAttemptCount($userId): int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->count();
    }

    public function getQuestionCount(): int
    {
        return count($this->questions ?? []);
    }

    public function calculateScore($answers): array
    {
        $totalQuestions = count($this->questions);
        $correctAnswers = 0;
        $results = [];
        
        foreach ($this->questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            $isCorrect = $this->checkAnswer($question, $userAnswer);
            
            if ($isCorrect) {
                $correctAnswers++;
            }
            
            $results[] = [
                'question_index' => $index,
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct_answer'] ?? null,
                'is_correct' => $isCorrect,
                'points' => $isCorrect ? ($question['points'] ?? 1) : 0
            ];
        }
        
        $score = match($this->scoring_method) {
            'percentage' => ($correctAnswers / $totalQuestions) * 100,
            'points' => array_sum(array_column($results, 'points')),
            'weighted' => $this->calculateWeightedScore($results),
            default => ($correctAnswers / $totalQuestions) * 100
        };
        
        return [
            'score' => round($score, 2),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => round(($correctAnswers / $totalQuestions) * 100, 2),
            'passed' => $score >= $this->passing_score,
            'results' => $results
        ];
    }

    private function checkAnswer($question, $userAnswer): bool
    {
        return match($question['type']) {
            'multiple_choice' => $userAnswer === $question['correct_answer'],
            'true_false' => (bool)$userAnswer === (bool)$question['correct_answer'],
            'multiple_select' => $this->checkMultipleSelect($question, $userAnswer),
            'text' => $this->checkTextAnswer($question, $userAnswer),
            default => false
        };
    }

    private function checkMultipleSelect($question, $userAnswer): bool
    {
        if (!is_array($userAnswer) || !is_array($question['correct_answer'])) {
            return false;
        }
        
        sort($userAnswer);
        sort($question['correct_answer']);
        
        return $userAnswer === $question['correct_answer'];
    }

    private function checkTextAnswer($question, $userAnswer): bool
    {
        $correctAnswer = strtolower(trim($question['correct_answer']));
        $userAnswer = strtolower(trim($userAnswer));
        
        return $correctAnswer === $userAnswer;
    }

    private function calculateWeightedScore($results): float
    {
        $totalPoints = array_sum(array_column($results, 'points'));
        $maxPoints = array_sum(array_column($this->questions, 'points'));
        
        return $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;
    }

    public function duplicate(): self
    {
        $assessment = $this->replicate();
        $assessment->title = $this->title . ' (Copy)';
        $assessment->is_active = false;
        $assessment->save();
        
        return $assessment;
    }

    public function getAnalytics(): array
    {
        $attempts = $this->attempts()->where('status', 'completed');
        
        return [
            'total_attempts' => $this->attempts()->count(),
            'completed_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('score') ?? 0,
            'highest_score' => $attempts->max('score') ?? 0,
            'lowest_score' => $attempts->min('score') ?? 0,
            'pass_rate' => $this->getPassRate(),
            'average_duration' => $attempts->avg('duration') ?? 0,
            'score_distribution' => $this->getScoreDistribution()
        ];
    }

    private function getPassRate(): float
    {
        $completed = $this->attempts()->where('status', 'completed')->count();
        if ($completed === 0) return 0;
        
        $passed = $this->attempts()
            ->where('status', 'completed')
            ->where('score', '>=', $this->passing_score)
            ->count();
            
        return ($passed / $completed) * 100;
    }

    private function getScoreDistribution(): array
    {
        $scores = $this->attempts()
            ->where('status', 'completed')
            ->pluck('score')
            ->toArray();
            
        $ranges = [
            '0-20' => 0, '21-40' => 0, '41-60' => 0,
            '61-80' => 0, '81-100' => 0
        ];
        
        foreach ($scores as $score) {
            if ($score <= 20) $ranges['0-20']++;
            elseif ($score <= 40) $ranges['21-40']++;
            elseif ($score <= 60) $ranges['41-60']++;
            elseif ($score <= 80) $ranges['61-80']++;
            else $ranges['81-100']++;
        }
        
        return $ranges;
    }
}
