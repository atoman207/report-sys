<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ManageImageStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:manage-images {action=stats} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage image storage: stats, cleanup, or organize files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $dryRun = $this->option('dry-run');

        switch ($action) {
            case 'stats':
                $this->showStorageStats();
                break;
            case 'cleanup':
                $this->cleanupOrphanedFiles($dryRun);
                break;
            case 'organize':
                $this->organizeFiles($dryRun);
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: stats, cleanup, organize");
                return 1;
        }

        return 0;
    }

    /**
     * Show storage statistics
     */
    private function showStorageStats()
    {
        $this->info('📊 Image Storage Statistics');
        $this->line('');

        // Get all reports with images
        $reportsWithImages = Report::whereNotNull('images')->get();
        $totalReports = $reportsWithImages->count();
        $totalImages = $reportsWithImages->sum('image_count');
        $totalSize = $reportsWithImages->sum('total_image_size');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Reports with Images', $totalReports],
                ['Total Images', $totalImages],
                ['Total Size', $this->formatBytes($totalSize)],
                ['Average Images per Report', $totalReports > 0 ? round($totalImages / $totalReports, 1) : 0],
                ['Average Size per Report', $totalReports > 0 ? $this->formatBytes($totalSize / $totalReports) : '0 B'],
            ]
        );

        // Check storage disk usage
        $this->info('💾 Storage Disk Information');
        $disk = Storage::disk('public');
        $totalDiskSize = 0;
        $totalDiskFiles = 0;

        if ($disk->exists('reports')) {
            $files = $disk->allFiles('reports');
            $totalDiskFiles = count($files);
            
            foreach ($files as $file) {
                $totalDiskSize += $disk->size($file);
            }
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files on Disk', $totalDiskFiles],
                ['Total Disk Size', $this->formatBytes($totalDiskSize)],
                ['Database Records', $totalImages],
                ['Orphaned Files', $totalDiskFiles - $totalImages],
            ]
        );
    }

    /**
     * Clean up orphaned files
     */
    private function cleanupOrphanedFiles($dryRun)
    {
        $this->info('🧹 Cleaning up orphaned files...');
        
        // Get all image paths from database
        $dbPaths = Report::whereNotNull('images')
            ->get()
            ->flatMap(function ($report) {
                $paths = $report->images ?? [];
                $signaturePath = $report->signature;
                if ($signaturePath) {
                    $paths[] = $signaturePath;
                }
                return $paths;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Get all files on disk
        $disk = Storage::disk('public');
        $diskFiles = $disk->allFiles('reports');

        // Find orphaned files
        $orphanedFiles = array_filter($diskFiles, function ($file) use ($dbPaths) {
            return !in_array($file, $dbPaths);
        });

        if (empty($orphanedFiles)) {
            $this->info('✅ No orphaned files found.');
            return;
        }

        $this->warn("Found " . count($orphanedFiles) . " orphaned files:");

        foreach ($orphanedFiles as $file) {
            $size = $disk->size($file);
            $this->line("  - {$file} ({$this->formatBytes($size)})");
            
            if (!$dryRun) {
                $disk->delete($file);
                $this->info("    ✅ Deleted");
            } else {
                $this->comment("    [DRY RUN] Would delete");
            }
        }

        if ($dryRun) {
            $this->info('🔍 Dry run completed. Use --dry-run=false to actually delete files.');
        } else {
            $this->info('✅ Cleanup completed successfully.');
        }
    }

    /**
     * Organize files by report ID
     */
    private function organizeFiles($dryRun)
    {
        $this->info('📁 Organizing files by report ID...');
        
        $disk = Storage::disk('public');
        $reports = Report::whereNotNull('images')->get();

        foreach ($reports as $report) {
            $this->line("Processing Report #{$report->id}...");
            
            if ($report->images) {
                foreach ($report->images as $imagePath) {
                    $this->organizeFile($disk, $imagePath, $report->id, $dryRun);
                }
            }
            
            if ($report->signature) {
                $this->organizeFile($disk, $report->signature, $report->id, $dryRun);
            }
        }

        if ($dryRun) {
            $this->info('🔍 Dry run completed. Use --dry-run=false to actually organize files.');
        } else {
            $this->info('✅ File organization completed successfully.');
        }
    }

    /**
     * Organize a single file
     */
    private function organizeFile($disk, $path, $reportId, $dryRun)
    {
        // Skip if already organized
        if (strpos($path, "reports/{$reportId}/") === 0) {
            return;
        }

        $filename = basename($path);
        $newPath = "reports/{$reportId}/{$filename}";

        if ($disk->exists($path)) {
            if (!$dryRun) {
                $disk->move($path, $newPath);
                $this->info("    ✅ Moved {$path} → {$newPath}");
            } else {
                $this->comment("    [DRY RUN] Would move {$path} → {$newPath}");
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 