<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AvatarUploadService;
use Illuminate\Support\Facades\Storage;

class TestAvatarUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:avatar-upload {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test avatar upload functionality';

    protected $avatarUploadService;

    /**
     * Execute the console command.
     */
    public function __construct(AvatarUploadService $avatarUploadService)
    {
        parent::__construct();
        $this->avatarUploadService = $avatarUploadService;
    }

    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $user = User::latest()->first();
            if (!$user) {
                $this->error("No users found in the database.");
                return 1;
            }
        }
        
        $this->info("Testing avatar functionality for User: {$user->name} (ID: {$user->id})");
        $this->line("Current avatar: " . ($user->avatar ?? 'None'));
        $this->line("Avatar URL: {$user->avatar_url}");
        $this->line("Has custom avatar: " . ($user->hasCustomAvatar() ? 'Yes' : 'No'));
        $this->line("");
        
        // Test avatar storage info
        if ($user->avatar) {
            $this->info("Avatar Storage Information:");
            $info = $this->avatarUploadService->getAvatarInfo($user->avatar);
            if ($info) {
                $this->line("  - File size: " . $this->formatBytes($info['size']));
                $this->line("  - Last modified: " . date('Y-m-d H:i:s', $info['last_modified']));
                $this->line("  - URL: {$info['url']}");
                $this->line("  - Exists on disk: " . ($this->avatarUploadService->avatarExists($user->avatar) ? 'Yes' : 'No'));
            } else {
                $this->warn("  - Avatar file not found on disk");
            }
        }
        
        $this->line("");
        $this->info("✅ Avatar functionality test completed!");
        
        return 0;
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