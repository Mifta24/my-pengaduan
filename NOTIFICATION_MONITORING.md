# 📊 Firebase FCM Notification Monitoring Guide

**Question:** Bisa cek message FCM di Firebase Console?  
**Answer:** ❌ **Tidak bisa langsung**, tapi ada cara lain yang lebih baik!

---

## 🤔 **Kenapa Tidak Bisa Lihat di Firebase Console?**

Firebase Cloud Messaging (FCM) adalah **delivery service** (seperti kantor pos), bukan database. FCM hanya:
- ✅ Menerima request dari server
- ✅ Mengirim ke device
- ❌ **TIDAK menyimpan history message**

Jadi **tidak ada UI di Firebase Console** untuk melihat message yang sudah terkirim.

---

## 🔍 **Cara Monitoring FCM Notifications:**

### **Option 1: Firebase Console - Analytics Only** 📈

Firebase Console **HANYA menampilkan analytics**, bukan message detail.

**Cara akses:**
1. Buka: https://console.firebase.google.com
2. Pilih project: **mypengaduan**
3. Sidebar → **Cloud Messaging**
4. Tab → **Reports**

**Yang bisa dilihat:**
- ✅ Total impressions (berapa notif ditampilkan)
- ✅ Opens (berapa kali user buka notif)
- ✅ Delivery rate (persentase terkirim)
- ❌ **Detail message content** (TIDAK ADA)
- ❌ **Recipient list** (TIDAK ADA)
- ❌ **Timestamp per message** (TIDAK ADA)

**Screenshot locations:**
```
Firebase Console
└── Cloud Messaging
    ├── Reports (aggregated stats only)
    ├── Topics (topic-based messaging)
    └── Send test message (manual testing)
```

---

## ✅ **Cara Terbaik: Laravel Backend Logging**

Karena FCM tidak menyimpan history, **solusi terbaik adalah logging di backend Laravel**.

### **1. Check Laravel Logs** 📝

Semua notification sudah **otomatis tercatat** di logs!

**Cara cek:**
```bash
# Real-time monitoring
tail -f storage/logs/laravel.log

# Filter notifikasi saja
tail -f storage/logs/laravel.log | grep -i notification

# Filter by type
tail -f storage/logs/laravel.log | grep "Complaint notification"
tail -f storage/logs/laravel.log | grep "Status change notification"
tail -f storage/logs/laravel.log | grep "Announcement notification"

# Lihat 50 baris terakhir
tail -50 storage/logs/laravel.log | grep notification
```

**Log format yang sudah ada:**
```
[2025-10-22 14:00:00] local.INFO: Complaint notification sent to admin
[2025-10-22 14:01:00] local.INFO: Status change notification sent to user: user_id=5
[2025-10-22 14:02:00] local.INFO: Announcement notification sent to all users. total_users: 10, total_devices: 15
```

---

### **2. Database Notification Table** (Optional Enhancement)

Jika mau **simpan history** notifikasi, bisa tambah table baru:

**Migration:**
```php
// database/migrations/2025_10_22_create_notification_logs_table.php
Schema::create('notification_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type'); // complaint_created, status_changed, announcement_created
    $table->string('title');
    $table->text('body');
    $table->json('data')->nullable();
    $table->enum('status', ['sent', 'failed', 'opened'])->default('sent');
    $table->timestamp('sent_at');
    $table->timestamp('opened_at')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'created_at']);
    $table->index('type');
});
```

**Update FirebaseService untuk logging:**
```php
// app/Services/FirebaseService.php

public function sendToDevice($token, $title, $body, $data = [])
{
    if (!$this->isConfigured) {
        Log::warning('Firebase not configured, skipping notification');
        return;
    }

    try {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $this->messaging->send($message);
        
        // ✅ Log ke database
        if (isset($data['user_id'])) {
            NotificationLog::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'] ?? 'unknown',
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }
        
        Log::info("FCM notification sent", ['token' => substr($token, 0, 20) . '...', 'title' => $title]);
    } catch (Exception $e) {
        Log::error("FCM send failed: " . $e->getMessage());
        
        // ✅ Log error ke database
        if (isset($data['user_id'])) {
            NotificationLog::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'] ?? 'unknown',
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'status' => 'failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
```

**Query untuk cek history:**
```sql
-- Notifikasi terkirim hari ini
SELECT * FROM notification_logs 
WHERE DATE(sent_at) = CURDATE() 
ORDER BY sent_at DESC;

-- Notifikasi per user
SELECT * FROM notification_logs 
WHERE user_id = 5 
ORDER BY sent_at DESC 
LIMIT 20;

-- Notifikasi yang dibuka
SELECT * FROM notification_logs 
WHERE status = 'opened' 
ORDER BY opened_at DESC;

-- Delivery rate
SELECT 
    type,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
    ROUND(SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as success_rate
FROM notification_logs
GROUP BY type;
```

---

### **3. Queue Job Monitoring** 📊

Monitor queue jobs untuk tahu notifikasi sedang diproses:

```bash
# Check pending jobs
php artisan tinker --execute="echo 'Pending: ' . \DB::table('jobs')->count();"

# Check failed jobs
php artisan queue:failed

# Monitor queue real-time
php artisan queue:monitor

# Check jobs table
php artisan tinker
>>> DB::table('jobs')->orderBy('id', 'desc')->limit(5)->get();
```

---

### **4. Firebase Admin SDK Logs** 🔥

Laravel sudah log semua FCM requests automatically:

**Check logs:**
```bash
# All FCM logs
tail -f storage/logs/laravel.log | grep -i fcm

# Success logs
tail -f storage/logs/laravel.log | grep "FCM notification sent"

# Error logs
tail -f storage/logs/laravel.log | grep "FCM send failed"

# Invalid token logs
tail -f storage/logs/laravel.log | grep "Invalid FCM token"
```

---

## 🧪 **Testing & Debugging:**

### **Test 1: Send Manual Test Message** 📤

**Via Firebase Console:**
1. Go to: https://console.firebase.google.com
2. Select project: **mypengaduan**
3. Sidebar → **Cloud Messaging**
4. Click: **"Send test message"**
5. Enter FCM token (dari mobile app)
6. Fill notification:
   - Title: "Test Notifikasi"
   - Body: "Testing dari Firebase Console"
7. Click **"Test"**

**Via Laravel Tinker:**
```bash
php artisan tinker

# Get user with devices
$user = User::find(1);
$tokens = $user->getActiveDeviceTokens();

# Send test notification
$firebaseService = app(\App\Services\FirebaseService::class);
$firebaseService->sendToUser(
    $user->id,
    '🧪 Test Notification',
    'Testing dari Laravel Tinker'
);

# Check logs
exit
tail -5 storage/logs/laravel.log
```

---

### **Test 2: Trigger Real Events** 🔔

**Test Admin Notification:**
```bash
# Create complaint via API
curl -X POST http://localhost/api/complaints \
  -H "Authorization: Bearer USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Keluhan",
    "description": "Testing notification",
    "category_id": 1,
    "location": "RT 05"
  }'

# Check queue processed
php artisan queue:work --once

# Check logs
tail -f storage/logs/laravel.log | grep "Complaint notification"
```

**Expected log:**
```
[2025-10-22 14:15:30] local.INFO: Complaint notification sent to admin
```

---

### **Test 3: Check Mobile App Received** 📱

**Mobile app harus implement logging:**

**Flutter example:**
```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('🔔 Notification received!');
  print('Title: ${message.notification?.title}');
  print('Body: ${message.notification?.body}');
  print('Data: ${message.data}');
  
  // Log to backend (optional)
  http.post(
    Uri.parse('$baseUrl/api/notifications/track-received'),
    body: jsonEncode({
      'notification_id': message.data['notification_id'],
      'received_at': DateTime.now().toIso8601String(),
    }),
  );
});
```

---

## 📈 **Monitoring Dashboard (Future Enhancement)**

Jika mau dashboard monitoring yang lengkap, bisa implement:

### **Admin Dashboard UI:**

**Example query untuk stats:**
```php
// app/Http/Controllers/Admin/NotificationController.php

public function dashboard()
{
    $stats = [
        'today' => NotificationLog::whereDate('sent_at', today())->count(),
        'week' => NotificationLog::whereBetween('sent_at', [now()->startOfWeek(), now()])->count(),
        'month' => NotificationLog::whereMonth('sent_at', now()->month)->count(),
        'success_rate' => NotificationLog::where('status', 'sent')->count() / NotificationLog::count() * 100,
        'by_type' => NotificationLog::groupBy('type')->selectRaw('type, COUNT(*) as total')->get(),
        'recent' => NotificationLog::latest()->limit(10)->get(),
    ];
    
    return view('admin.notifications.dashboard', compact('stats'));
}
```

**UI Features:**
- 📊 Charts (notifikasi per hari/minggu/bulan)
- 📈 Delivery rate
- 📱 Device distribution (Android vs iOS)
- 👥 Top recipients
- 🔔 Recent notifications
- ❌ Failed notifications

---

## 🎯 **Best Practices:**

### **1. Log Everything** 📝
```php
// Always log in Listeners
Log::info('Sending notification', [
    'type' => 'complaint_created',
    'user_id' => $userId,
    'device_count' => count($tokens),
]);
```

### **2. Use Structured Logging** 🏗️
```php
Log::channel('notifications')->info('FCM sent', [
    'user_id' => $userId,
    'type' => 'status_changed',
    'title' => $title,
    'sent_at' => now(),
    'success' => true,
]);
```

### **3. Monitor Failed Jobs** ⚠️
```bash
# Daily check
php artisan queue:failed | mail -s "Failed Notification Jobs" admin@example.com

# Auto-retry
php artisan queue:retry all
```

### **4. Alert on High Failure Rate** 🚨
```php
// In AppServiceProvider or custom command
$failureRate = NotificationLog::where('status', 'failed')
    ->whereDate('sent_at', today())
    ->count() / NotificationLog::whereDate('sent_at', today())->count();

if ($failureRate > 0.1) { // > 10%
    Mail::to('admin@example.com')->send(new HighFailureRateAlert($failureRate));
}
```

---

## 🔧 **Quick Commands:**

```bash
# Monitor all notifications
tail -f storage/logs/laravel.log | grep -i notification

# Count today's notifications
grep "notification sent" storage/logs/laravel-$(date +%Y-%m-%d).log | wc -l

# Check failed notifications
grep "FCM send failed" storage/logs/laravel.log

# Monitor queue worker
watch -n 2 'php artisan queue:monitor'

# Check database (if implemented)
php artisan tinker --execute="echo 'Today: ' . \App\Models\NotificationLog::whereDate('sent_at', today())->count();"
```

---

## 📊 **Summary:**

| Method | Pros | Cons | Best For |
|--------|------|------|----------|
| **Firebase Console** | Official UI | Only analytics, no details | Quick overview |
| **Laravel Logs** | Already implemented | Manual search | Debugging |
| **Database Logs** | Complete history, queryable | Need to implement | Production monitoring |
| **Queue Monitoring** | Real-time | Only pending jobs | Development |
| **Custom Dashboard** | Visual, comprehensive | Takes time to build | Management |

---

## ✅ **Recommendation:**

**For Now (Quick):**
1. ✅ Use Laravel logs: `tail -f storage/logs/laravel.log | grep notification`
2. ✅ Monitor queue: `php artisan queue:monitor`
3. ✅ Check mobile app directly

**For Production (Better):**
1. 📊 Implement `notification_logs` table
2. 🎨 Build admin dashboard
3. 🚨 Set up alerts for failures
4. 📈 Track analytics (delivery rate, open rate)

---

**Mau implement database logging sekarang? Atau cukup pakai logs dulu?** 🤔
