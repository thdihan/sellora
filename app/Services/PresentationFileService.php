<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * PresentationFileService
 *
 * Handles file upload, storage, and management for presentations
 */
class PresentationFileService
{
    /**
     * Upload and store a presentation file
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadFile(UploadedFile $file, int $userId): array
    {
        // Validate file type
        $allowedMimeTypes = [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/pdf',
            'application/vnd.oasis.opendocument.presentation'
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid file type. Only PowerPoint, PDF, and ODP files are allowed.');
        }

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        
        // Create user-specific directory
        $directory = 'presentations/user_' . $userId;
        
        // Store file
        $path = $file->storeAs($directory, $filename, 'public');
        
        return [
            'file_path' => $path,
            'original_filename' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }

    /**
     * Delete a presentation file
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool
    {
        return Storage::disk('public')->delete($filePath);
    }

    /**
     * Get file URL for display
     *
     * @param string $filePath
     * @return string
     */
    public function getFileUrl(string $filePath): string
    {
        return Storage::disk('public')->url($filePath);
    }

    /**
     * Generate thumbnail for presentation (placeholder implementation)
     *
     * @param string $filePath
     * @return string|null
     */
    public function generateThumbnail(string $filePath): ?string
    {
        // This is a placeholder - in a real implementation, you would:
        // 1. Use a library like ImageMagick or similar to generate thumbnails
        // 2. For PowerPoint files, convert first slide to image
        // 3. For PDF files, convert first page to image
        // 4. Store thumbnail in thumbnails directory
        
        return null; // Return thumbnail path when implemented
    }

    /**
     * Validate file size
     *
     * @param UploadedFile $file
     * @param int $maxSizeMB
     * @return bool
     */
    public function validateFileSize(UploadedFile $file, int $maxSizeMB = 50): bool
    {
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        return $file->getSize() <= $maxSizeBytes;
    }

    /**
     * Get file information
     *
     * @param string $filePath
     * @return array|null
     */
    public function getFileInfo(string $filePath): ?array
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }

        return [
            'size' => Storage::disk('public')->size($filePath),
            'last_modified' => Storage::disk('public')->lastModified($filePath),
            'exists' => true
        ];
    }

    /**
     * Move file to archive directory
     *
     * @param string $filePath
     * @return string|null
     */
    public function archiveFile(string $filePath): ?string
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }

        $archivePath = str_replace('presentations/', 'presentations/archived/', $filePath);
        
        if (Storage::disk('public')->move($filePath, $archivePath)) {
            return $archivePath;
        }

        return null;
    }
}