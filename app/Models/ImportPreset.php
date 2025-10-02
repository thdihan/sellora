<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPreset extends Model
{
    protected $fillable = [
        'name',
        'source_type',
        'module',
        'column_map',
        'options',
        'created_by',
    ];

    protected $casts = [
        'column_map' => 'array',
        'options' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
