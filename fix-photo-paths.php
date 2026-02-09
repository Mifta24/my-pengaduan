<?php

/**
 * Script to fix photo paths in Railway PostgreSQL database
 *
 * Problem: Old photos stored with wrong path (just filename.jpg)
 * Solution: Add complaints/photos/ prefix to existing photos
 *
 * Usage: php fix-photo-paths.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "🔍 Checking complaints with wrong photo paths...\n\n";

// Get complaints with photos that don't start with 'complaints/'
$complaints = DB::table('complaints')
    ->whereNotNull('photo')
    ->where('photo', 'not like', 'complaints/%')
    ->get();

if ($complaints->isEmpty()) {
    echo "✅ No complaints found with wrong photo paths!\n";
    exit(0);
}

echo "Found " . $complaints->count() . " complaints with wrong photo paths:\n\n";

$fixed = 0;
$notFound = 0;

foreach ($complaints as $complaint) {
    $oldPath = $complaint->photo;

    // Skip if already has proper path
    if (strpos($oldPath, 'complaints/photos/') === 0) {
        continue;
    }

    echo "Complaint ID: {$complaint->id}\n";
    echo "  Old path: {$oldPath}\n";

    // Try to find the file in different locations
    $possiblePaths = [
        "complaints/photos/{$oldPath}",
        "complaints/{$oldPath}",
        $oldPath
    ];

    $foundPath = null;
    foreach ($possiblePaths as $path) {
        if (Storage::disk('public')->exists($path)) {
            $foundPath = $path;
            break;
        }
    }

    if ($foundPath) {
        // If file found but not in correct location, move it
        $correctPath = "complaints/photos/" . basename($oldPath);

        if ($foundPath !== $correctPath) {
            // Move file to correct location
            $fileContents = Storage::disk('public')->get($foundPath);
            Storage::disk('public')->put($correctPath, $fileContents);

            // Delete old file if different location
            if ($foundPath !== $oldPath) {
                Storage::disk('public')->delete($foundPath);
            }

            echo "  ✅ Moved: {$foundPath} → {$correctPath}\n";
        } else {
            echo "  ✅ File already in correct location\n";
        }

        // Update database
        DB::table('complaints')
            ->where('id', $complaint->id)
            ->update(['photo' => $correctPath]);

        echo "  ✅ Database updated: {$correctPath}\n\n";
        $fixed++;
    } else {
        echo "  ❌ File not found in storage\n\n";
        $notFound++;
    }
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "Summary:\n";
echo "  Fixed: {$fixed}\n";
echo "  Not found: {$notFound}\n";
echo "  Total: {$complaints->count()}\n";
echo "═══════════════════════════════════════\n";

if ($fixed > 0) {
    echo "\n✅ Photo paths fixed successfully!\n";
    echo "   You can now view photos at:\n";
    echo "   " . config('app.url') . "/storage/complaints/photos/filename.jpg\n";
}

if ($notFound > 0) {
    echo "\n⚠️  {$notFound} files not found in storage.\n";
    echo "   These photos were never uploaded or deleted.\n";
}
