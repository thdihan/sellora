<?php

/**
 * Report Model
 *
 * Represents generated reports in the system with metadata,
 * file paths, and generation status tracking.
 *
 * @category Models
 * @package  Sellora\Models
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
 * Class Report
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $user_id
 * @property array|null $filters
 * @property string $format
 * @property string|null $file_path
 * @property int|null $file_size
 * @property Carbon $generated_at
 * @property Carbon|null $expires_at
 * @property string $status
 * @property string|null $error_message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'user_id',
        'filters',
        'format',
        'file_path',
        'file_size',
        'generated_at',
        'expires_at',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'filters' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Report types enumeration
     *
     * @var array<string>
     */
    public const TYPES = [
        'sales',
        'expenses',
        'visits',
        'budgets',
        'custom',
    ];

    /**
     * Report formats enumeration
     *
     * @var array<string>
     */
    public const FORMATS = [
        'pdf',
        'excel',
        'csv',
    ];

    /**
     * Report statuses enumeration
     *
     * @var array<string>
     */
    public const STATUSES = [
        'pending',
        'processing',
        'completed',
        'failed',
    ];

    /**
     * Get the user that owns the report.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include reports of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @param string                                $type  The report type to filter by
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include reports with a given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  The query builder instance
     * @param string                                $status The report status to filter by
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include completed reports.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include reports for a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  The query builder instance
     * @param int                                   $userId The user ID to filter by
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if the report is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the report has failed.
     *
     * @return bool
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the report is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the file size in human readable format.
     *
     * @return string|null
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the download URL for the report file.
     *
     * @return string|null
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->file_path || !$this->isCompleted()) {
            return null;
        }

        return route('reports.download', $this->id);
    }
}