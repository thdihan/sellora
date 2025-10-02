<?php

/**
 * Bill File Service
 *
 * This file contains the BillFileService class which handles file upload,
 * storage, and management operations for bills in the application.
 *
 * @category Service
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\BillFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Bill File Service Class
 *
 * Handles file upload, storage, and management for bills including
 * validation, storage operations, and file metadata management.
 *
 * @category Service
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class BillFileService
{
    /**
     * Upload and store bill files
     *
     * Processes multiple file uploads for a specific bill, validating
     * and storing each file while creating database records.
     *
     * @param array $files  Array of UploadedFile instances
     * @param int   $billId The ID of the bill to associate files with
     *
     * @return array Array of created BillFile instances
     *
     * @throws \InvalidArgumentException When file validation fails
     */
    public function uploadFiles(array $files, int $billId): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadSingleFile($file, $billId);
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Upload and store a single bill file
     *
     * Validates, stores, and creates a database record for a single file
     * associated with a bill.
     *
     * @param UploadedFile $file   The file to upload
     * @param int          $billId The ID of the bill to associate the file with
     *
     * @return BillFile The created BillFile instance
     *
     * @throws \InvalidArgumentException When file validation fails
     */
    public function uploadSingleFile(UploadedFile $file, int $billId): BillFile
    {
        // Validate file type
        $this->validateFileType($file);
        
        // Validate file size (10MB limit)
        if (!$this->validateFileSize($file, 10)) {
            throw new \InvalidArgumentException('File size exceeds 10MB limit.');
        }
        
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        
        // Store file in bill-specific directory
        $path = $file->storeAs('bills/' . $billId, $filename, 'public');
        
        // Create database record
        return BillFile::create(
            [
                'bill_id' => $billId,
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'original_name' => $originalName,
                'file_size' => $file->getSize(),
            ]
        );
    }
    
    /**
     * Delete a bill file
     *
     * Removes a file from storage and deletes its database record.
     *
     * @param BillFile $billFile The bill file to delete
     *
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteFile(BillFile $billFile): bool
    {
        // Delete from storage
        $deleted = Storage::disk('public')->delete($billFile->file_path);
        
        // Delete database record
        if ($deleted) {
            $billFile->delete();
        }
        
        return $deleted;
    }
    
    /**
     * Delete multiple bill files
     *
     * Removes multiple files from storage and deletes their database records.
     *
     * @param array $billFiles Array of BillFile instances to delete
     *
     * @return bool True if all deletions were successful, false otherwise
     */
    public function deleteMultipleFiles(array $billFiles): bool
    {
        $allDeleted = true;
        
        foreach ($billFiles as $billFile) {
            if (!$this->deleteFile($billFile)) {
                $allDeleted = false;
            }
        }
        
        return $allDeleted;
    }
    
    /**
     * Get file URL for display
     *
     * Generates a public URL for accessing a stored file.
     *
     * @param string $filePath The path to the file in storage
     *
     * @return string The public URL for the file
     */
    public function getFileUrl(string $filePath): string
    {
        return Storage::url($filePath);
    }
    
    /**
     * Validate file type
     *
     * Checks if the uploaded file type is allowed based on MIME type.
     *
     * @param UploadedFile $file The file to validate
     *
     * @return bool True if file type is valid
     *
     * @throws \InvalidArgumentException When file type is not allowed
     */
    public function validateFileType(UploadedFile $file): bool
    {
        $allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid file type. Only PDF, images, Word, Excel, and text files are allowed.');
        }
        
        return true;
    }
    
    /**
     * Validate file size
     *
     * Checks if the uploaded file size is within the allowed limit.
     *
     * @param UploadedFile $file      The file to validate
     * @param int          $maxSizeMB Maximum allowed size in megabytes
     *
     * @return bool True if file size is valid, false otherwise
     */
    public function validateFileSize(UploadedFile $file, int $maxSizeMB = 10): bool
    {
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        return $file->getSize() <= $maxSizeBytes;
    }
    
    /**
     * Get file information
     *
     * Retrieves metadata information about a stored file.
     *
     * @param string $filePath The path to the file in storage
     *
     * @return array|null File information array or null if file doesn't exist
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
     * Check if file exists in storage
     *
     * Verifies whether a file exists in the storage system.
     *
     * @param string $filePath The path to check
     *
     * @return bool True if file exists, false otherwise
     */
    public function fileExists(string $filePath): bool
    {
        return Storage::disk('public')->exists($filePath);
    }
    
    /**
     * Get formatted file size
     *
     * Converts file size from bytes to human-readable format.
     *
     * @param int $bytes The file size in bytes
     *
     * @return string Formatted file size with appropriate unit
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Move file to archive directory
     *
     * Moves a file from active storage to the archive directory.
     *
     * @param string $filePath The path of the file to archive
     *
     * @return string|null The new archive path or null if operation failed
     */
    public function archiveFile(string $filePath): ?string
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }
        
        $archivePath = str_replace('bills/', 'bills/archived/', $filePath);
        
        if (Storage::disk('public')->move($filePath, $archivePath)) {
            return $archivePath;
        }
        
        return null;
    }
}