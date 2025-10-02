<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportJob extends Model
{
    protected $fillable = [
        'scope',
        'modules',
        'format',
        'filters',
        'include_dependencies',
        'status',
        'created_by',
        'stats',
        'file_path',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'modules' => 'array',
        'filters' => 'array',
        'stats' => 'array',
        'include_dependencies' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
