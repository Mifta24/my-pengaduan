#!/usr/bin/env php
<?php

/**
 * Check Queue Status
 * 
 * Script untuk cek status queue jobs dan notifications
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n" . str_repeat("=", 60) . "\n";
echo "QUEUE STATUS CHECK\n";
echo str_repeat("=", 60) . "\n\n";

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✅ Database connected\n\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check queue configuration
echo "Queue Configuration:\n";
echo "  Driver: " . config('queue.default') . "\n";
echo "  Connection: " . config('queue.connections.database.connection') . "\n\n";

// Check pending jobs
$pendingJobs = DB::table('jobs')->count();
echo "Pending Jobs: " . $pendingJobs . "\n";

if ($pendingJobs > 0) {
    echo "⚠️  WARNING: Ada {$pendingJobs} jobs yang belum diproses!\n";
    echo "   Jalankan: php artisan queue:work\n\n";
    
    // Show last 5 pending jobs
    $jobs = DB::table('jobs')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Last 5 Pending Jobs:\n";
    foreach ($jobs as $job) {
        $payload = json_decode($job->payload, true);
        $jobName = $payload['displayName'] ?? 'Unknown';
        echo "  - {$jobName} (queued: {$job->created_at})\n";
    }
    echo "\n";
} else {
    echo "✅ No pending jobs\n\n";
}

// Check failed jobs
$failedJobs = DB::table('failed_jobs')->count();
echo "Failed Jobs: " . $failedJobs . "\n";

if ($failedJobs > 0) {
    echo "❌ WARNING: Ada {$failedJobs} jobs yang gagal!\n";
    echo "   Jalankan: php artisan queue:retry all\n\n";
    
    // Show last 5 failed jobs
    $failed = DB::table('failed_jobs')
        ->orderBy('failed_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Last 5 Failed Jobs:\n";
    foreach ($failed as $job) {
        $payload = json_decode($job->payload, true);
        $jobName = $payload['displayName'] ?? 'Unknown';
        echo "  - {$jobName}\n";
        echo "    Failed at: {$job->failed_at}\n";
        echo "    Exception: " . substr($job->exception, 0, 100) . "...\n\n";
    }
} else {
    echo "✅ No failed jobs\n\n";
}

// Check recent notifications
echo str_repeat("-", 60) . "\n";
echo "Recent FCM Notifications (last 10):\n";
echo str_repeat("-", 60) . "\n";

$notifications = DB::table('fcm_notifications')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($notifications->isEmpty()) {
    echo "No notifications found\n";
} else {
    foreach ($notifications as $notif) {
        $user = DB::table('users')->where('id', $notif->user_id)->first();
        $userName = $user ? $user->name : 'Unknown';
        
        echo "\n";
        echo "Type: {$notif->type}\n";
        echo "User: {$userName}\n";
        echo "Title: {$notif->title}\n";
        echo "Body: {$notif->body}\n";
        echo "Read: " . ($notif->is_read ? '✅' : '❌') . "\n";
        echo "Created: {$notif->created_at}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 60) . "\n";

if ($pendingJobs > 0) {
    echo "⚠️  Action Required: Start queue worker\n";
    echo "   Command: php artisan queue:work --verbose\n";
} elseif ($failedJobs > 0) {
    echo "⚠️  Action Required: Retry failed jobs\n";
    echo "   Command: php artisan queue:retry all\n";
} else {
    echo "✅ Queue system is healthy\n";
}

echo "\n";
