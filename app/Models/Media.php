<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'alt_text',
        'description',
        'caption',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
        'size' => 'integer',
        'order_column' => 'integer',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeInCollection($query, $collectionName)
    {
        return $query->where('collection_name', $collectionName);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column')->orderBy('id');
    }

    public function getUrl($conversion = null): string
    {
        $disk = $conversion ? $this->conversions_disk : $this->disk;
        $path = $this->getPath($conversion);
        
        return Storage::disk($disk)->url($path);
    }

    public function getPath($conversion = null): string
    {
        $basePath = $this->mediable_type . '/' . $this->mediable_id . '/';
        
        if ($conversion && isset($this->generated_conversions[$conversion])) {
            return $basePath . 'conversions/' . $this->file_name . '-' . $conversion . '.' . pathinfo($this->file_name, PATHINFO_EXTENSION);
        }
        
        return $basePath . $this->file_name;
    }

    public function getFullPath($conversion = null): string
    {
        $disk = $conversion ? $this->conversions_disk : $this->disk;
        return Storage::disk($disk)->path($this->getPath($conversion));
    }

    public function exists($conversion = null): bool
    {
        $disk = $conversion ? $this->conversions_disk : $this->disk;
        return Storage::disk($disk)->exists($this->getPath($conversion));
    }

    public function getHumanReadableSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getExtension(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function delete()
    {
        // Delete the original file
        if ($this->exists()) {
            Storage::disk($this->disk)->delete($this->getPath());
        }
        
        // Delete conversions
        if ($this->generated_conversions) {
            foreach (array_keys($this->generated_conversions) as $conversion) {
                if ($this->exists($conversion)) {
                    Storage::disk($this->conversions_disk)->delete($this->getPath($conversion));
                }
            }
        }
        
        return parent::delete();
    }
}
