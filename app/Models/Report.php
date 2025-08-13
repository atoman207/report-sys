<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'company', 'person', 'site', 'store', 'work_type', 'task_type', 'request_detail',
        'start_time', 'end_time', 'visit_status', 'repair_place', 'visit_status_detail',
        'work_detail', 'signature', 'images', 'user_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'images' => 'array',
    ];

    /**
     * Get the user who created this report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Normalize a stored path to be relative to the public disk root.
     */
    private function normalizeStoragePath(string $path): string
    {
        $normalized = str_replace('\\', '/', trim($path));

        // Strip scheme/host if a full URL was stored
        $normalized = preg_replace('#^https?://[^/]+/#i', '', $normalized);

        // If path starts with public URL prefix "storage/", remove it
        if (strpos($normalized, 'storage/') === 0) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        // If an absolute server path was stored, keep only segment after storage/app/public/
        $needle = 'storage/app/public/';
        $pos = stripos($normalized, $needle);
        if ($pos !== false) {
            $normalized = substr($normalized, $pos + strlen($needle));
        }

        // Remove leading slashes
        $normalized = ltrim($normalized, '/');

        return $normalized;
    }

    /**
     * Normalized images array (paths relative to public disk root)
     */
    public function getNormalizedImagesAttribute(): array
    {
        if (empty($this->images) || !is_array($this->images)) {
            return [];
        }

        return collect($this->images)
            ->filter()
            ->map(fn ($p) => $this->normalizeStoragePath((string) $p))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Normalized signature path (relative to public disk root)
     */
    public function getNormalizedSignatureAttribute(): ?string
    {
        if (empty($this->signature)) {
            return null;
        }

        return $this->normalizeStoragePath((string) $this->signature);
    }

    /**
     * Get image URLs for display (auto-normalized)
     */
    public function getImageUrlsAttribute()
    {
        $paths = $this->normalized_images;
        if (empty($paths)) {
            return [];
        }

        return collect($paths)->map(function ($path) {
            return Storage::disk('public')->url($path);
        })->toArray();
    }

    /**
     * Get signature URL for display (auto-normalized)
     */
    public function getSignatureUrlAttribute()
    {
        if (!$this->normalized_signature) {
            return null;
        }

        return Storage::disk('public')->url($this->normalized_signature);
    }

    /**
     * Check if report has images
     */
    public function hasImages()
    {
        return !empty($this->normalized_images);
    }

    /**
     * Get image count
     */
    public function getImageCountAttribute()
    {
        return count($this->normalized_images);
    }

    /**
     * Get total file size of images
     */
    public function getTotalImageSizeAttribute()
    {
        if (!$this->hasImages()) {
            return 0;
        }
        $total = 0;
        foreach ($this->normalized_images as $path) {
            if (Storage::disk('public')->exists($path)) {
                $total += Storage::disk('public')->size($path);
            }
        }
        return $total;
    }

    /**
     * Get formatted total image size
     */
    public function getFormattedImageSizeAttribute()
    {
        $size = $this->total_image_size;
        
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 1) . ' KB';
        } else {
            return round($size / (1024 * 1024), 1) . ' MB';
        }
    }
}
