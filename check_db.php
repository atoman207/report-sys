<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Report;
use Illuminate\Support\Facades\Storage;

echo "Checking Report 4 database paths...\n";

$report = Report::find(4);
if (!$report) {
    echo "Report 4 not found\n";
    exit;
}

echo "Report ID: " . $report->id . "\n";
echo "Images in DB: " . json_encode($report->images) . "\n";
echo "Signature in DB: " . $report->signature . "\n";

echo "\nChecking storage paths:\n";
if ($report->images) {
    foreach ($report->images as $index => $imgPath) {
        $exists = Storage::disk('public')->exists($imgPath);
        echo "Image $index: $imgPath - Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
}

if ($report->signature) {
    $exists = Storage::disk('public')->exists($report->signature);
    echo "Signature: $report->signature - Exists: " . ($exists ? 'YES' : 'NO') . "\n";
}

echo "\nActual files in storage:\n";
$files = Storage::disk('public')->allFiles('reports/4');
foreach ($files as $file) {
    echo "- $file\n";
} 