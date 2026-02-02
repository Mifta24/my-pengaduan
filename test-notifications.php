#!/usr/bin/env php
<?php

/**
 * Test Notification System
 *
 * Script untuk test notifikasi Firebase
 * Usage: php test-notifications.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Complaint;
use App\Models\Announcement;
use App\Events\ComplaintCreated;
use App\Events\ComplaintStatusChanged;
use App\Events\AnnouncementCreated;
use App\Services\FirebaseService;

echo "🔔 Testing Notification System\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Firebase Service
echo "1️⃣  Testing Firebase Service Configuration...\n";
$firebaseService = app(FirebaseService::class);
if ($firebaseService->isConfigured()) {
    echo "   ✅ Firebase is configured\n\n";
} else {
    echo "   ❌ Firebase NOT configured - Check FIREBASE_CREDENTIALS in .env\n\n";
}

// Test 2: Check Admin Users
echo "2️⃣  Checking Admin Users...\n";
$admins = User::role('admin')->get();
echo "   Found {$admins->count()} admin(s)\n";
foreach ($admins as $admin) {
    $deviceCount = $admin->devices()->where('is_active', true)->count();
    echo "   - {$admin->name} ({$admin->email}) - {$deviceCount} active device(s)\n";
}
echo "\n";

// Test 3: Check Regular Users
echo "3️⃣  Checking Regular Users...\n";
$users = User::role('user')->get();
echo "   Found {$users->count()} user(s)\n";
$usersWithDevices = 0;
foreach ($users as $user) {
    $deviceCount = $user->devices()->where('is_active', true)->count();
    if ($deviceCount > 0) {
        $usersWithDevices++;
        echo "   - {$user->name} ({$user->email}) - {$deviceCount} active device(s)\n";
    }
}
echo "   {$usersWithDevices} user(s) have active devices\n\n";

// Test 4: Check Queue Configuration
echo "4️⃣  Checking Queue Configuration...\n";
$queueConnection = config('queue.default');
echo "   Queue Connection: {$queueConnection}\n";

if ($queueConnection === 'database') {
    $pendingJobs = DB::table('jobs')->count();
    $failedJobs = DB::table('failed_jobs')->count();
    echo "   Pending Jobs: {$pendingJobs}\n";
    echo "   Failed Jobs: {$failedJobs}\n";

    if ($pendingJobs > 0) {
        echo "   ⚠️  You have pending jobs. Run: php artisan queue:work\n";
    }
    if ($failedJobs > 0) {
        echo "   ⚠️  You have failed jobs. Check: php artisan queue:failed\n";
    }
}
echo "\n";

// Test 5: Check Event Service Provider
echo "5️⃣  Checking Event Listeners...\n";
$listeners = app('events')->getListeners(ComplaintCreated::class);
echo "   ComplaintCreated listeners: " . count($listeners) . "\n";

$listeners = app('events')->getListeners(ComplaintStatusChanged::class);
echo "   ComplaintStatusChanged listeners: " . count($listeners) . "\n";

$listeners = app('events')->getListeners(AnnouncementCreated::class);
echo "   AnnouncementCreated listeners: " . count($listeners) . "\n";
echo "\n";

// Test 6: Recent Notifications
echo "6️⃣  Checking Recent Notifications (last 24h)...\n";
$recentNotifications = DB::table('fcm_notifications')
    ->where('created_at', '>', now()->subDay())
    ->groupBy('type')
    ->select('type', DB::raw('COUNT(*) as count'))
    ->get();

if ($recentNotifications->isEmpty()) {
    echo "   ⚠️  No notifications sent in last 24 hours\n";
} else {
    foreach ($recentNotifications as $notif) {
        echo "   - {$notif->type}: {$notif->count} notification(s)\n";
    }
}
echo "\n";

// Test 7: Send Test Notification
echo "7️⃣  Send Test Notification?\n";
echo "   This will dispatch a test ComplaintCreated event\n";
echo "   Type 'yes' to continue: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'yes') {
    // Find first user with device
    $user = User::role('user')
        ->whereHas('devices', function($q) {
            $q->where('is_active', true);
        })
        ->first();

    if (!$user) {
        echo "   ❌ No user with active device found. Cannot send test.\n";
    } else {
        // Create test complaint
        $complaint = new Complaint([
            'user_id' => $user->id,
            'category_id' => 1,
            'title' => 'Test Notification - ' . now()->format('H:i:s'),
            'description' => 'This is a test notification',
            'location' => 'Test Location',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        // Don't save to DB, just dispatch event
        echo "   📤 Dispatching ComplaintCreated event...\n";
        event(new ComplaintCreated($complaint));

        echo "   ✅ Event dispatched!\n";
        echo "   Check queue: php artisan queue:work -v\n";
        echo "   Check logs: tail -f storage/logs/laravel.log\n";
    }
} else {
    echo "   Skipped.\n";
}
echo "\n";

// Summary
echo str_repeat("=", 50) . "\n";
echo "✅ Test Complete!\n\n";

echo "📝 Next Steps:\n";
echo "   1. Make sure Firebase credentials are configured\n";
echo "   2. Ensure users/admins have registered device tokens\n";
echo "   3. Run queue worker: php artisan queue:work -v\n";
echo "   4. Monitor logs: tail -f storage/logs/laravel.log\n";
echo "   5. Test via API endpoints\n\n";

echo "📚 Documentation: docs/NOTIFICATION_TROUBLESHOOTING.md\n";
