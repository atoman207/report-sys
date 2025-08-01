<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AvatarUploadService
{
    /**
     * Upload user avatar
     */
    public function uploadAvatar(UploadedFile $avatar, int $userId): string
    {
        // Validate file
        $this->validateAvatar($avatar);
        
        // Generate unique filename
        $filename = $this->generateAvatarFilename($avatar, $userId);
        
        // Store file
        $path = $avatar->storeAs('avatars', $filename, 'public');
        
        Log::info('Avatar uploaded successfully', [
            'user_id' => $userId,
            'filename' => $filename,
            'path' => $path
        ]);
        
        return $path;
    }
    
    /**
     * Validate avatar file
     */
    private function validateAvatar(UploadedFile $avatar): void
    {
        // Check file size (2MB max)
        if ($avatar->getSize() > 2 * 1024 * 1024) {
            throw new \Exception('Avatar file size must be less than 2MB.');
        }
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($avatar->getMimeType(), $allowedTypes)) {
            throw new \Exception('Avatar must be a valid image file (JPG, PNG, GIF).');
        }
        
        // Check image dimensions
        $imageInfo = getimagesize($avatar->getPathname());
        if ($imageInfo === false) {
            throw new \Exception('Invalid image file.');
        }
        
        // Optional: Check minimum dimensions
        if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
            throw new \Exception('Avatar image must be at least 100x100 pixels.');
        }
    }
    
    /**
     * Generate unique filename for avatar
     */
    private function generateAvatarFilename(UploadedFile $avatar, int $userId): string
    {
        $extension = $avatar->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $randomString = Str::random(8);
        
        return "user_{$userId}_{$timestamp}_{$randomString}.{$extension}";
    }
    
    /**
     * Delete avatar file
     */
    public function deleteAvatar(string $avatarPath): bool
    {
        if (Storage::disk('public')->exists($avatarPath)) {
            Storage::disk('public')->delete($avatarPath);
            
            Log::info('Avatar deleted successfully', [
                'path' => $avatarPath
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get avatar URL
     */
    public function getAvatarUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Check if avatar exists
     */
    public function avatarExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }
    
    /**
     * Get avatar file info
     */
    public function getAvatarInfo(string $path): ?array
    {
        if (!$this->avatarExists($path)) {
            return null;
        }
        
        return [
            'size' => Storage::disk('public')->size($path),
            'last_modified' => Storage::disk('public')->lastModified($path),
            'url' => $this->getAvatarUrl($path)
        ];
    }
} 