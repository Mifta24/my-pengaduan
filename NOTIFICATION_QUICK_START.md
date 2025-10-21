# 🚀 Quick Start - Implementasi Notifikasi FCM

## 📌 Ringkasan Singkat

Panduan cepat untuk mengimplementasikan sistem notifikasi Firebase Cloud Messaging di MyPengaduan.

---

## ⚡ Quick Setup (30 menit pertama)

### Step 1: Setup Firebase Project (10 menit)

1. **Buka Firebase Console**
   ```
   https://console.firebase.google.com
   ```

2. **Create Project**
   - Klik "Add project"
   - Nama: "MyPengaduan"
   - Enable Google Analytics (optional)
   - Klik "Create project"

3. **Add Web App**
   - Klik icon Web (</>) 
   - Nickname: "MyPengaduan Web"
   - ✅ Check "Also set up Firebase Hosting"
   - Klik "Register app"
   - **SIMPAN** konfigurasi yang muncul

4. **Enable Cloud Messaging**
   - Di sidebar, klik "Build" → "Cloud Messaging"
   - Klik "Get started"
   - Klik tab "Web configuration"
   - Klik "Generate key pair"
   - **SIMPAN** VAPID key

5. **Download Service Account**
   - Klik ⚙️ (Settings) → "Project settings"
   - Tab "Service accounts"
   - Klik "Generate new private key"
   - Download file JSON
   - Rename menjadi `firebase-credentials.json`
   - Simpan ke `storage/app/firebase/`

---

### Step 2: Install Dependencies (5 menit)

```bash
# Backend
composer require kreait/laravel-firebase

# Frontend
npm install firebase
```

---

### Step 3: Configuration (5 menit)

#### 3.1 Publish Firebase Config
```bash
php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider" --tag=config
```

#### 3.2 Update .env
```env
# Firebase Configuration
FIREBASE_PROJECT_ID=mypengaduan
FIREBASE_DATABASE_URL=https://mypengaduan-default-rtdb.firebaseio.com
FIREBASE_CREDENTIALS=firebase/firebase-credentials.json
FIREBASE_VAPID_KEY=your_vapid_key_here
```

#### 3.3 Create Firebase Config JS
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

export const vapidKey = "YOUR_VAPID_KEY";
```

---

### Step 4: Create Database Tables (5 menit)

```bash
# Create migrations
php artisan make:migration create_user_devices_table
php artisan make:migration create_notification_settings_table
```

**Copy migration code dari NOTIFICATION_SYSTEM_PLAN.md Phase 2**

```bash
# Run migrations
php artisan migrate
```

---

### Step 5: Create Models (5 menit)

```bash
php artisan make:model UserDevice
php artisan make:model NotificationSetting
```

**Copy model code dari NOTIFICATION_SYSTEM_PLAN.md Phase 3**

---

## 🎯 Minimal Working Example

Setelah setup awal, ini adalah implementasi minimal yang bisa langsung jalan:

### Backend - Send Notification

```php
// app/Services/FirebaseService.php (simplified)
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));
        
        $this->messaging = $factory->createMessaging();
    }

    public function sendToDevice(string $token, string $title, string $body)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));

        return $this->messaging->send($message);
    }
}
```

### Frontend - Receive Notification

```javascript
// resources/js/app.js
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';
import { firebaseConfig, vapidKey } from './firebase-config';

// Initialize
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request permission & get token
async function initNotifications() {
    const permission = await Notification.requestPermission();
    
    if (permission === 'granted') {
        const token = await getToken(messaging, { vapidKey });
        console.log('FCM Token:', token);
        
        // Send to backend
        fetch('/api/device-tokens', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({
                device_token: token,
                device_type: 'web'
            })
        });
    }
}

// Handle foreground messages
onMessage(messaging, (payload) => {
    console.log('Notification:', payload);
    new Notification(payload.notification.title, {
        body: payload.notification.body
    });
});

// Run on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotifications);
} else {
    initNotifications();
}
```

---

## 📝 Test Notifikasi Pertama

### 1. Register Device Token

```bash
# Via API (setelah login)
curl -X POST http://localhost/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "FCM_TOKEN_FROM_FRONTEND",
    "device_type": "web"
  }'
```

### 2. Send Test Notification

```php
// routes/web.php (temporary test route)
Route::get('/test-notification', function () {
    $firebaseService = app(App\Services\FirebaseService::class);
    
    $token = 'USER_FCM_TOKEN'; // Get from database
    $title = '🔔 Test Notification';
    $body = 'Notifikasi berhasil dikirim!';
    
    $result = $firebaseService->sendToDevice($token, $title, $body);
    
    return response()->json([
        'success' => true,
        'message' => 'Notification sent',
        'result' => $result
    ]);
})->middleware('auth');
```

Buka: `http://localhost/test-notification`

---

## 🎨 UI Components (Copy-Paste Ready)

### Notification Bell Icon

```blade
<!-- Add to navbar -->
<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Badge -->
        <span id="notif-badge" 
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2 hidden">
            0
        </span>
    </button>
    
    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
        
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Notifikasi</h3>
        </div>
        
        <div id="notif-list" class="max-h-96 overflow-y-auto">
            <!-- Notifications loaded here -->
            <div class="p-4 text-center text-gray-500">
                Tidak ada notifikasi
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-200 text-center">
            <a href="/notifications" class="text-sm text-blue-600 hover:text-blue-800">
                Lihat semua
            </a>
        </div>
    </div>
</div>
```

### Toast Notification

```javascript
// resources/js/toast.js
function showToast(title, message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `
        bg-white rounded-lg shadow-lg border-l-4 
        ${type === 'success' ? 'border-green-500' : 
          type === 'error' ? 'border-red-500' : 
          'border-blue-500'}
        p-4 mb-4 flex items-start animate-slide-in
    `;
    
    toast.innerHTML = `
        <div class="flex-shrink-0">
            ${type === 'success' ? '✅' : type === 'error' ? '❌' : '🔔'}
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-gray-900">${title}</p>
            <p class="text-sm text-gray-500">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
            ✕
        </button>
    `;
    
    document.getElementById('toast-container').appendChild(toast);
    
    setTimeout(() => toast.remove(), 5000);
}

// Usage
showToast('Keluhan Baru', 'Keluhan dari John Doe telah dibuat', 'success');
```

---

## 🔥 Trigger Notifikasi dari Controller

### Example: Notify Admin on New Complaint

```php
// app/Http/Controllers/ComplaintController.php

use App\Services\FirebaseService;
use App\Models\User;

public function store(Request $request)
{
    // ... validation
    
    $complaint = Complaint::create($request->all());
    
    // 🔔 Send notification to all admins
    $firebaseService = app(FirebaseService::class);
    $admins = User::role('admin')->get();
    
    foreach ($admins as $admin) {
        $tokens = $admin->devices()->active()->pluck('device_token')->toArray();
        
        foreach ($tokens as $token) {
            $firebaseService->sendToDevice(
                $token,
                '🆕 Keluhan Baru',
                "Keluhan dari {$complaint->user->name}: {$complaint->title}"
            );
        }
    }
    
    return redirect()->route('complaints.show', $complaint);
}
```

### Example: Notify User on Status Change

```php
// app/Http/Controllers/Admin/ComplaintController.php

public function updateStatus(Request $request, Complaint $complaint)
{
    $complaint->update(['status' => $request->status]);
    
    // 🔔 Notify complaint owner
    $firebaseService = app(FirebaseService::class);
    $user = $complaint->user;
    $tokens = $user->getActiveDeviceTokens();
    
    foreach ($tokens as $token) {
        $firebaseService->sendToDevice(
            $token,
            '📊 Status Keluhan Berubah',
            "Keluhan Anda \"{$complaint->title}\" statusnya berubah menjadi {$request->status}"
        );
    }
    
    return back()->with('success', 'Status updated');
}
```

---

## 🎯 Priority Implementation Order

### Phase 1: Core (Day 1)
1. ✅ Setup Firebase project
2. ✅ Install packages
3. ✅ Create migrations & models
4. ✅ Create FirebaseService
5. ✅ Frontend Firebase init
6. ✅ Test notification (manual)

### Phase 2: Admin Notifications (Day 2)
1. ✅ Notify admins on new complaint
2. ✅ Notification bell UI
3. ✅ Notification list API
4. ✅ Mark as read functionality

### Phase 3: User Notifications (Day 3)
1. ✅ Notify user on status change
2. ✅ Notify user on admin response
3. ✅ Notification settings page
4. ✅ Enable/disable notifications

### Phase 4: Announcements (Day 4)
1. ✅ Notify all users on new announcement
2. ✅ Topic subscription
3. ✅ Rich media notifications

### Phase 5: Polish (Day 5)
1. ✅ Toast notifications
2. ✅ Service worker
3. ✅ Background notifications
4. ✅ Testing & bug fixes

---

## 🐛 Common Issues & Solutions

### Issue 1: CORS Error
```
Solution: Add to .env
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

### Issue 2: Service Worker Not Registering
```javascript
// Check HTTPS (required for service workers)
if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
    navigator.serviceWorker.register('/firebase-messaging-sw.js');
}
```

### Issue 3: Token Not Sending
```javascript
// Check if user is authenticated
if (!localStorage.getItem('auth_token')) {
    console.error('User not authenticated');
    return;
}
```

### Issue 4: Notification Permission Denied
```javascript
// Always check permission first
if (Notification.permission === 'denied') {
    alert('Mohon aktifkan notifikasi di pengaturan browser');
}
```

---

## 📊 Testing Checklist

- [ ] Firebase project created
- [ ] Credentials downloaded
- [ ] Migrations run successfully
- [ ] Frontend can request permission
- [ ] FCM token generated
- [ ] Token saved to database
- [ ] Test notification sent
- [ ] Notification received
- [ ] Bell icon shows badge
- [ ] Dropdown loads notifications
- [ ] Mark as read works
- [ ] Background notifications work

---

## 📚 Next Steps

1. **Read Full Plan:** `NOTIFICATION_SYSTEM_PLAN.md`
2. **Follow Phases:** Implement step-by-step
3. **Test Each Feature:** Don't skip testing
4. **Monitor Logs:** Check Laravel logs & Firebase console
5. **Deploy Gradually:** Test in staging first

---

## 💡 Tips

✅ **DO:**
- Test on HTTPS (required for service workers)
- Handle permission denied gracefully
- Store tokens securely
- Update tokens on refresh
- Log errors for debugging

❌ **DON'T:**
- Hardcode Firebase credentials in JS
- Send too many notifications (spam)
- Forget to unsubscribe on logout
- Ignore error handling
- Skip service worker testing

---

## 🎉 Success Criteria

Your notification system is working when:
- ✅ User can enable/disable notifications
- ✅ Admin receives notification when user creates complaint
- ✅ User receives notification when status changes
- ✅ All users receive notification on new announcement
- ✅ Notifications work in background
- ✅ Badge count updates in real-time
- ✅ Clicking notification opens relevant page

---

**Ready to start?** Follow Phase 1 first! 🚀

**Need help?** Check `NOTIFICATION_SYSTEM_PLAN.md` for detailed code.
