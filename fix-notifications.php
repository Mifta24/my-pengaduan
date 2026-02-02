#!/usr/bin/env php
<?php

/**
 * Quick Fix untuk Setup Notifikasi
 *
 * Script ini akan:
 * 1. Create migration untuk table yang missing
 * 2. Setup default notification settings
 * 3. Clear cache
 * 4. Verify configuration
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "🔧 Quick Fix - Notification Setup\n";
echo str_repeat("=", 50) . "\n\n";

// Step 1: Check Database Tables
echo "1️⃣  Checking database tables...\n";

$requiredTables = [
    'user_devices' => 'Device tokens storage',
    'fcm_notifications' => 'Notification logs',
    'notification_settings' => 'User preferences',
];

$missingTables = [];
foreach ($requiredTables as $table => $description) {
    if (Schema::hasTable($table)) {
        echo "   ✅ {$table} exists\n";
    } else {
        echo "   ❌ {$table} MISSING - {$description}\n";
        $missingTables[] = $table;
    }
}

if (!empty($missingTables)) {
    echo "\n   ⚠️  Missing tables detected!\n";
    echo "   Run: php artisan migrate\n\n";
} else {
    echo "   ✅ All required tables exist\n\n";
}

// Step 2: Setup Default Notification Settings
echo "2️⃣  Setting up default notification settings...\n";

if (Schema::hasTable('notification_settings')) {
    $users = User::all();
    $created = 0;

    foreach ($users as $user) {
        if (!$user->notificationSettings) {
            DB::table('notification_settings')->insert([
                'user_id' => $user->id,
                'complaint_created' => true,
                'complaint_status_changed' => true,
                'complaint_response' => true,
                'announcement_created' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $created++;
        }
    }

    echo "   ✅ Created {$created} default notification settings\n\n";
} else {
    echo "   ⚠️  notification_settings table not found\n\n";
}

// Step 3: Clear All Caches
echo "3️⃣  Clearing caches...\n";

try {
    Artisan::call('config:clear');
    echo "   ✅ Config cache cleared\n";

    Artisan::call('cache:clear');
    echo "   ✅ Application cache cleared\n";

    Artisan::call('event:clear');
    echo "   ✅ Event cache cleared\n";

    Artisan::call('view:clear');
    echo "   ✅ View cache cleared\n";

    echo "\n";
} catch (\Exception $e) {
    echo "   ❌ Error clearing caches: " . $e->getMessage() . "\n\n";
}

// Step 4: Verify Queue Configuration
echo "4️⃣  Verifying queue configuration...\n";

$queueConnection = config('queue.default');
echo "   Queue Driver: {$queueConnection}\n";

if ($queueConnection === 'sync') {
    echo "   ⚠️  WARNING: Queue is set to 'sync' (no background processing)\n";
    echo "   Recommended: Change QUEUE_CONNECTION=database in .env\n";
} else {
    echo "   ✅ Queue configured for background processing\n";
}
echo "\n";

// Step 5: Check .env File
echo "5️⃣  Checking .env configuration...\n";

$envChecks = [
    'QUEUE_CONNECTION' => env('QUEUE_CONNECTION'),
    'FIREBASE_CREDENTIALS' => env('FIREBASE_CREDENTIALS'),
];

foreach ($envChecks as $key => $value) {
    if ($value) {
        echo "   ✅ {$key} is set\n";
    } else {
        echo "   ⚠️  {$key} is NOT set\n";
    }
}
echo "\n";

// Step 6: Check Firebase Credentials File
echo "6️⃣  Checking Firebase credentials...\n";

$credentialsPath = env('FIREBASE_CREDENTIALS');
if ($credentialsPath) {
    $fullPath = storage_path('app/' . $credentialsPath);

    if (file_exists($fullPath) && !is_dir($fullPath)) {
        $size = filesize($fullPath);
        echo "   ✅ Credentials file found ({$size} bytes)\n";
        echo "   Location: {$fullPath}\n";
    } else {
        echo "   ❌ Credentials file NOT found\n";
        echo "   Expected location: {$fullPath}\n";
        echo "   Please upload your Firebase service account JSON file\n";
    }
} else {
    echo "   ⚠️  FIREBASE_CREDENTIALS not set in .env\n";
}
echo "\n";

// Summary
echo str_repeat("=", 50) . "\n";
echo "✅ Quick Fix Complete!\n\n";

echo "📝 What to do next:\n\n";

if (!empty($missingTables)) {
    echo "1. 🔴 CRITICAL: Run migrations\n";
    echo "   php artisan migrate\n\n";
}

if (!env('FIREBASE_CREDENTIALS')) {
    echo "2. 🔴 CRITICAL: Setup Firebase\n";
    echo "   - Download service account JSON from Firebase Console\n";
    echo "   - Save to storage/app/firebase-credentials.json\n";
    echo "   - Add to .env: FIREBASE_CREDENTIALS=firebase-credentials.json\n\n";
}

if ($queueConnection === 'sync') {
    echo "3. ⚠️  RECOMMENDED: Change queue driver\n";
    echo "   - Set QUEUE_CONNECTION=database in .env\n";
    echo "   - Run: php artisan queue:work\n\n";
}

echo "4. ✅ Test the system\n";
echo "   php test-notifications.php\n\n";

echo "5. 📚 Read troubleshooting guide\n";
echo "   docs/NOTIFICATION_TROUBLESHOOTING.md\n\n";
