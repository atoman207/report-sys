<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "=== Administrator Avatar Setter ===\n\n";

// Find the administrator
$user = User::where('email', 'goodsman207@gmail.com')->first();

if (!$user) {
    echo "❌ User with email 'goodsman207@gmail.com' not found.\n";
    exit(1);
}

echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
echo "Current avatar: " . ($user->avatar ?: 'None') . "\n\n";

// Ask for image path
echo "Please provide the full path to the image file:\n";
echo "Example: C:\\Users\\YourName\\Pictures\\admin-avatar.jpg\n\n";

$imagePath = trim(fgets(STDIN));

if (!file_exists($imagePath)) {
    echo "❌ Image file not found: {$imagePath}\n";
    echo "Please make sure the file exists and the path is correct.\n";
    exit(1);
}

// Validate image
$imageInfo = getimagesize($imagePath);
if ($imageInfo === false) {
    echo "❌ Invalid image file: {$imagePath}\n";
    exit(1);
}

$fileSize = filesize($imagePath);
if ($fileSize > 2 * 1024 * 1024) {
    echo "❌ Image file is too large. Maximum size is 2MB.\n";
    exit(1);
}

echo "✅ Image validation passed:\n";
echo "  - Size: " . formatBytes($fileSize) . "\n";
echo "  - Dimensions: {$imageInfo[0]}x{$imageInfo[1]} pixels\n";
echo "  - Type: " . image_type_to_mime_type($imageInfo[2]) . "\n\n";

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

        echo "✅ Avatar set successfully!\n";
        echo "  - File: {$destinationPath}\n";
        echo "  - URL: " . Storage::disk('public')->url($destinationPath) . "\n";
        echo "  - User: {$user->name} ({$user->email})\n\n";
        
        echo "The avatar will now appear in the header when the administrator logs in.\n";
    } else {
        echo "❌ Failed to copy image file to storage.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "❌ Error setting avatar: " . $e->getMessage() . "\n";
    exit(1);
}

function formatBytes($bytes, $precision = 2) {
    if ($bytes < 1024) {
        return $bytes . ' B';
    } elseif ($bytes < 1024 * 1024) {
        return round($bytes / 1024, $precision) . ' KB';
    } else {
        return round($bytes / (1024 * 1024), $precision) . ' MB';
    }
} 