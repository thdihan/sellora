<?php

/**
 * OrderFile Model
 *
 * Handles file attachments for orders in the sales module.
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

/**
 * OrderFile Model
 *
 * Manages file attachments associated with orders.
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $original_name
 * @property string $file_path
 * @property string $file_type
 * @property int    $file_size
 */
class OrderFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the order that owns the file.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the file size in human readable format.
     *
     * @return string
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image.
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array(strtolower($this->file_type), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Check if file is a document.
     *
     * @return bool
     */
    public function isDocument(): bool
    {
        return in_array(strtolower($this->file_type), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
    }
}