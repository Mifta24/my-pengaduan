# 🚀 Quick Start - Testing Notifications

## ⚡ **Quick Setup (5 Minutes)**

### **Step 1: Start Queue Worker**
```bash
# Open new terminal and run:
php artisan queue:work

# Keep this running in background
```

### **Step 2: Register Test Device (Mobile Developer)**

Mobile app harus register FCM token terlebih dahulu:

```javascript
// Mobile app code (React Native / Flutter)
POST /api/device-tokens
Headers: {
  Authorization: Bearer {token}
}
Body: {
  device_token: "FCM_TOKEN_FROM_FIREBASE_SDK",
  device_type: "android",
  device_model: "Samsung Galaxy S21",
  os_version: "Android 13",
  app_version: "1.0.0"
}
```

### **Step 3: Test Notifications**

## 🧪 **Test Scenarios:**

### **Test 1: New Complaint → Admin Gets Notified** 📋

#### **Option A: Via API (cURL)**
```bash
curl -X POST http://localhost/api/complaints \
  -H "Authorization: Bearer USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Keluhan Notifikasi",
    "description": "Testing apakah admin dapat notifikasi",
    "category_id": 1,
    "location": "RT 05 RW 03"
  }'
```

#### **Option B: Via Web**
1. Login as **user** (not admin)
2. Go to: http://localhost/complaints/create
3. Fill form and submit
4. Wait ~5 seconds

#### **Expected Result:**
✅ Admin device receives FCM notification:
```
Title: 🆕 Keluhan Baru #123
Body: John Doe membuat keluhan baru: Test Keluhan Notifikasi
```

#### **Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Complaint notification"
```

Should see:
```
Complaint notification sent to admin
```

---

### **Test 2: Status Change → User Gets Notified** 🔄

#### **Steps:**
1. Login as **admin**
2. Go to complaint detail page
3. Change status (e.g., Pending → In Progress)
4. Click "Update Status"
5. Wait ~5 seconds

#### **Expected Result:**
✅ User device receives FCM notification:
```
Title: 🔄 Status Keluhan Diperbarui
Body: Keluhan #123 - Test Keluhan sekarang berstatus: Diproses
```

#### **Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Status change notification"
```

---

### **Test 3: Announcement → All Users Get Notified** 📢

#### **Steps:**
1. Login as **admin**
2. Go to: http://localhost/admin/announcements/create
3. Fill form:
   - Title: "Test Pengumuman Notifikasi"
   - Content: "Testing broadcast notification"
   - Priority: "urgent" (optional)
4. Submit
5. Wait ~5 seconds

#### **Expected Result:**
✅ ALL user devices receive FCM notification:
```
Title: 🚨 Pengumuman Baru (if urgent) or 📢 Pengumuman Baru
Body: Test Pengumuman Notifikasi
```

#### **Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Announcement notification"
```

Should see:
```
Announcement notification sent to all users
total_users: 10
total_devices: 15
```

---

## 🔍 **Debugging:**

### **Issue 1: Notification Not Received**

#### **Checklist:**
```bash
# 1. Check queue worker running
ps aux | grep "queue:work"

# 2. Check pending jobs
php artisan queue:monitor

# 3. Check failed jobs
php artisan queue:failed

# 4. Check logs
tail -f storage/logs/laravel.log

# 5. Check device token registered
php artisan tinker
>>> \App\Models\UserDevice::where('user_id', 1)->get();
```

#### **Common Issues:**
- ❌ Queue worker not running → Start with `php artisan queue:work`
- ❌ Invalid FCM token → Mobile app needs to re-register
- ❌ Firebase not configured → Check `firebase-credentials.json`
- ❌ User disabled notifications → Check `notification_settings`

---

### **Issue 2: Job Failed**

#### **Check Failed Jobs:**
```bash
php artisan queue:failed
```

#### **Retry Failed Jobs:**
```bash
# Retry all
php artisan queue:retry all

# Retry specific job
php artisan queue:retry {job-id}
```

#### **View Error Details:**
```sql
SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 1;
```

---

## 📊 **Monitoring:**

### **Real-time Queue Monitoring:**
```bash
# Watch queue jobs in real-time
watch -n 2 'php artisan queue:monitor'
```

### **Check Database:**
```sql
-- Pending jobs
SELECT id, queue, created_at, available_at 
FROM jobs 
ORDER BY id DESC 
LIMIT 10;

-- Device tokens
SELECT u.name, u.email, COUNT(ud.id) as device_count
FROM users u
LEFT JOIN user_devices ud ON u.id = ud.user_id AND ud.is_active = true
GROUP BY u.id;

-- Notification settings
SELECT u.name, ns.*
FROM users u
LEFT JOIN notification_settings ns ON u.id = ns.user_id;
```

---

## 🎯 **Production Checklist:**

Before going to production:

- [ ] **Queue Worker Setup**
  - [ ] Use Supervisor to keep queue worker running
  - [ ] Configure retry attempts: `--tries=3`
  - [ ] Set timeout: `--timeout=60`
  - [ ] Enable daemon mode: `--daemon`

- [ ] **Firebase Configuration**
  - [ ] firebase-credentials.json in place
  - [ ] FIREBASE_PROJECT_ID set in .env
  - [ ] File permissions correct (readable)

- [ ] **Monitoring**
  - [ ] Queue monitoring enabled
  - [ ] Log rotation configured
  - [ ] Failed job alerts set up

- [ ] **Performance**
  - [ ] Redis for queue (recommended over database)
  - [ ] Queue workers scaled based on traffic
  - [ ] Batch size optimized

---

## 📱 **For Mobile Developer:**

### **FCM Token Registration:**
```dart
// Flutter example
import 'package:firebase_messaging/firebase_messaging.dart';

Future<void> registerDevice() async {
  final fcmToken = await FirebaseMessaging.instance.getToken();
  
  // Send to API
  final response = await http.post(
    Uri.parse('$baseUrl/api/device-tokens'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({
      'device_token': fcmToken,
      'device_type': Platform.isAndroid ? 'android' : 'ios',
      'device_model': 'Model name',
      'os_version': 'OS version',
      'app_version': '1.0.0',
    }),
  );
}
```

### **Handle Incoming Notifications:**
```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Got a message in foreground!');
  print('Title: ${message.notification?.title}');
  print('Body: ${message.notification?.body}');
  print('Data: ${message.data}');
  
  // Handle based on type
  final type = message.data['type'];
  switch (type) {
    case 'complaint_created':
      // Navigate to complaint list
      break;
    case 'complaint_status_changed':
      // Navigate to complaint detail
      final complaintId = message.data['complaint_id'];
      Navigator.push(...);
      break;
    case 'announcement_created':
      // Navigate to announcements
      break;
  }
});
```

---

## ⚡ **Quick Commands:**

```bash
# Start queue worker
php artisan queue:work

# Start with auto-restart on code changes
php artisan queue:work --tries=3

# Process one job only (for testing)
php artisan queue:work --once

# Clear all jobs
php artisan queue:flush

# Restart queue workers
php artisan queue:restart

# View failed jobs
php artisan queue:failed

# Retry all failed
php artisan queue:retry all
```

---

## 🎉 **Success Indicators:**

You know it's working when:

1. ✅ Queue worker shows job processing
2. ✅ Logs show "notification sent to..."
3. ✅ Mobile device receives notification
4. ✅ Tapping notification opens correct screen
5. ✅ No failed jobs in queue
6. ✅ All tests pass

---

**Ready to test? Start the queue worker and create a complaint!** 🚀
