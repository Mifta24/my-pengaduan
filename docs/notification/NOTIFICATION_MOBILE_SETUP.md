# 🚀 Implementasi Notifikasi FCM - Mobile Only (Laravel API)

## 📋 Overview

Sistem notifikasi push untuk **aplikasi mobile** (Android/iOS) menggunakan Firebase Cloud Messaging, dengan Laravel sebagai backend API.

**Keuntungan Mobile-Only:**
- ✅ Lebih sederhana (tidak perlu Service Worker)
- ✅ Lebih reliable (native push notification)
- ✅ Battery efficient
- ✅ Better user experience
- ✅ Faster implementation (~2 hari vs 5 hari)

---

## 🎯 Langkah Awal - Step by Step

### **PHASE 1: Setup Firebase (30 menit)**

#### Step 1.1: Create Firebase Project
1. Buka [Firebase Console](https://console.firebase.google.com)
2. Klik **"Add project"**
3. Nama project: `MyPengaduan`
4. Enable Google Analytics (optional)
5. Klik **"Create project"**

#### Step 1.2: Add Android App (if needed)
1. Klik icon Android di project overview
2. Package name: `com.yourcompany.mypengaduan` (sesuaikan dengan mobile app Anda)
3. Download `google-services.json`
4. **SIMPAN** file ini (nanti kasih ke mobile developer)

#### Step 1.3: Add iOS App (if needed)
1. Klik icon iOS di project overview
2. Bundle ID: `com.yourcompany.mypengaduan`
3. Download `GoogleService-Info.plist`
4. **SIMPAN** file ini (nanti kasih ke mobile developer)

#### Step 1.4: Enable Cloud Messaging
1. Di sidebar klik **"Build"** → **"Cloud Messaging"**
2. Tab **"Cloud Messaging API (Legacy)"** → Enable API
3. **COPY** Server Key (ini yang penting!)
4. **SIMPAN** di notes Anda

#### Step 1.5: Download Service Account
1. Klik ⚙️ (Settings) → **"Project settings"**
2. Tab **"Service accounts"**
3. Klik **"Generate new private key"**
4. Download file JSON
5. Rename menjadi `firebase-credentials.json`

---

### **PHASE 2: Setup Laravel Backend (45 menit)**

#### Step 2.1: Install Firebase Package
```bash
composer require kreait/laravel-firebase
```

#### Step 2.2: Publish Config
```bash
php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider" --tag=config
```

#### Step 2.3: Store Credentials
```bash
# Create directory
mkdir -p storage/app/firebase

# Copy file yang sudah di-download
# Move firebase-credentials.json ke storage/app/firebase/
```

#### Step 2.4: Update .env
```env
# Add these lines to .env
FIREBASE_PROJECT_ID=mypengaduan
FIREBASE_CREDENTIALS=firebase/firebase-credentials.json
```

#### Step 2.5: Create Database Tables
```bash
# Create migrations
php artisan make:migration create_user_devices_table
php artisan make:migration create_notification_settings_table
```

**Migration 1: user_devices**
```php
// database/migrations/xxxx_create_user_devices_table.php
public function up()
{
    Schema::create('user_devices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('device_token', 500)->unique();
        $table->enum('device_type', ['android', 'ios'])->default('android');
        $table->string('device_model')->nullable();
        $table->string('os_version')->nullable();
        $table->string('app_version')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamp('last_used_at')->nullable();
        $table->timestamps();
        
        $table->index(['user_id', 'is_active']);
    });
}
```

**Migration 2: notification_settings**
```php
// database/migrations/xxxx_create_notification_settings_table.php
public function up()
{
    Schema::create('notification_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->boolean('complaint_created')->default(true);
        $table->boolean('complaint_status_changed')->default(true);
        $table->boolean('announcement_created')->default(true);
        $table->boolean('admin_response')->default(true);
        $table->boolean('comment_added')->default(true);
        $table->boolean('push_enabled')->default(true);
        $table->timestamps();
        
        $table->unique('user_id');
    });
}
```

**Run migrations:**
```bash
php artisan migrate
```

---

### **PHASE 3: Create Models (15 menit)**

#### Step 3.1: Create UserDevice Model
```bash
php artisan make:model UserDevice
```

```php
// app/Models/UserDevice.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'device_model',
        'os_version',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAndroid($query)
    {
        return $query->where('device_type', 'android');
    }

    public function scopeIos($query)
    {
        return $query->where('device_type', 'ios');
    }
}
```

#### Step 3.2: Create NotificationSetting Model
```bash
php artisan make:model NotificationSetting
```

```php
// app/Models/NotificationSetting.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'complaint_created',
        'complaint_status_changed',
        'announcement_created',
        'admin_response',
        'comment_added',
        'push_enabled',
    ];

    protected $casts = [
        'complaint_created' => 'boolean',
        'complaint_status_changed' => 'boolean',
        'announcement_created' => 'boolean',
        'admin_response' => 'boolean',
        'comment_added' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Step 3.3: Update User Model
```php
// app/Models/User.php

// Add these methods to User model
public function devices()
{
    return $this->hasMany(UserDevice::class);
}

public function notificationSettings()
{
    return $this->hasOne(NotificationSetting::class);
}

public function getActiveDeviceTokens()
{
    return $this->devices()
        ->where('is_active', true)
        ->pluck('device_token')
        ->toArray();
}
```

---

### **PHASE 4: Create Firebase Service (30 menit)**

#### Step 4.1: Create Service Class
```bash
mkdir -p app/Services
```

```php
// app/Services/FirebaseService.php
<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/' . config('firebase.credentials.file')));
        
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $result = $this->messaging->send($message);
            
            Log::info('FCM notification sent', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage(), [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
            
            // If token is invalid, mark device as inactive
            if (strpos($e->getMessage(), 'not-found') !== false || 
                strpos($e->getMessage(), 'invalid-registration-token') !== false) {
                $this->markTokenAsInactive($token);
            }
            
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return false;
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $result = $this->messaging->sendMulticast($message, $tokens);
            
            Log::info('FCM multicast sent', [
                'token_count' => count($tokens),
                'success_count' => $result->successes()->count(),
                'failure_count' => $result->failures()->count(),
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Multicast Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send to topic (broadcast to all subscribed users)
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            return $this->messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Topic Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe devices to topic
     */
    public function subscribeToTopic(array $tokens, string $topic)
    {
        try {
            return $this->messaging->subscribeToTopic($topic, $tokens);
        } catch (\Exception $e) {
            Log::error('FCM Subscribe Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark device token as inactive
     */
    protected function markTokenAsInactive(string $token)
    {
        \App\Models\UserDevice::where('device_token', $token)
            ->update(['is_active' => false]);
    }
}
```

---

### **PHASE 5: Create API Controllers (30 menit)**

#### Step 5.1: Device Token Controller
```bash
php artisan make:controller Api/DeviceTokenController
```

```php
// app/Http/Controllers/Api/DeviceTokenController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    /**
     * Register device token (dipanggil dari mobile app)
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
            'device_model' => 'nullable|string',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Update or create device
        $device = UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_token' => $request->device_token,
            ],
            [
                'device_type' => $request->device_type,
                'device_model' => $request->device_model,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully',
            'data' => $device,
        ]);
    }

    /**
     * Get user devices
     */
    public function index()
    {
        $devices = Auth::user()->devices()
            ->orderBy('last_used_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Delete device token
     */
    public function destroy($id)
    {
        $device = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device removed successfully',
        ]);
    }
}
```

#### Step 5.2: Notification Controller
```bash
php artisan make:controller Api/NotificationController
```

```php
// app/Http/Controllers/Api/NotificationController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications (from database)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->when($request->unread, function ($query) {
                $query->whereNull('read_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Get notification settings
     */
    public function getSettings()
    {
        $settings = Auth::user()->notificationSettings;
        
        if (!$settings) {
            $settings = NotificationSetting::create([
                'user_id' => Auth::id(),
                'complaint_created' => true,
                'complaint_status_changed' => true,
                'announcement_created' => true,
                'admin_response' => true,
                'comment_added' => true,
                'push_enabled' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'complaint_created' => 'boolean',
            'complaint_status_changed' => 'boolean',
            'announcement_created' => 'boolean',
            'admin_response' => 'boolean',
            'comment_added' => 'boolean',
            'push_enabled' => 'boolean',
        ]);

        $settings = Auth::user()->notificationSettings;
        
        if (!$settings) {
            $settings = new NotificationSetting(['user_id' => Auth::id()]);
        }

        $settings->fill($request->all());
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated',
            'data' => $settings,
        ]);
    }
}
```

---

### **PHASE 6: Add API Routes (5 menit)**

```php
// routes/api.php

use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;

Route::middleware('auth:sanctum')->group(function () {
    // Device tokens
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::get('/device-tokens', [DeviceTokenController::class, 'index']);
    Route::delete('/device-tokens/{id}', [DeviceTokenController::class, 'destroy']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    
    // Notification settings
    Route::get('/notification-settings', [NotificationController::class, 'getSettings']);
    Route::put('/notification-settings', [NotificationController::class, 'updateSettings']);
});
```

---

### **PHASE 7: Test API (15 menit)**

#### Test 1: Register Device Token
```bash
curl -X POST http://localhost/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "FIREBASE_TOKEN_FROM_MOBILE",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "Android 13",
    "app_version": "1.0.0"
  }'
```

#### Test 2: Send Test Notification
```php
// Create temporary test route
// routes/web.php
Route::get('/test-fcm', function () {
    $firebaseService = app(\App\Services\FirebaseService::class);
    
    $token = 'DEVICE_TOKEN_FROM_DATABASE';
    $title = '🔔 Test Notification';
    $body = 'Notifikasi berhasil dikirim dari Laravel!';
    
    $result = $firebaseService->sendToDevice($token, $title, $body);
    
    return response()->json([
        'success' => true,
        'message' => 'Notification sent',
        'result' => $result
    ]);
})->middleware('auth');
```

---

## 📱 Untuk Mobile Developer (Info yang Perlu Diberikan)

### File yang Perlu Dikirim ke Mobile Developer:

1. **google-services.json** (Android)
2. **GoogleService-Info.plist** (iOS)
3. **Server Key** (untuk testing)

### API Endpoints yang Perlu Diintegrasikan:

```
POST   /api/device-tokens          - Register FCM token
GET    /api/device-tokens          - Get registered devices
DELETE /api/device-tokens/{id}     - Remove device
GET    /api/notifications          - Get notification history
POST   /api/notifications/{id}/read - Mark as read
GET    /api/notification-settings  - Get settings
PUT    /api/notification-settings  - Update settings
```

### Notification Payload Format:

```json
{
  "notification": {
    "title": "Judul notifikasi",
    "body": "Isi notifikasi"
  },
  "data": {
    "type": "complaint_created",
    "complaint_id": "123",
    "url": "/complaints/123"
  }
}
```

---

## ✅ Checklist Langkah Awal (2 Jam Pertama)

- [ ] **Firebase Setup (30 menit)**
  - [ ] Create Firebase project
  - [ ] Add Android/iOS app
  - [ ] Enable Cloud Messaging
  - [ ] Download credentials

- [ ] **Laravel Setup (45 menit)**
  - [ ] Install kreait/laravel-firebase
  - [ ] Store credentials
  - [ ] Update .env
  - [ ] Create migrations
  - [ ] Run migrations

- [ ] **Models (15 menit)**
  - [ ] Create UserDevice model
  - [ ] Create NotificationSetting model
  - [ ] Update User model

- [ ] **Services & Controllers (30 menit)**
  - [ ] Create FirebaseService
  - [ ] Create DeviceTokenController
  - [ ] Create NotificationController
  - [ ] Add API routes

**Total: ~2 jam untuk setup dasar!**

---

## 🎯 Next Steps Setelah Setup

### Tomorrow (Day 2):
1. Create Events & Listeners
2. Trigger notifications from controllers
3. Test all notification types

### Day After (Day 3):
1. Coordinate with mobile developer
2. Test end-to-end
3. Deploy to staging

---

## 📞 Support

**Firebase Console:** https://console.firebase.google.com  
**Kreait Docs:** https://firebase-php.readthedocs.io  
**Laravel Notifications:** https://laravel.com/docs/notifications

---

**Siap mulai?** Follow checklist di atas step-by-step! 🚀
