#!/usr/bin/env php
<?php

/**
 * Firebase FCM Notification Monitoring Tool
 *
 * Simple CLI tool untuk monitoring notifikasi FCM
 * Usage: php monitor-notifications.php [option]
 */

$command = $argv[1] ?? 'help';

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║        FCM NOTIFICATION MONITORING TOOL                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

switch ($command) {
    case 'logs':
        echo "📝 Recent Notification Logs (Last 20 lines):\n";
        echo "─────────────────────────────────────────────────────────────\n";
        $logs = shell_exec('tail -50 storage/logs/laravel.log | grep -i notification');
        if (empty(trim($logs))) {
            echo "⚠️  No notification logs found yet.\n";
            echo "   Try creating a complaint or announcement to trigger notifications.\n";
        } else {
            echo $logs;
        }
        break;

    case 'count':
        echo "📊 Notification Count (Today):\n";
        echo "─────────────────────────────────────────────────────────────\n";
        $today = date('Y-m-d');
        $logFile = "storage/logs/laravel-{$today}.log";

        if (!file_exists($logFile)) {
            $logFile = "storage/logs/laravel.log";
        }

        $total = shell_exec("grep -c -i 'notification sent' {$logFile} 2>/dev/null || echo 0");
        $complaint = shell_exec("grep -c 'Complaint notification' {$logFile} 2>/dev/null || echo 0");
        $status = shell_exec("grep -c 'Status change notification' {$logFile} 2>/dev/null || echo 0");
        $announcement = shell_exec("grep -c 'Announcement notification' {$logFile} 2>/dev/null || echo 0");

        echo "   Total Notifications: " . trim($total) . "\n";
        echo "   ├─ Complaint Created: " . trim($complaint) . "\n";
        echo "   ├─ Status Changed: " . trim($status) . "\n";
        echo "   └─ Announcements: " . trim($announcement) . "\n";
        break;

    case 'queue':
        echo "⚙️  Queue Status:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        system('php artisan queue:monitor 2>/dev/null || echo "Queue driver not configured"');
        break;

    case 'failed':
        echo "❌ Failed Notification Jobs:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        system('php artisan queue:failed');
        break;

    case 'watch':
        echo "👀 Watching notification logs (Ctrl+C to stop)...\n";
        echo "─────────────────────────────────────────────────────────────\n";
        passthru('tail -f storage/logs/laravel.log | grep -i --line-buffered notification');
        break;

    case 'test':
        echo "🧪 Testing Firebase Connection:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        system('php test-firebase.php');
        break;

    case 'stats':
        echo "📈 Notification Statistics:\n";
        echo "─────────────────────────────────────────────────────────────\n";

        require __DIR__.'/vendor/autoload.php';
        $app = require_once __DIR__.'/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        try {
            $devices = DB::table('user_devices')->count();
            $activeDevices = DB::table('user_devices')->where('is_active', true)->count();
            $androidDevices = DB::table('user_devices')->where('device_type', 'android')->where('is_active', true)->count();
            $iosDevices = DB::table('user_devices')->where('device_type', 'ios')->where('is_active', true)->count();
            $settings = DB::table('notification_settings')->count();

            echo "   📱 Total Devices: {$devices}\n";
            echo "      ├─ Active: {$activeDevices}\n";
            echo "      ├─ Android: {$androidDevices}\n";
            echo "      └─ iOS: {$iosDevices}\n";
            echo "\n";
            echo "   ⚙️  Notification Settings: {$settings} users configured\n";

            // Check recent device registrations
            $recentDevices = DB::table('user_devices')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            echo "   📊 New devices (last 7 days): {$recentDevices}\n";

        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
        break;

    case 'clear-logs':
        echo "🗑️  Clearing old logs...\n";
        $deleted = 0;
        $logDir = 'storage/logs/';
        $files = glob($logDir . 'laravel-*.log');
        $cutoffDate = date('Y-m-d', strtotime('-30 days'));

        foreach ($files as $file) {
            if (preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', basename($file), $matches)) {
                if ($matches[1] < $cutoffDate) {
                    unlink($file);
                    $deleted++;
                }
            }
        }

        echo "   ✅ Deleted {$deleted} old log files (older than 30 days)\n";
        break;

    case 'help':
    default:
        echo "Available Commands:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        echo "  📝 logs          - Show recent notification logs\n";
        echo "  📊 count         - Count notifications sent today\n";
        echo "  ⚙️  queue        - Check queue status\n";
        echo "  ❌ failed        - Show failed notification jobs\n";
        echo "  👀 watch         - Watch notification logs in real-time\n";
        echo "  🧪 test          - Test Firebase connection\n";
        echo "  📈 stats         - Show notification statistics\n";
        echo "  🗑️  clear-logs   - Clear old log files (>30 days)\n";
        echo "  ❓ help          - Show this help message\n";
        echo "\n";
        echo "Examples:\n";
        echo "  php monitor-notifications.php logs\n";
        echo "  php monitor-notifications.php watch\n";
        echo "  php monitor-notifications.php stats\n";
        break;
}

echo "\n";
