<?php

/**
 * Diagnostic script to check photo upload issue in Railway
 * 
 * Usage: php check-photo-issue.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

echo "═══════════════════════════════════════════════════════════════\n";
echo "🔍 PHOTO UPLOAD DIAGNOSTIC - Railway Environment\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. Check environment config
echo "1️⃣  ENVIRONMENT CONFIGURATION:\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   FILESYSTEM_DISK: " . config('filesystems.default') . "\n";
echo "   Storage URL: " . config('filesystems.disks.public.url') . "\n";
echo "\n";

// 2. Check storage link
echo "2️⃣  STORAGE LINK STATUS:\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath)) {
    echo "   ✅ Storage link EXISTS\n";
    echo "   Link target: " . readlink($publicStoragePath) . "\n";
} elseif (is_dir($publicStoragePath)) {
    echo "   ⚠️  Storage directory exists but is NOT a symlink\n";
} else {
    echo "   ❌ Storage link MISSING\n";
    echo "   Run: php artisan storage:link\n";
}
echo "\n";

// 3. Check latest complaint with photo
echo "3️⃣  LATEST COMPLAINT WITH PHOTO:\n";
$complaint = DB::table('complaints')
    ->whereNotNull('photo')
    ->orderBy('created_at', 'desc')
    ->first(['id', 'photo', 'created_at']);

if ($complaint) {
    echo "   Complaint ID: {$complaint->id}\n";
    echo "   Photo path in DB: {$complaint->photo}\n";
    echo "   Created at: {$complaint->created_at}\n";
    
    // Check if path is correct
    if (strpos($complaint->photo, 'complaints/photos/') === 0) {
        echo "   ✅ Path format is CORRECT\n";
    } else {
        echo "   ❌ Path format is WRONG (missing complaints/photos/)\n";
        echo "   Should be: complaints/photos/{$complaint->photo}\n";
    }
    
    // Check if file exists
    $fileExists = Storage::disk('public')->exists($complaint->photo);
    echo "   File exists: " . ($fileExists ? "✅ YES" : "❌ NO") . "\n";
    
    if ($fileExists) {
        $url = Storage::disk('public')->url($complaint->photo);
        echo "   Generated URL: {$url}\n";
    } else {
        // Try to find the file
        $fileName = basename($complaint->photo);
        $possiblePaths = [
            "complaints/photos/{$fileName}",
            "complaints/{$fileName}",
            $fileName
        ];
        
        echo "   Searching file in other locations...\n";
        foreach ($possiblePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                echo "   ✅ Found at: {$path}\n";
                break;
            }
        }
    }
    
    // Generate what the URL should be
    $correctPath = strpos($complaint->photo, 'complaints/photos/') === 0 
        ? $complaint->photo 
        : 'complaints/photos/' . basename($complaint->photo);
    
    $correctUrl = config('app.url') . '/storage/' . $correctPath;
    echo "\n   ✅ CORRECT URL SHOULD BE:\n";
    echo "   {$correctUrl}\n";
} else {
    echo "   No complaints with photos found\n";
}

echo "\n";

// 4. Check complaints with wrong paths
echo "4️⃣  COMPLAINTS WITH WRONG PHOTO PATHS:\n";
$wrongPaths = DB::table('complaints')
    ->whereNotNull('photo')
    ->where('photo', 'not like', 'complaints/%')
    ->count();

if ($wrongPaths > 0) {
    echo "   ❌ Found {$wrongPaths} complaints with wrong paths\n";
    echo "   Run: php fix-photo-paths.php\n";
} else {
    echo "   ✅ All complaints have correct photo paths\n";
}

echo "\n";

// 5. Test file upload capability
echo "5️⃣  STORAGE WRITE TEST:\n";
try {
    $testPath = 'test-upload-' . time() . '.txt';
    Storage::disk('public')->put($testPath, 'Test upload from diagnostic script');
    
    if (Storage::disk('public')->exists($testPath)) {
        echo "   ✅ Write test SUCCESS\n";
        echo "   Test file: {$testPath}\n";
        Storage::disk('public')->delete($testPath);
        echo "   ✅ Delete test SUCCESS\n";
    } else {
        echo "   ❌ Write test FAILED - file not found after upload\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Write test ERROR: {$e->getMessage()}\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📋 RECOMMENDATIONS:\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if ($wrongPaths > 0) {
    echo "1. Fix old photo paths:\n";
    echo "   php fix-photo-paths.php\n\n";
}

if (!is_link($publicStoragePath)) {
    echo "2. Create storage link:\n";
    echo "   php artisan storage:link\n\n";
}

echo "3. Clear all caches:\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n\n";

echo "4. Test upload new photo and check database:\n";
echo "   SELECT id, photo, created_at FROM complaints WHERE photo IS NOT NULL ORDER BY created_at DESC LIMIT 1;\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
