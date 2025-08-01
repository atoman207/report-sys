<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class VerifyImageStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:verify-images {--fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all images stored in the database are accessible on the server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying image storage...');
        $this->line('');

        $reports = Report::whereNotNull('images')->get();
        $totalReports = $reports->count();
        $totalImages = 0;
        $accessibleImages = 0;
        $missingImages = 0;
        $issues = [];

        if ($totalReports === 0) {
            $this->info('✅ No reports with images found.');
            return 0;
        }

        $this->info("Found {$totalReports} reports with images:");
        $this->line('');

        foreach ($reports as $report) {
            $this->line("Report #{$report->id} ({$report->company}):");
            
            if ($report->hasImages()) {
                foreach ($report->images as $index => $imagePath) {
                    $totalImages++;
                    $exists = Storage::disk('public')->exists($imagePath);
                    
                    if ($exists) {
                        $accessibleImages++;
                        $size = Storage::disk('public')->size($imagePath);
                        $this->info("  ✅ Image " . ($index + 1) . ": {$imagePath} ({$this->formatBytes($size)})");
                    } else {
                        $missingImages++;
                        $this->error("  ❌ Image " . ($index + 1) . ": {$imagePath} (MISSING)");
                        $issues[] = [
                            'report_id' => $report->id,
                            'company' => $report->company,
                            'image_path' => $imagePath,
                            'issue' => 'File not found on disk'
                        ];
                    }
                }
            }
            
            // Check signature
            if ($report->signature) {
                $signatureExists = Storage::disk('public')->exists($report->signature);
                if ($signatureExists) {
                    $size = Storage::disk('public')->size($report->signature);
                    $this->info("  ✅ Signature: {$report->signature} ({$this->formatBytes($size)})");
                } else {
                    $this->error("  ❌ Signature: {$report->signature} (MISSING)");
                    $issues[] = [
                        'report_id' => $report->id,
                        'company' => $report->company,
                        'image_path' => $report->signature,
                        'issue' => 'Signature file not found on disk'
                    ];
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
                ['Total Images in Database', $totalImages],
                ['Accessible Images', $accessibleImages],
                ['Missing Images', $missingImages],
                ['Accessibility Rate', $totalImages > 0 ? round(($accessibleImages / $totalImages) * 100, 1) . '%' : '0%'],
            ]
        );

        if ($missingImages > 0) {
            $this->warn("⚠️  Found {$missingImages} missing images!");
            
            if ($this->option('fix')) {
                $this->info('🔧 Attempting to fix issues...');
                $this->fixImageIssues($issues);
            } else {
                $this->info('💡 Run with --fix option to attempt automatic fixes.');
            }
        } else {
            $this->info('✅ All images are accessible!');
        }

        return $missingImages > 0 ? 1 : 0;
    }

    /**
     * Attempt to fix image issues
     */
    private function fixImageIssues($issues)
    {
        $fixed = 0;
        
        foreach ($issues as $issue) {
            $this->line("Fixing issue for Report #{$issue['report_id']}:");
            
            // Check if file exists in different locations
            $possiblePaths = [
                $issue['image_path'],
                str_replace('reports/', 'reports/old/', $issue['image_path']),
                'reports/' . basename($issue['image_path']),
            ];
            
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $this->info("  ✅ Found file at: {$path}");
                    
                    // Update the database record
                    $report = Report::find($issue['report_id']);
                    if ($report) {
                        if (strpos($issue['image_path'], 'signature') !== false) {
                            $report->update(['signature' => $path]);
                        } else {
                            $images = $report->images;
                            $key = array_search($issue['image_path'], $images);
                            if ($key !== false) {
                                $images[$key] = $path;
                                $report->update(['images' => $images]);
                            }
                        }
                        $fixed++;
                        $this->info("  ✅ Updated database record");
                    }
                    break;
                }
            }
            
            if (!Storage::disk('public')->exists($issue['image_path'])) {
                $this->error("  ❌ Could not find file: {$issue['image_path']}");
            }
        }
        
        if ($fixed > 0) {
            $this->info("✅ Fixed {$fixed} issues!");
        } else {
            $this->warn("⚠️  Could not automatically fix any issues.");
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