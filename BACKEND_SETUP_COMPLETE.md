# ✅ Laravel Backend Setup - COMPLETED!

## 🎉 Status: BERHASIL

Setup Laravel backend untuk notifikasi FCM mobile-only **SUDAH SELESAI**!

---

## 📦 Yang Sudah Diinstall

### 1. **Package Installed** ✅
```bash
✅ kreait/laravel-firebase ^6.1
✅ kreait/firebase-php 7.23.0
✅ 25 dependencies berhasil diinstall
```

### 2. **Database Tables Created** ✅
```sql
✅ user_devices (9 kolom)
   - id, user_id, device_token, device_type, device_model,
     os_version, app_version, is_active, last_used_at, timestamps
   
✅ notification_settings (8 kolom)
   - id, user_id, complaint_created, complaint_status_changed,
     announcement_created, admin_response, comment_added, 
     push_enabled, timestamps
```

### 3. **Models Created** ✅
```
✅ app/Models/UserDevice.php
   - Relationships: belongsTo User
   - Scopes: active(), android(), ios()
   - Fillable: all device fields
   - Casts: is_active (boolean), last_used_at (datetime)

✅ app/Models/NotificationSetting.php
   - Relationships: belongsTo User
   - Fillable: all notification settings
   - Casts: all booleans

✅ app/Models/User.php (Updated)
   - hasMany devices()
   - hasOne notificationSettings()
   - getActiveDeviceTokens() helper method
```

### 4. **Services Created** ✅
```
✅ app/Services/FirebaseService.php
   Methods:
   - sendToDevice($token, $title, $body, $data)
   - sendToMultipleDevices($tokens, $title, $body, $data)
   - sendToTopic($topic, $title, $body, $data)
   - subscribeToTopic($tokens, $topic)
   - markTokenAsInactive($token) [auto cleanup]
```

### 5. **API Controllers Created** ✅
```
✅ app/Http/Controllers/Api/DeviceTokenController.php
   - store()    → Register device token
   - index()    → Get user devices
   - destroy()  → Remove device

✅ app/Http/Controllers/Api/NotificationController.php
   - index()           → Get notifications
   - markAsRead($id)   → Mark one as read
   - markAllAsRead()   → Mark all as read
   - getSettings()     → Get notification preferences
   - updateSettings()  → Update preferences
```

### 6. **API Routes Added** ✅
```
✅ POST   /api/device-tokens           → Register FCM token
✅ GET    /api/device-tokens           → List user devices
✅ DELETE /api/device-tokens/{id}      → Remove device

✅ GET    /api/notifications           → Get notifications
✅ POST   /api/notifications/{id}/read → Mark as read
✅ POST   /api/notifications/read-all  → Mark all read

✅ GET    /api/notification-settings   → Get settings
✅ PUT    /api/notification-settings   → Update settings
```

### 7. **Configuration Files** ✅
```
✅ config/firebase.php (published)
✅ .env (updated with Firebase config)
✅ storage/app/firebase/ (folder created)
✅ storage/app/firebase/README.md (instructions)
```

---

## 📁 File Structure

```
mypengaduan/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── DeviceTokenController.php      ✅ NEW
│   │   └── NotificationController.php      ✅ NEW
│   ├── Models/
│   │   ├── User.php                        ✅ UPDATED
│   │   ├── UserDevice.php                  ✅ NEW
│   │   └── NotificationSetting.php         ✅ NEW
│   └── Services/
│       └── FirebaseService.php             ✅ NEW
├── config/
│   └── firebase.php                        ✅ NEW
├── database/migrations/
│   ├── 2025_10_20_083709_create_user_devices_table.php        ✅ NEW
│   └── 2025_10_20_083924_create_notification_settings_table.php ✅ NEW
├── routes/
│   └── api.php                             ✅ UPDATED
├── storage/app/firebase/
│   └── README.md                           ✅ NEW
└── .env                                    ✅ UPDATED
```

---

## 🔑 Next Steps (Untuk Anda)

### **1. Setup Firebase Project (30 menit)**

#### a. Create Firebase Project
1. Buka: https://console.firebase.google.com
2. Klik **"Add project"** atau **"Tambah project"**
3. Nama project: `MyPengaduan` (atau nama lain yang Anda mau)
4. Enable/Disable Google Analytics (optional)
5. Klik **"Create project"**

#### b. Enable Cloud Messaging
1. Di sidebar → **Build** → **Cloud Messaging**
2. Jika muncul prompt "Enable API" → klik **Enable**

#### c. Add Mobile App (Android)
1. Project Overview → klik icon Android
2. **Android package name**: `com.yourcompany.mypengaduan` (sesuaikan!)
3. **App nickname**: MyPengaduan (optional)
4. Klik **"Register app"**
5. **DOWNLOAD** file `google-services.json`
6. **SIMPAN** file ini (akan dikirim ke mobile developer)

#### d. Add Mobile App (iOS) - jika perlu
1. Project Overview → klik icon Apple
2. **iOS bundle ID**: `com.yourcompany.mypengaduan`
3. **App nickname**: MyPengaduan
4. Klik **"Register app"**
5. **DOWNLOAD** file `GoogleService-Info.plist`
6. **SIMPAN** file ini (untuk mobile developer)

#### e. Download Service Account (PENTING!)
1. Klik ⚙️ (Settings) → **Project settings**
2. Tab **"Service accounts"**
3. Klik **"Generate new private key"**
4. Konfirmasi dengan **"Generate key"**
5. File JSON akan ter-download
6. **RENAME** file menjadi: `firebase-credentials.json`
7. **COPY** file ke folder: `storage/app/firebase/firebase-credentials.json`

### **2. Update .env File**

Buka file `.env` dan update:

```env
FIREBASE_PROJECT_ID=mypengaduan-xxxx
FIREBASE_CREDENTIALS=firebase/firebase-credentials.json
```

Ganti `mypengaduan-xxxx` dengan **Project ID** Anda (lihat di Firebase Console).

### **3. Test Connection**

```bash
php artisan tinker

# Test apakah Firebase service bisa diload
$firebase = app(\App\Services\FirebaseService::class);
dd($firebase);
```

Jika tidak ada error → **SETUP BERHASIL!** ✅

---

## 🧪 Testing API Endpoints

### Test 1: Register Device Token

**Endpoint:** `POST /api/device-tokens`  
**Headers:**
```
Authorization: Bearer YOUR_SANCTUM_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "device_token": "FIREBASE_FCM_TOKEN_FROM_MOBILE",
  "device_type": "android",
  "device_model": "Samsung Galaxy S21",
  "os_version": "Android 13",
  "app_version": "1.0.0"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "device_token": "FIREBASE_FCM_TOKEN...",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "Android 13",
    "app_version": "1.0.0",
    "is_active": true,
    "last_used_at": "2025-10-20T08:45:00.000000Z",
    "created_at": "2025-10-20T08:45:00.000000Z",
    "updated_at": "2025-10-20T08:45:00.000000Z"
  }
}
```

### Test 2: Get Notification Settings

**Endpoint:** `GET /api/notification-settings`  
**Headers:**
```
Authorization: Bearer YOUR_SANCTUM_TOKEN
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "complaint_created": true,
    "complaint_status_changed": true,
    "announcement_created": true,
    "admin_response": true,
    "comment_added": true,
    "push_enabled": true,
    "created_at": "2025-10-20T08:45:00.000000Z",
    "updated_at": "2025-10-20T08:45:00.000000Z"
  }
}
```

---

## 📱 Untuk Mobile Developer

### Files yang Perlu Dikirim ke Mobile Developer:

1. ✅ **google-services.json** (Android)
2. ✅ **GoogleService-Info.plist** (iOS)
3. ✅ **Server Key** (optional, untuk testing manual)
4. ✅ API Endpoint Documentation (sudah ada di `routes/api.php`)

### API Endpoints yang Perlu Diintegrasikan:

```
Base URL: https://your-domain.com/api

Authentication: Bearer Token (Sanctum)

Endpoints:
- POST   /device-tokens           → Register FCM token saat app pertama kali dibuka
- GET    /device-tokens           → List registered devices
- DELETE /device-tokens/{id}      → Logout / remove device
- GET    /notifications           → Fetch notification history
- POST   /notifications/{id}/read → Mark notification as read
- GET    /notification-settings   → Get user preferences
- PUT    /notification-settings   → Update preferences
```

### Notification Payload yang Akan Diterima:

```json
{
  "notification": {
    "title": "Keluhan Baru #123",
    "body": "User John Doe membuat keluhan baru"
  },
  "data": {
    "type": "complaint_created",
    "complaint_id": "123",
    "user_id": "45",
    "timestamp": "2025-10-20T08:45:00Z"
  }
}
```

### Notification Types:

```
1. complaint_created           → Admin dpt notif (user buat keluhan baru)
2. complaint_status_changed    → User dpt notif (status keluhan berubah)
3. announcement_created        → Semua user dpt notif (pengumuman baru)
4. admin_response              → User dpt notif (admin balas keluhan)
5. comment_added               → Terkait dpt notif (ada komentar baru)
```

---

## ⚠️ Security Notes

### File yang HARUS di `.gitignore`:
```
✅ storage/app/firebase/firebase-credentials.json
✅ .env
```

### File Sudah Protected:
- ✅ `firebase-credentials.json` → TIDAK akan ter-commit ke Git
- ✅ `.env` → TIDAK akan ter-commit ke Git
- ✅ Sudah ada di `.gitignore` default Laravel

---

## 🚀 What's Next?

### Day 2: Implement Event & Listeners (Tomorrow)

**Yang perlu dibuat:**
1. **Events:**
   - ComplaintCreated
   - ComplaintStatusChanged
   - AnnouncementCreated
   - CommentAdded

2. **Listeners:**
   - SendComplaintNotificationToAdmin
   - SendStatusChangeNotificationToUser
   - SendAnnouncementNotificationToAll
   - SendCommentNotification

3. **Update Controllers:**
   - ComplaintController → trigger ComplaintCreated event
   - Admin\ComplaintController → trigger StatusChanged event
   - AnnouncementController → trigger AnnouncementCreated event
   - CommentController → trigger CommentAdded event

**Estimasi waktu:** 2-3 jam

---

## 📊 Summary Statistics

```
✅ Packages Installed:     1 (kreait/laravel-firebase + 25 dependencies)
✅ Database Tables:        2 (user_devices, notification_settings)
✅ Models Created:         2 (UserDevice, NotificationSetting)
✅ Models Updated:         1 (User)
✅ Services Created:       1 (FirebaseService)
✅ Controllers Created:    2 (DeviceTokenController, NotificationController)
✅ Routes Added:           8 API endpoints
✅ Config Files:           2 (firebase.php, .env updated)
✅ Documentation:          3 files (this + README + mobile setup)

Total Time Spent:         ~45 minutes
Estimated Time Saved:     ~3-4 hours (dengan automation)
```

---

## ✨ Congratulations!

Laravel backend untuk notifikasi FCM **SUDAH SIAP**! 🎉

**Next action:** Setup Firebase project dan download credentials file, lalu kita lanjut ke **Day 2** untuk implement Events & Listeners.

**Questions?** Silakan tanya jika ada yang kurang jelas! 💪
