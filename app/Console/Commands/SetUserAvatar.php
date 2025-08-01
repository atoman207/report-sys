<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SetUserAvatar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-avatar {email} {image-path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user\'s avatar by copying an image file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $imagePath = $this->argument('image-path');

        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $this->info("Found user: {$user->name} (ID: {$user->id})");

        // Check if image file exists
        if (!file_exists($imagePath)) {
            $this->error("Image file not found: {$imagePath}");
            $this->line("Please provide the full path to the image file.");
            return 1;
        }

        // Validate image file
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            $this->error("Invalid image file: {$imagePath}");
            return 1;
        }

        $fileSize = filesize($imagePath);
        if ($fileSize > 2 * 1024 * 1024) {
            $this->error("Image file is too large. Maximum size is 2MB.");
            return 1;
        }

        $this->info("Image validation passed:");
        $this->line("  - Size: " . $this->formatBytes($fileSize));
        $this->line("  - Dimensions: {$imageInfo[0]}x{$imageInfo[1]} pixels");
        $this->line("  - Type: " . image_type_to_mime_type($imageInfo[2]));

        try {
            // Generate unique filename
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $timestamp = now()->timestamp;
            $randomString = Str::random(8);
            $filename = "user_{$user->id}_{$timestamp}_{$randomString}.{$extension}";
            
            // Copy file to avatars directory
            $destinationPath = "avatars/{$filename}";
            $sourceContent = file_get_contents($imagePath);
            
            if (Storage::disk('public')->put($destinationPath, $sourceContent)) {
                // Update user's avatar in database
                $user->avatar = $destinationPath;
                $user->save();

                $this->info("✅ Avatar set successfully!");
                $this->line("  - File: {$destinationPath}");
                $this->line("  - URL: " . Storage::disk('public')->url($destinationPath));
                $this->line("  - User: {$user->name} ({$user->email})");

                return 0;
            } else {
                $this->error("Failed to copy image file to storage.");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("Error setting avatar: " . $e->getMessage());
            return 1;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1024 * 1024) {
            return round($bytes / 1024, $precision) . ' KB';
        } else {
            return round($bytes / (1024 * 1024), $precision) . ' MB';
        }
    }
} 