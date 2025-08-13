<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\File;
use ZipArchive;

class AdminImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the admin image management dashboard
     */
    public function index()
    {
        $reports = Report::with('user')
            ->whereNotNull('images')
            ->orWhereNotNull('signature')
            ->orderBy('created_at', 'desc')
            ->get();

        $imageStats = $this->getImageStatistics();
        
        return view('admin.images.index', compact('reports', 'imageStats'));
    }

    /**
     * Preview a specific image
     */
    public function preview($reportId, $imagePath)
    {
        $report = Report::with('user')->findOrFail($reportId);
        
        // Decode the URL-encoded image path
        $imagePath = urldecode($imagePath);
        
        // Debug: Log the report data
        \Log::info('Preview Debug', [
            'report_id' => $reportId,
            'report_images' => $report->images,
            'report_signature' => $report->signature,
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage')
        ]);
        
        // Prepare all images for the gallery
        $allImages = [];
        
        // Add report images (normalized)
        if ($report->normalized_images) {
            foreach ($report->normalized_images as $index => $imgPath) {
                \Log::info('Checking image path', ['path' => $imgPath, 'exists' => Storage::disk('public')->exists($imgPath)]);
                if (Storage::disk('public')->exists($imgPath)) {
                    $allImages[] = [
                        'path' => $imgPath,
                        'url' => Storage::disk('public')->url($imgPath),
                        'filename' => basename($imgPath),
                        'type' => 'report_image',
                        'index' => $index
                    ];
                }
            }
        }
        
        // Add signature if exists (normalized)
        if ($report->normalized_signature && Storage::disk('public')->exists($report->normalized_signature)) {
            \Log::info('Checking signature path', ['path' => $report->normalized_signature, 'exists' => Storage::disk('public')->exists($report->normalized_signature)]);
            $allImages[] = [
                'path' => $report->normalized_signature,
                'url' => Storage::disk('public')->url($report->normalized_signature),
                'filename' => basename($report->normalized_signature),
                'type' => 'signature',
                'index' => count($allImages)
            ];
        }
        
        \Log::info('Final allImages array', ['count' => count($allImages), 'images' => $allImages]);
        
        // If no images found, redirect back with error
        if (empty($allImages)) {
            return redirect()->route('admin.images.index')
                ->with('error', 'このレポートには画像がありません。');
        }
        
        return view('admin.images.preview', compact('report', 'allImages'));
    }

    /**
     * Download a single image
     */
    public function download($reportId, $imagePath)
    {
        $report = Report::findOrFail($reportId);
        $imagePath = urldecode($imagePath);
        
        if (!Storage::disk('public')->exists($imagePath)) {
            abort(404, 'Image not found');
        }

        $filename = basename($imagePath);
        $downloadName = "report_{$reportId}_{$filename}";
        
        return Storage::disk('public')->download($imagePath, $downloadName);
    }

    /**
     * Download all images for a specific report
     */
    public function downloadReportImages($reportId)
    {
        $report = Report::findOrFail($reportId);
        
        if (empty($report->images) && empty($report->signature)) {
            return back()->with('error', 'このレポートには画像がありません。');
        }

        $zip = new ZipArchive();
        $zipName = "report_{$reportId}_images.zip";
        $zipPath = storage_path("app/temp/{$zipName}");
        
        // Create temp directory if it doesn't exist
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'ZIPファイルの作成に失敗しました。');
        }

        // Add images to zip (normalized)
        if ($report->normalized_images) {
            foreach ($report->normalized_images as $index => $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    $filename = basename($imagePath);
                    $imageNumber = $index + 1;
                    $zip->addFromString("images/image_{$imageNumber}_{$filename}", 
                        Storage::disk('public')->get($imagePath));
                }
            }
        }

        // Add signature to zip (normalized)
        if ($report->normalized_signature && Storage::disk('public')->exists($report->normalized_signature)) {
            $signatureFilename = basename($report->normalized_signature);
            $zip->addFromString("signature_{$signatureFilename}", 
                Storage::disk('public')->get($report->normalized_signature));
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend();
    }

    /**
     * Download all images from all reports
     */
    public function downloadAllImages()
    {
        $reports = Report::whereNotNull('images')
            ->orWhereNotNull('signature')
            ->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'システムに画像がありません。');
        }

        $zip = new ZipArchive();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $zipName = "all_reports_images_{$timestamp}.zip";
        $zipPath = storage_path("app/temp/{$zipName}");
        
        // Create temp directory if it doesn't exist
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'ZIPファイルの作成に失敗しました。');
        }

        foreach ($reports as $report) {
            $reportFolder = "report_{$report->id}_{$report->company}";
            
            // Add images (normalized)
            if ($report->normalized_images) {
                foreach ($report->normalized_images as $index => $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        $filename = basename($imagePath);
                        $imageNumber = $index + 1;
                        $zip->addFromString("{$reportFolder}/images/image_{$imageNumber}_{$filename}", 
                            Storage::disk('public')->get($imagePath));
                    }
                }
            }

            // Add signature (normalized)
            if ($report->normalized_signature && Storage::disk('public')->exists($report->normalized_signature)) {
                $signatureFilename = basename($report->normalized_signature);
                $zip->addFromString("{$reportFolder}/signature_{$signatureFilename}", 
                    Storage::disk('public')->get($report->normalized_signature));
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend();
    }

    /**
     * Delete a specific image
     */
    public function deleteImage(Request $request, $reportId, $imagePath)
    {
        $report = Report::findOrFail($reportId);
        $imagePath = urldecode($imagePath);
        
        if (!Storage::disk('public')->exists($imagePath)) {
            return back()->with('error', '画像が見つかりません。');
        }

        // Remove image from report's images array
        if ($report->images && in_array($imagePath, $report->images)) {
            $report->images = array_values(array_filter($report->images, function($path) use ($imagePath) {
                return $path !== $imagePath;
            }));
            $report->save();
        }

        // Delete the actual file
        Storage::disk('public')->delete($imagePath);

        return back()->with('success', '画像を削除しました。');
    }

    /**
     * Stream a file from the public storage disk
     */
    public function file($path)
    {
        $path = urldecode($path);
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $mime = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';
        $contents = Storage::disk('public')->get($path);
        return response($contents, 200)->header('Content-Type', $mime);
    }

    /**
     * Get image statistics
     */
    private function getImageStatistics()
    {
        $reports = Report::whereNotNull('images')
            ->orWhereNotNull('signature')
            ->get();

        $totalImages = 0;
        $totalSize = 0;
        $reportsWithImages = 0;
        $reportsWithSignatures = 0;

        foreach ($reports as $report) {
            if ($report->images) {
                $reportsWithImages++;
                $totalImages += count($report->images);
                
                foreach ($report->images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        $totalSize += Storage::disk('public')->size($imagePath);
                    }
                }
            }

            if ($report->signature) {
                $reportsWithSignatures++;
                if (Storage::disk('public')->exists($report->signature)) {
                    $totalSize += Storage::disk('public')->size($report->signature);
                }
            }
        }

        return [
            'total_reports_with_images' => $reportsWithImages,
            'total_images' => $totalImages,
            'total_image_size' => $totalSize,
            'reports_with_signatures' => $reportsWithSignatures,
            'formatted_total_size' => $this->formatBytes($totalSize)
        ];
    }

    /**
     * Get detailed image information
     */
    private function getImageInfo($imagePath)
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($imagePath);
        $fileInfo = getimagesize($fullPath);

        return [
            'path' => $imagePath,
            'url' => Storage::disk('public')->url($imagePath),
            'size' => Storage::disk('public')->size($imagePath),
            'formatted_size' => $this->formatBytes(Storage::disk('public')->size($imagePath)),
            'width' => $fileInfo[0] ?? null,
            'height' => $fileInfo[1] ?? null,
            'mime_type' => $fileInfo['mime'] ?? null,
            'filename' => basename($imagePath),
            'last_modified' => Storage::disk('public')->lastModified($imagePath),
            'formatted_last_modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($imagePath))
        ];
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