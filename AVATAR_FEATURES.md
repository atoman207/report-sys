# 👤 Avatar Upload Feature

## Overview
The system now supports user avatar uploads during registration, with proper validation, storage management, and fallback to default avatars.

## Database Changes

### Users Table Migration
```php
// Migration: 2025_08_01_100343_add_avatar_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('avatar')->nullable()->after('email');
});
```

### User Model Updates
```php
// Added to fillable array
protected $fillable = [
    'name',
    'email', 
    'password',
    'role',
    'avatar', // New field
];

// Added helper methods
public function getAvatarUrlAttribute(): string
public function hasCustomAvatar(): bool
public function getAvatarDisplayName(): string
```

## Features

### 📁 File Storage
- **Storage Location**: `storage/app/public/avatars/`
- **File Naming**: `user_{userId}_{timestamp}_{randomString}.{extension}`
- **Public Access**: Files accessible via `/storage/avatars/` URL

### ✅ Validation Rules
- **File Size**: Maximum 2MB
- **File Types**: JPG, PNG, GIF only
- **Image Dimensions**: Minimum 100x100 pixels
- **Required**: Optional during registration

### 🎨 Avatar Display
- **Custom Avatar**: Shows uploaded image if available
- **Default Avatar**: Falls back to `/images/default-avatar.png`
- **URL Access**: `$user->avatar_url` for direct access
- **Alt Text**: `$user->getAvatarDisplayName()` for accessibility

## Technical Implementation

### AvatarUploadService
```php
class AvatarUploadService
{
    public function uploadAvatar(UploadedFile $avatar, int $userId): string
    public function deleteAvatar(string $avatarPath): bool
    public function getAvatarUrl(string $path): string
    public function avatarExists(string $path): bool
    public function getAvatarInfo(string $path): ?array
}
```

### Registration Process
1. **User Creation**: User created first to get ID
2. **Avatar Upload**: File uploaded using AvatarUploadService
3. **Database Update**: Avatar path stored in users table
4. **Error Handling**: Registration continues even if avatar upload fails

### File Validation
```php
// Size check
if ($avatar->getSize() > 2 * 1024 * 1024) {
    throw new \Exception('Avatar file size must be less than 2MB.');
}

// Type check
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($avatar->getMimeType(), $allowedTypes)) {
    throw new \Exception('Avatar must be a valid image file (JPG, PNG, GIF).');
}

// Dimension check
$imageInfo = getimagesize($avatar->getPathname());
if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
    throw new \Exception('Avatar image must be at least 100x100 pixels.');
}
```

## Frontend Integration

### Registration Form
- **Click to Upload**: Avatar preview is clickable
- **File Input**: Hidden file input triggered by avatar click
- **Preview**: Real-time preview of selected image
- **Validation**: Client-side file size and type validation

### JavaScript Functions
```javascript
function previewAvatar(input) {
    // File validation and preview
}

function triggerFileUpload() {
    document.getElementById('avatar').click();
}
```

### CSS Styling
```css
.avatar-preview {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
}

.avatar-upload-overlay {
    // Hover overlay with camera icon
}
```

## Commands

### Test Avatar Functionality
```bash
# Test with latest user
php artisan test:avatar-upload

# Test with specific user
php artisan test:avatar-upload --user-id=6
```

### Storage Management
```bash
# Create storage link (already exists)
php artisan storage:link

# Create avatars directory
mkdir -p storage/app/public/avatars
```

## Usage Examples

### In Blade Templates
```php
<!-- Display user avatar -->
<img src="{{ $user->avatar_url }}" alt="{{ $user->getAvatarDisplayName() }}" class="avatar">

<!-- Check if user has custom avatar -->
@if($user->hasCustomAvatar())
    <img src="{{ $user->avatar_url }}" alt="Custom Avatar">
@else
    <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar">
@endif
```

### In Controllers
```php
// Get avatar URL
$avatarUrl = $user->avatar_url;

// Check for custom avatar
if ($user->hasCustomAvatar()) {
    // Handle custom avatar
}

// Get avatar info
$avatarInfo = $this->avatarUploadService->getAvatarInfo($user->avatar);
```

## Error Handling

### Upload Failures
- **Validation Errors**: File size, type, or dimension violations
- **Storage Errors**: Disk space or permission issues
- **Graceful Degradation**: Registration continues without avatar

### Logging
```php
// Success
Log::info('Avatar uploaded successfully', [
    'user_id' => $userId,
    'filename' => $filename,
    'path' => $path
]);

// Error
Log::error('Failed to upload avatar during registration', [
    'user_id' => $user->id,
    'error' => $e->getMessage()
]);
```

## Security Features

### File Validation
- **MIME Type Checking**: Ensures only image files
- **Size Limits**: Prevents large file uploads
- **Dimension Validation**: Ensures minimum image quality
- **Unique Filenames**: Prevents filename conflicts

### Storage Security
- **Public Disk**: Files stored in public storage for web access
- **Organized Structure**: Files stored in `avatars/` subdirectory
- **Access Control**: Files accessible via web but not directly executable

## Future Enhancements

### Potential Improvements
- **Image Resizing**: Automatic resizing to standard sizes
- **Avatar Cropping**: User interface for cropping avatars
- **Multiple Formats**: Support for WebP and other formats
- **CDN Integration**: Cloud storage for better performance
- **Avatar History**: Keep previous avatars for rollback
- **Bulk Operations**: Commands for managing avatar storage

### Performance Optimizations
- **Image Optimization**: Compress images for faster loading
- **Caching**: Cache avatar URLs for better performance
- **Lazy Loading**: Load avatars only when needed
- **Thumbnail Generation**: Create smaller versions for lists

## File Structure
```
storage/
├── app/
│   └── public/
│       └── avatars/
│           ├── user_1_1234567890_abc123.jpg
│           ├── user_2_1234567891_def456.png
│           └── ...
└── ...

public/
├── storage -> ../storage
└── images/
    └── default-avatar.png
``` 