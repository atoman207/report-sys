<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageUploadService
{
    /**
     * Upload multiple images and return their paths
     *
     * @param array $images
     * @param int $reportId
     * @return array
     */
    public function uploadImages(array $images, int $reportId): array
    {
        $uploadedPaths = [];
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile && $image->isValid()) {
                try {
                    $path = $this->storeImage($image, $reportId);
                    $uploadedPaths[] = $path;
                    
                    Log::info('Image uploaded successfully', [
                        'report_id' => $reportId,
                        'original_name' => $image->getClientOriginalName(),
                        'stored_path' => $path,
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to upload image', [
                        'report_id' => $reportId,
                        'original_name' => $image->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $uploadedPaths;
    }
    
    /**
     * Store a single image
     *
     * @param UploadedFile $image
     * @param int $reportId
     * @return string
     */
    private function storeImage(UploadedFile $image, int $reportId): string
    {
        // Generate unique filename
        $extension = $image->getClientOriginalExtension();
        $filename = $this->generateUniqueFilename($reportId, $extension);
        
        // Store in reports directory with organized structure
        $path = $image->storeAs(
            "reports/{$reportId}",
            $filename,
            'public'
        );
        
        return $path;
    }
    
    /**
     * Generate unique filename for uploaded image
     *
     * @param int $reportId
     * @param string $extension
     * @return string
     */
    private function generateUniqueFilename(int $reportId, string $extension): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);
        
        return "report_{$reportId}_{$timestamp}_{$randomString}.{$extension}";
    }
    
    /**
     * Upload signature image
     *
     * @param string $signatureData
     * @param int $reportId
     * @return string
     */
    public function uploadSignature(string $signatureData, int $reportId): string
    {
        try {
            // Clean the base64 data
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signatureData = base64_decode($signatureData);
            
            // Generate filename
            $filename = "signature_{$reportId}_" . now()->format('Y-m-d_H-i-s') . ".png";
            $path = "reports/{$reportId}/{$filename}";
            
            // Store the signature
            Storage::disk('public')->put($path, $signatureData);
            
            Log::info('Signature uploaded successfully', [
                'report_id' => $reportId,
                'stored_path' => $path
            ]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to upload signature', [
                'report_id' => $reportId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Delete images for a report
     *
     * @param array $imagePaths
     * @return bool
     */
    public function deleteImages(array $imagePaths): bool
    {
        try {
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    Log::info('Image deleted successfully', ['path' => $path]);
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete images', [
                'paths' => $imagePaths,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Delete avatar/signature image
     *
     * @param string $path
     * @return bool
     */
    public function deleteAvatar(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Avatar/Signature deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete avatar/signature', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get image URL for display
     *
     * @param string $path
     * @return string
     */
    public function getImageUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Validate image file
     *
     * @param UploadedFile $image
     * @return bool
     */
    public function validateImage(UploadedFile $image): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        return in_array($image->getMimeType(), $allowedMimes) && 
               $image->getSize() <= $maxSize;
    }
    
    /**
     * Get image information
     *
     * @param string $path
     * @return array|null
     */
    public function getImageInfo(string $path): ?array
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }
        
        $fullPath = Storage::disk('public')->path($path);
        $fileInfo = getimagesize($fullPath);
        
        return [
            'path' => $path,
            'url' => $this->getImageUrl($path),
            'size' => Storage::disk('public')->size($path),
            'width' => $fileInfo[0] ?? null,
            'height' => $fileInfo[1] ?? null,
            'mime_type' => $fileInfo['mime'] ?? null,
            'filename' => basename($path)
        ];
    }
} 