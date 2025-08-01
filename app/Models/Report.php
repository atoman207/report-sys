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
     * Get image URLs for display
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }

        return collect($this->images)->map(function ($path) {
            return Storage::disk('public')->url($path);
        })->toArray();
    }

    /**
     * Get signature URL for display
     */
    public function getSignatureUrlAttribute()
    {
        if (!$this->signature) {
            return null;
        }

        return Storage::disk('public')->url($this->signature);
    }

    /**
     * Check if report has images
     */
    public function hasImages()
    {
        return !empty($this->images) && is_array($this->images);
    }

    /**
     * Get image count
     */
    public function getImageCountAttribute()
    {
        return $this->hasImages() ? count($this->images) : 0;
    }

    /**
     * Get total file size of images
     */
    public function getTotalImageSizeAttribute()
    {
        if (!$this->hasImages()) {
            return 0;
        }

        $totalSize = 0;
        foreach ($this->images as $path) {
            if (Storage::disk('public')->exists($path)) {
                $totalSize += Storage::disk('public')->size($path);
            }
        }

        return $totalSize;
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
