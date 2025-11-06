# 🔔 Rencana Sistem Notifikasi Real-time dengan Firebase FCM

## 📋 Overview

Sistem notifikasi push real-time menggunakan **Firebase Cloud Messaging (FCM)** terintegrasi dengan Laravel API untuk memberikan notifikasi instant kepada user dan admin.

---

## 🎯 Fitur Notifikasi yang Akan Diimplementasikan

### **1. Notifikasi untuk Admin**
- ✅ **Keluhan Baru Dibuat** - User membuat keluhan baru
- ✅ **Komentar Baru pada Keluhan** - User menambahkan komentar
- ✅ **User Baru Registrasi** - Pendaftaran user baru (opsional)

### **2. Notifikasi untuk User**
- ✅ **Status Keluhan Berubah** - Pending → In Progress → Resolved/Rejected
- ✅ **Admin Memberi Response** - Admin menambahkan response/komentar
- ✅ **Pengumuman Baru** - Admin publish pengumuman baru
- ✅ **Keluhan Disetujui/Ditolak** - Status final keluhan

### **3. Notifikasi untuk Semua (Broadcast)**
- ✅ **Pengumuman Penting** - Admin broadcast ke semua user
- ✅ **System Maintenance** - Pemberitahuan maintenance

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                     NOTIFICATION FLOW                        │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│              │         │              │         │              │
│   User App   │────────▶│ Laravel API  │────────▶│   Firebase   │
│   (Web/PWA)  │         │   Backend    │         │     FCM      │
│              │         │              │         │              │
└──────────────┘         └──────────────┘         └──────────────┘
       │                        │                        │
       │                        │                        │
       └────────────────────────┴────────────────────────┘
                                │
                                ▼
                    Push Notification to Device
```

---

## 📦 Komponen yang Dibutuhkan

### **A. Backend (Laravel)**

#### 1. **Database Tables**
```sql
-- Table: user_devices (FCM tokens)
- id
- user_id
- device_token (FCM token)
- device_type (web/android/ios)
- device_name
- is_active
- last_used_at
- created_at
- updated_at

-- Table: notifications
- id
- notifiable_type (App\Models\User)
- notifiable_id
- type (complaint_created, status_changed, etc)
- data (JSON)
- read_at
- created_at
- updated_at

-- Table: notification_settings (per user)
- id
- user_id
- complaint_created (boolean)
- complaint_status_changed (boolean)
- announcement_created (boolean)
- admin_response (boolean)
- email_notifications (boolean)
- created_at
- updated_at
```

#### 2. **Laravel Packages**
```bash
composer require kreait/laravel-firebase
composer require laravel/sanctum (sudah ada)
```

#### 3. **Models Baru**
- `app/Models/UserDevice.php`
- `app/Models/NotificationSetting.php`

#### 4. **Controllers Baru**
- `app/Http/Controllers/Api/NotificationController.php`
- `app/Http/Controllers/Api/DeviceTokenController.php`

#### 5. **Services**
- `app/Services/FirebaseService.php`
- `app/Services/NotificationService.php`

#### 6. **Events & Listeners**
- `app/Events/ComplaintCreated.php`
- `app/Events/ComplaintStatusChanged.php`
- `app/Events/AnnouncementCreated.php`
- `app/Listeners/SendComplaintNotification.php`

#### 7. **Notifications**
- `app/Notifications/ComplaintCreatedNotification.php`
- `app/Notifications/ComplaintStatusChangedNotification.php`
- `app/Notifications/AnnouncementCreatedNotification.php`
- `app/Notifications/AdminResponseNotification.php`

---

### **B. Frontend (JavaScript/PWA)**

#### 1. **Firebase SDK**
```javascript
// Firebase initialization
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';
```

#### 2. **Service Worker**
```javascript
// public/firebase-messaging-sw.js
// Handle background notifications
```

#### 3. **JavaScript Files**
```javascript
// resources/js/firebase-init.js
// resources/js/notification-handler.js
// resources/js/notification-ui.js
```

#### 4. **UI Components**
- Bell icon dengan badge count
- Notification dropdown
- Toast notifications
- Notification settings page

---

## 🔧 Implementasi Step-by-Step

### **Phase 1: Setup Firebase (30 menit)**

#### Step 1.1: Create Firebase Project
1. Buka [Firebase Console](https://console.firebase.google.com)
2. Create new project: "MyPengaduan"
3. Enable Cloud Messaging
4. Generate Web credentials (Web Push certificates)
5. Download `firebase-credentials.json`

#### Step 1.2: Install Laravel Firebase
```bash
composer require kreait/laravel-firebase
php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider" --tag=config
```

#### Step 1.3: Configure Firebase
```php
// config/firebase.php
return [
    'credentials' => [
        'file' => storage_path('app/firebase/firebase-credentials.json'),
    ],
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
];
```

```env
// .env
FIREBASE_DATABASE_URL=https://mypengaduan-default-rtdb.firebaseio.com
FIREBASE_PROJECT_ID=mypengaduan
FIREBASE_SERVER_KEY=your_server_key_here
```

---

### **Phase 2: Database Setup (20 menit)**

#### Step 2.1: Create Migrations
```bash
php artisan make:migration create_user_devices_table
php artisan make:migration create_notification_settings_table
php artisan make:migration add_fcm_token_to_users_table
```

#### Step 2.2: Migration Content

**user_devices migration:**
```php
Schema::create('user_devices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('device_token', 500)->unique();
    $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
    $table->string('device_name')->nullable();
    $table->string('browser')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_used_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'is_active']);
});
```

**notification_settings migration:**
```php
Schema::create('notification_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->boolean('complaint_created')->default(true);
    $table->boolean('complaint_status_changed')->default(true);
    $table->boolean('announcement_created')->default(true);
    $table->boolean('admin_response')->default(true);
    $table->boolean('comment_added')->default(true);
    $table->boolean('email_notifications')->default(false);
    $table->boolean('push_notifications')->default(true);
    $table->timestamps();
    
    $table->unique('user_id');
});
```

#### Step 2.3: Run Migrations
```bash
php artisan migrate
```

---

### **Phase 3: Backend Models (15 menit)**

#### Step 3.1: UserDevice Model
```php
// app/Models/UserDevice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'device_name',
        'browser',
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

    public function scopeWeb($query)
    {
        return $query->where('device_type', 'web');
    }
}
```

#### Step 3.2: NotificationSetting Model
```php
// app/Models/NotificationSetting.php
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
        'email_notifications',
        'push_notifications',
    ];

    protected $casts = [
        'complaint_created' => 'boolean',
        'complaint_status_changed' => 'boolean',
        'announcement_created' => 'boolean',
        'admin_response' => 'boolean',
        'comment_added' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
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
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    // Add relationships
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
    
    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }
    
    // Helper method
    public function getActiveDeviceTokens()
    {
        return $this->devices()
            ->where('is_active', true)
            ->pluck('device_token')
            ->toArray();
    }
}
```

---

### **Phase 4: Firebase Service (30 menit)**

#### Step 4.1: Create FirebaseService
```php
// app/Services/FirebaseService.php
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(
            storage_path('app/firebase/firebase-credentials.json')
        );
        
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $token, array $notification, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(
                    FirebaseNotification::create(
                        $notification['title'],
                        $notification['body']
                    )
                    ->withImageUrl($notification['image'] ?? null)
                )
                ->withData($data);

            return $this->messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, array $notification, array $data = [])
    {
        try {
            $message = CloudMessage::new()
                ->withNotification(
                    FirebaseNotification::create(
                        $notification['title'],
                        $notification['body']
                    )
                    ->withImageUrl($notification['image'] ?? null)
                )
                ->withData($data);

            return $this->messaging->sendMulticast($message, $tokens);
        } catch (\Exception $e) {
            Log::error('FCM Multicast Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send to topic (broadcast)
     */
    public function sendToTopic(string $topic, array $notification, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(
                    FirebaseNotification::create(
                        $notification['title'],
                        $notification['body']
                    )
                )
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
        return $this->messaging->subscribeToTopic($topic, $tokens);
    }
}
```

#### Step 4.2: Create NotificationService
```php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to user
     */
    public function notifyUser(User $user, string $type, array $data)
    {
        // Check user notification settings
        $settings = $user->notificationSettings;
        if (!$settings || !$settings->push_notifications) {
            return false;
        }

        // Check if specific notification type is enabled
        if (!$this->isNotificationTypeEnabled($settings, $type)) {
            return false;
        }

        // Get active device tokens
        $tokens = $user->getActiveDeviceTokens();
        if (empty($tokens)) {
            return false;
        }

        // Prepare notification
        $notification = $this->prepareNotification($type, $data);

        // Send to all user devices
        return $this->firebaseService->sendToMultipleDevices(
            $tokens,
            $notification,
            $data
        );
    }

    /**
     * Send notification to all admins
     */
    public function notifyAdmins(string $type, array $data)
    {
        $admins = User::role('admin')->get();
        
        foreach ($admins as $admin) {
            $this->notifyUser($admin, $type, $data);
        }
    }

    /**
     * Broadcast to all users
     */
    public function broadcastToAll(array $notification, array $data = [])
    {
        return $this->firebaseService->sendToTopic('all_users', $notification, $data);
    }

    /**
     * Prepare notification content based on type
     */
    protected function prepareNotification(string $type, array $data): array
    {
        return match($type) {
            'complaint_created' => [
                'title' => '🆕 Keluhan Baru',
                'body' => "Keluhan baru dari {$data['user_name']}: {$data['title']}",
                'image' => null,
            ],
            'complaint_status_changed' => [
                'title' => '📊 Status Keluhan Berubah',
                'body' => "Keluhan Anda \"{$data['title']}\" statusnya berubah menjadi {$data['status']}",
                'image' => null,
            ],
            'announcement_created' => [
                'title' => '📢 Pengumuman Baru',
                'body' => $data['title'],
                'image' => $data['image'] ?? null,
            ],
            'admin_response' => [
                'title' => '💬 Response dari Admin',
                'body' => "Admin merespon keluhan Anda: \"{$data['title']}\"",
                'image' => null,
            ],
            'comment_added' => [
                'title' => '💭 Komentar Baru',
                'body' => "{$data['user_name']} menambahkan komentar pada keluhan Anda",
                'image' => null,
            ],
            default => [
                'title' => 'Notifikasi',
                'body' => $data['message'] ?? 'Anda memiliki notifikasi baru',
                'image' => null,
            ],
        };
    }

    /**
     * Check if notification type is enabled
     */
    protected function isNotificationTypeEnabled($settings, string $type): bool
    {
        $mapping = [
            'complaint_created' => 'complaint_created',
            'complaint_status_changed' => 'complaint_status_changed',
            'announcement_created' => 'announcement_created',
            'admin_response' => 'admin_response',
            'comment_added' => 'comment_added',
        ];

        $settingKey = $mapping[$type] ?? null;
        return $settingKey ? $settings->$settingKey : true;
    }
}
```

---

### **Phase 5: API Controllers (45 menit)**

#### Step 5.1: DeviceTokenController
```php
// app/Http/Controllers/Api/DeviceTokenController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    /**
     * Register device token
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'device_name' => 'nullable|string',
            'browser' => 'nullable|string',
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
                'device_name' => $request->device_name,
                'browser' => $request->browser,
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

    /**
     * Toggle device status
     */
    public function toggle($id)
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

        $device->is_active = !$device->is_active;
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Device status updated',
            'data' => $device,
        ]);
    }
}
```

#### Step 5.2: NotificationController
```php
// app/Http/Controllers/Api/NotificationController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications
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
     * Mark all as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get notification settings
     */
    public function getSettings()
    {
        $settings = Auth::user()->notificationSettings ?? new NotificationSetting([
            'user_id' => Auth::id(),
            'complaint_created' => true,
            'complaint_status_changed' => true,
            'announcement_created' => true,
            'admin_response' => true,
            'comment_added' => true,
            'email_notifications' => false,
            'push_notifications' => true,
        ]);

        if (!$settings->exists) {
            $settings->save();
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
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
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

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared',
        ]);
    }
}
```

---

### **Phase 6: Events & Listeners (30 menit)**

#### Step 6.1: Create Events
```bash
php artisan make:event ComplaintCreated
php artisan make:event ComplaintStatusChanged
php artisan make:event AnnouncementCreated
php artisan make:event AdminResponseAdded
```

#### Step 6.2: ComplaintCreated Event
```php
// app/Events/ComplaintCreated.php
namespace App\Events;

use App\Models\Complaint;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintCreated
{
    use Dispatchable, SerializesModels;

    public $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }
}
```

#### Step 6.3: ComplaintStatusChanged Event
```php
// app/Events/ComplaintStatusChanged.php
namespace App\Events;

use App\Models\Complaint;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusChanged
{
    use Dispatchable, SerializesModels;

    public $complaint;
    public $oldStatus;
    public $newStatus;

    public function __construct(Complaint $complaint, string $oldStatus, string $newStatus)
    {
        $this->complaint = $complaint;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
```

#### Step 6.4: Create Listeners
```bash
php artisan make:listener SendComplaintCreatedNotification --event=ComplaintCreated
php artisan make:listener SendComplaintStatusNotification --event=ComplaintStatusChanged
php artisan make:listener SendAnnouncementNotification --event=AnnouncementCreated
```

#### Step 6.5: Listener Implementation
```php
// app/Listeners/SendComplaintCreatedNotification.php
namespace App\Listeners;

use App\Events\ComplaintCreated;
use App\Services\NotificationService;

class SendComplaintCreatedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ComplaintCreated $event)
    {
        $complaint = $event->complaint;

        // Notify all admins
        $this->notificationService->notifyAdmins('complaint_created', [
            'complaint_id' => $complaint->id,
            'user_name' => $complaint->user->name,
            'title' => $complaint->title,
            'category' => $complaint->category->name ?? 'Uncategorized',
            'url' => route('admin.complaints.show', $complaint->id),
        ]);
    }
}
```

#### Step 6.6: Register Events
```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    ComplaintCreated::class => [
        SendComplaintCreatedNotification::class,
    ],
    ComplaintStatusChanged::class => [
        SendComplaintStatusNotification::class,
    ],
    AnnouncementCreated::class => [
        SendAnnouncementNotification::class,
    ],
];
```

---

### **Phase 7: Update Controllers (Trigger Events)**

#### Update ComplaintController
```php
// app/Http/Controllers/Admin/ComplaintController.php

use App\Events\ComplaintCreated;
use App\Events\ComplaintStatusChanged;

// In store method
public function store(Request $request)
{
    // ... validation and save complaint
    
    event(new ComplaintCreated($complaint));
    
    return redirect()->route('complaints.show', $complaint);
}

// In updateStatus method
public function updateStatus(Request $request, Complaint $complaint)
{
    $oldStatus = $complaint->status;
    $complaint->status = $request->status;
    $complaint->save();
    
    event(new ComplaintStatusChanged($complaint, $oldStatus, $request->status));
    
    return back()->with('success', 'Status updated');
}
```

---

### **Phase 8: Frontend Setup (60 menit)**

#### Step 8.1: Install Firebase SDK
```bash
npm install firebase
```

#### Step 8.2: Firebase Config
```javascript
// resources/js/firebase-config.js
export const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "mypengaduan.firebaseapp.com",
    projectId: "mypengaduan",
    storageBucket: "mypengaduan.appspot.com",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
};
```

#### Step 8.3: Firebase Init
```javascript
// resources/js/firebase-init.js
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';
import { firebaseConfig } from './firebase-config';

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request permission and get token
export async function requestNotificationPermission() {
    try {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            
            // Get FCM token
            const token = await getToken(messaging, {
                vapidKey: 'YOUR_VAPID_KEY'
            });
            
            if (token) {
                console.log('FCM Token:', token);
                // Send token to backend
                await registerDeviceToken(token);
                return token;
            }
        } else {
            console.log('Notification permission denied.');
        }
    } catch (error) {
        console.error('Error getting notification permission:', error);
    }
}

// Register device token to backend
async function registerDeviceToken(token) {
    try {
        const response = await fetch('/api/device-tokens', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`,
            },
            body: JSON.stringify({
                device_token: token,
                device_type: 'web',
                device_name: navigator.userAgent,
                browser: getBrowserName(),
            }),
        });
        
        const data = await response.json();
        console.log('Device registered:', data);
    } catch (error) {
        console.error('Error registering device:', error);
    }
}

// Handle foreground messages
onMessage(messaging, (payload) => {
    console.log('Message received:', payload);
    
    const { title, body, image } = payload.notification;
    
    // Show browser notification
    showNotification(title, body, image);
    
    // Update notification bell badge
    updateNotificationBadge();
    
    // Show toast
    showToast(title, body);
});

// Helper functions
function getBrowserName() {
    const userAgent = navigator.userAgent;
    if (userAgent.indexOf('Chrome') > -1) return 'Chrome';
    if (userAgent.indexOf('Safari') > -1) return 'Safari';
    if (userAgent.indexOf('Firefox') > -1) return 'Firefox';
    if (userAgent.indexOf('Edge') > -1) return 'Edge';
    return 'Unknown';
}

function getAuthToken() {
    return localStorage.getItem('auth_token');
}
```

#### Step 8.4: Service Worker
```javascript
// public/firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "YOUR_API_KEY",
    authDomain: "mypengaduan.firebaseapp.com",
    projectId: "mypengaduan",
    storageBucket: "mypengaduan.appspot.com",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Background message received:', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        image: payload.notification.image,
        data: payload.data,
        actions: [
            { action: 'open', title: 'Buka' },
            { action: 'close', title: 'Tutup' }
        ]
    };
    
    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'open') {
        const url = event.notification.data.url || '/';
        event.waitUntil(
            clients.openWindow(url)
        );
    }
});
```

#### Step 8.5: Notification UI Component
```javascript
// resources/js/notification-ui.js
class NotificationUI {
    constructor() {
        this.bellIcon = document.getElementById('notification-bell');
        this.badge = document.getElementById('notification-badge');
        this.dropdown = document.getElementById('notification-dropdown');
        this.list = document.getElementById('notification-list');
        
        this.init();
    }
    
    init() {
        this.loadNotifications();
        this.setupEventListeners();
        this.startPolling();
    }
    
    async loadNotifications() {
        try {
            const response = await fetch('/api/notifications', {
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`,
                },
            });
            
            const data = await response.json();
            this.renderNotifications(data.data.data);
            this.updateBadge(data.unread_count);
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
    
    renderNotifications(notifications) {
        if (notifications.length === 0) {
            this.list.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    Tidak ada notifikasi
                </div>
            `;
            return;
        }
        
        this.list.innerHTML = notifications.map(notif => `
            <div class="notification-item ${notif.read_at ? '' : 'unread'}" 
                 data-id="${notif.id}">
                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${this.getNotificationIcon(notif.type)}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                ${notif.data.title}
                            </p>
                            <p class="text-sm text-gray-500">
                                ${notif.data.body}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                ${this.formatTime(notif.created_at)}
                            </p>
                        </div>
                        ${!notif.read_at ? '<span class="w-2 h-2 bg-blue-600 rounded-full"></span>' : ''}
                    </div>
                </div>
            </div>
        `).join('');
        
        // Add click listeners
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                this.markAsRead(item.dataset.id);
            });
        });
    }
    
    updateBadge(count) {
        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.classList.remove('hidden');
        } else {
            this.badge.classList.add('hidden');
        }
    }
    
    async markAsRead(id) {
        try {
            await fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`,
                },
            });
            
            this.loadNotifications();
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }
    
    getNotificationIcon(type) {
        const icons = {
            'complaint_created': '🆕',
            'complaint_status_changed': '📊',
            'announcement_created': '📢',
            'admin_response': '💬',
            'comment_added': '💭',
        };
        
        return icons[type] || '🔔';
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'Baru saja';
        if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
        return `${Math.floor(diff / 86400)} hari lalu`;
    }
    
    setupEventListeners() {
        this.bellIcon.addEventListener('click', () => {
            this.dropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.bellIcon.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.dropdown.classList.add('hidden');
            }
        });
    }
    
    startPolling() {
        // Poll for new notifications every 30 seconds
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    if (isUserLoggedIn()) {
        new NotificationUI();
    }
});

function isUserLoggedIn() {
    return !!localStorage.getItem('auth_token');
}

function getAuthToken() {
    return localStorage.getItem('auth_token');
}
```

---

### **Phase 9: API Routes (10 menit)**

```php
// routes/api.php

use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;

Route::middleware('auth:sanctum')->group(function () {
    // Device tokens
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::get('/device-tokens', [DeviceTokenController::class, 'index']);
    Route::delete('/device-tokens/{id}', [DeviceTokenController::class, 'destroy']);
    Route::patch('/device-tokens/{id}/toggle', [DeviceTokenController::class, 'toggle']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'clearAll']);
    
    // Notification settings
    Route::get('/notification-settings', [NotificationController::class, 'getSettings']);
    Route::put('/notification-settings', [NotificationController::class, 'updateSettings']);
});
```

---

### **Phase 10: UI Integration (30 menit)**

#### Step 10.1: Add to Layout
```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <!-- ... existing head content ... -->
    
    <!-- Firebase & Notifications -->
    @vite(['resources/js/firebase-init.js', 'resources/js/notification-ui.js'])
</head>

<body>
    <!-- Notification Bell in Navbar -->
    <div class="relative">
        <button id="notification-bell" class="relative p-2 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span id="notification-badge" 
                  class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full hidden">
                0
            </span>
        </button>
        
        <!-- Notification Dropdown -->
        <div id="notification-dropdown" 
             class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
                    <button onclick="markAllAsRead()" 
                            class="text-sm text-blue-600 hover:text-blue-800">
                        Tandai semua dibaca
                    </button>
                </div>
            </div>
            <div id="notification-list" class="max-h-96 overflow-y-auto">
                <!-- Notifications will be loaded here -->
            </div>
            <div class="p-4 border-t border-gray-200 text-center">
                <a href="/notifications" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat semua notifikasi
                </a>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2">
        <!-- Toasts will appear here -->
    </div>
    
    <!-- Request Notification Permission on Login -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @auth
                // Request notification permission
                import('./firebase-init.js').then(module => {
                    module.requestNotificationPermission();
                });
            @endauth
        });
    </script>
</body>
```

---

## 📊 Timeline Estimasi

| Phase | Task | Estimasi |
|-------|------|----------|
| 1 | Firebase Setup | 30 menit |
| 2 | Database Migrations | 20 menit |
| 3 | Models | 15 menit |
| 4 | Firebase & Notification Services | 30 menit |
| 5 | API Controllers | 45 menit |
| 6 | Events & Listeners | 30 menit |
| 7 | Update Existing Controllers | 20 menit |
| 8 | Frontend Setup | 60 menit |
| 9 | API Routes | 10 menit |
| 10 | UI Integration | 30 menit |
| 11 | Testing & Debugging | 60 menit |

**Total: ~5.5 jam**

---

## 🧪 Testing Checklist

### Backend Testing
- [ ] Device token registration works
- [ ] FCM sends to single device
- [ ] FCM sends to multiple devices
- [ ] Topic subscription works
- [ ] Events trigger correctly
- [ ] Listeners send notifications
- [ ] Notification settings save/load
- [ ] API endpoints secured with auth

### Frontend Testing
- [ ] FCM token generated
- [ ] Permission request shows
- [ ] Foreground notifications display
- [ ] Background notifications work
- [ ] Service worker registered
- [ ] Notification bell updates
- [ ] Dropdown shows notifications
- [ ] Mark as read works
- [ ] Toast notifications appear

### Integration Testing
- [ ] Create complaint → Admin gets notification
- [ ] Change status → User gets notification
- [ ] Create announcement → All users notified
- [ ] Admin responds → User gets notification
- [ ] Settings persist across sessions

---

## 🔐 Security Considerations

1. **FCM Token Security**
   - Tokens stored securely in database
   - HTTPS only for API calls
   - Token refresh on expiry

2. **API Security**
   - All routes protected with Sanctum auth
   - Validate user ownership of devices
   - Rate limiting on notification endpoints

3. **Data Privacy**
   - Users control notification preferences
   - Can delete device tokens
   - Can disable notifications anytime

---

## 📱 Progressive Web App (PWA) Enhancement

```javascript
// public/sw.js
self.addEventListener('push', (event) => {
    const data = event.data.json();
    
    const options = {
        body: data.body,
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
        vibrate: [200, 100, 200],
        data: data.data,
        actions: [
            { action: 'view', title: 'Lihat' },
            { action: 'dismiss', title: 'Tutup' }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});
```

---

## 🚀 Deployment Checklist

### Before Deploy
- [ ] Firebase project created
- [ ] Firebase credentials downloaded
- [ ] Environment variables set
- [ ] Database migrations run
- [ ] Service worker registered
- [ ] VAPID keys generated

### After Deploy
- [ ] Test on production
- [ ] Monitor FCM logs
- [ ] Check notification delivery
- [ ] Test on multiple devices
- [ ] Monitor error rates

---

## 📚 Resources

- [Firebase Cloud Messaging Docs](https://firebase.google.com/docs/cloud-messaging)
- [Laravel Notifications](https://laravel.com/docs/notifications)
- [Kreait Firebase PHP](https://github.com/kreait/laravel-firebase)
- [Web Push Protocol](https://web.dev/push-notifications/)

---

## 💡 Future Enhancements

- [ ] Email notifications as fallback
- [ ] SMS notifications for critical alerts
- [ ] In-app notification center
- [ ] Notification templates
- [ ] Scheduled notifications
- [ ] Rich media notifications
- [ ] Group notifications
- [ ] Notification analytics

---

**Status:** ✅ Ready to Implement  
**Priority:** High  
**Complexity:** Medium-High  
**Impact:** Very High (Significant UX improvement)
