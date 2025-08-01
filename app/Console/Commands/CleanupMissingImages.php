<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class CleanupMissingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup-missing-images {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up database records for missing images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning up missing images...');
        $this->line('');

        $reports = Report::whereNotNull('images')->get();
        $totalReports = $reports->count();
        $cleanedReports = 0;
        $dryRun = $this->option('dry-run');

        if ($totalReports === 0) {
            $this->info('✅ No reports with images found.');
            return 0;
        }

        $this->info("Found {$totalReports} reports with images:");
        $this->line('');

        foreach ($reports as $report) {
            $this->line("Report #{$report->id} ({$report->company}):");
            
            if ($report->hasImages()) {
                $validImages = [];
                $removedImages = 0;
                
                foreach ($report->images as $imagePath) {
                    $exists = Storage::disk('public')->exists($imagePath);
                    
                    if ($exists) {
                        $validImages[] = $imagePath;
                        $size = Storage::disk('public')->size($imagePath);
                        $this->info("  ✅ Image: {$imagePath} ({$this->formatBytes($size)})");
                    } else {
                        $removedImages++;
                        $this->warn("  ❌ Removing missing image: {$imagePath}");
                    }
                }
                
                // Update report with only valid images
                if ($removedImages > 0) {
                    if (!$dryRun) {
                        $report->update(['images' => $validImages]);
                        $this->info("  ✅ Updated report: removed {$removedImages} missing images");
                    } else {
                        $this->info("  🔍 Would update report: remove {$removedImages} missing images");
                    }
                    $cleanedReports++;
                }
            }
            
            // Check signature
            if ($report->signature) {
                $signatureExists = Storage::disk('public')->exists($report->signature);
                if ($signatureExists) {
                    $size = Storage::disk('public')->size($report->signature);
                    $this->info("  ✅ Signature: {$report->signature} ({$this->formatBytes($size)})");
                } else {
                    $this->warn("  ❌ Removing missing signature: {$report->signature}");
                    if (!$dryRun) {
                        $report->update(['signature' => null]);
                        $this->info("  ✅ Updated report: removed missing signature");
                    } else {
                        $this->info("  🔍 Would update report: remove missing signature");
                    }
                    $cleanedReports++;
                }
            }
            
            $this->line('');
        }

        // Summary
        $this->info('📊 Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Reports with Images', $totalReports],
                ['Reports Cleaned', $cleanedReports],
                ['Mode', $dryRun ? 'Dry Run' : 'Live'],
            ]
        );

        if ($cleanedReports > 0) {
            if ($dryRun) {
                $this->info("🔍 Found {$cleanedReports} reports that need cleanup (dry run mode)");
                $this->info("💡 Run without --dry-run to actually clean up the database");
            } else {
                $this->info("✅ Successfully cleaned up {$cleanedReports} reports!");
            }
        } else {
            $this->info('✅ No cleanup needed - all images are accessible!');
        }

        return 0;
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