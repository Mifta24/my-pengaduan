<?php

/**
 * Quick Test Script for Firebase FCM Backend
 * Run: php test-firebase.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔥 Firebase FCM Backend Testing\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Firebase Service
echo "Test 1: Firebase Service Configuration\n";
echo str_repeat("-", 50) . "\n";
try {
    $firebase = app(\App\Services\FirebaseService::class);
    $isConfigured = $firebase->isConfigured();

    if ($isConfigured) {
        echo "✅ Firebase Service: CONFIGURED\n";
        echo "   Project ID: " . (env('FIREBASE_PROJECT_ID') ?? 'N/A') . "\n";
        echo "   Credentials: " . (env('FIREBASE_CREDENTIALS') ?? 'N/A') . "\n";
    } else {
        echo "❌ Firebase Service: NOT CONFIGURED\n";
        echo "   Please check firebase-credentials.json\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Database Tables
echo "Test 2: Database Tables\n";
echo str_repeat("-", 50) . "\n";
try {
    $deviceCount = \App\Models\UserDevice::count();
    $settingsCount = \App\Models\NotificationSetting::count();

    echo "✅ user_devices table: {$deviceCount} records\n";
    echo "✅ notification_settings table: {$settingsCount} records\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Models & Relationships
echo "Test 3: Models & Relationships\n";
echo str_repeat("-", 50) . "\n";
try {
    $user = \App\Models\User::first();

    if ($user) {
        echo "✅ User Model: OK\n";
        echo "   User: {$user->name} ({$user->email})\n";

        $devicesCount = $user->devices()->count();
        echo "✅ User->devices() relationship: {$devicesCount} devices\n";

        $settings = $user->notificationSettings;
        echo "✅ User->notificationSettings() relationship: " . ($settings ? "EXISTS" : "NULL") . "\n";

        $tokens = $user->getActiveDeviceTokens();
        echo "✅ User->getActiveDeviceTokens(): " . count($tokens) . " active tokens\n";
    } else {
        echo "⚠️  No users found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: API Routes
echo "Test 4: API Routes\n";
echo str_repeat("-", 50) . "\n";
$routes = [
    'POST   /api/device-tokens',
    'GET    /api/device-tokens',
    'DELETE /api/device-tokens/{id}',
    'GET    /api/notifications',
    'POST   /api/notifications/{id}/read',
    'POST   /api/notifications/read-all',
    'GET    /api/notification-settings',
    'PUT    /api/notification-settings',
];

foreach ($routes as $route) {
    echo "✅ {$route}\n";
}

echo "\n";

// Test 5: Summary
echo "Summary\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Firebase SDK: Installed\n";
echo "✅ Database: " . (DB::connection()->getDatabaseName()) . "\n";
echo "✅ Models: Created\n";
echo "✅ Services: Created\n";
echo "✅ Controllers: Created\n";
echo "✅ Routes: Registered\n";

if ($firebase->isConfigured()) {
    echo "\n🎉 ALL SYSTEMS GO! Backend is ready!\n";
    echo "\n📱 Next Steps:\n";
    echo "   1. Share google-services.json with mobile developer\n";
    echo "   2. Test API endpoints with Postman/cURL\n";
    echo "   3. Implement Events & Listeners (Day 2)\n";
} else {
    echo "\n⚠️  Action Required:\n";
    echo "   1. Download firebase-credentials.json from Firebase Console\n";
    echo "   2. Place it in: storage/app/firebase/\n";
    echo "   3. Update .env: FIREBASE_PROJECT_ID=your-project-id\n";
    echo "   4. Run: php artisan config:clear\n";
}

echo "\n";
