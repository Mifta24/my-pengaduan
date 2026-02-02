# 🔔 Panduan Troubleshooting Notifikasi Firebase

## ✅ MASALAH YANG SUDAH DIPERBAIKI

### 1. Event ComplaintCreated Tidak Di-Dispatch
**Status**: ✅ **FIXED**

**Masalah**: 
- Kode ada komentar "Dispatch event to send notification to admins" tapi tidak ada `event()` call
- Notifikasi tidak terkirim saat user membuat complaint baru

**Solusi yang Diterapkan**:
```php
// Di ComplaintController::store()
event(new ComplaintCreated($complaint));
```

---

## 🔍 CHECKLIST TROUBLESHOOTING

Jika notifikasi masih tidak muncul, cek hal-hal berikut:

### 1. ✅ Queue Worker Berjalan
Karena semua listener menggunakan `ShouldQueue`, Anda HARUS menjalankan queue worker:

```bash
# Development
php artisan queue:work

# Production (dengan supervisor)
php artisan queue:work --daemon --tries=3
```

**Cara Cek**:
```bash
# Cek apakah ada jobs di queue
php artisan queue:failed

# Restart queue worker jika ada perubahan
php artisan queue:restart
```

---

### 2. ✅ Firebase Configuration

Cek file `.env`:
```env
FIREBASE_CREDENTIALS=/path/to/firebase-credentials.json
FIREBASE_DATABASE_URL=https://your-project.firebaseio.com
```

**Test Firebase Connection**:
```bash
php artisan tinker
>>> app(\App\Services\FirebaseService::class)
```

---

### 3. ✅ User Punya Device Token

User/Admin harus register device token dulu via API:

```bash
POST /api/device-tokens
Authorization: Bearer {token}

{
  "device_token": "fcm_token_here",
  "device_name": "Samsung Galaxy S21",
  "device_type": "android"
}
```

**Cara Cek di Database**:
```sql
-- Cek device tokens user
SELECT * FROM user_devices WHERE user_id = 1;

-- Cek admin yang punya device
SELECT u.id, u.name, u.email, COUNT(ud.id) as device_count
FROM users u
LEFT JOIN user_devices ud ON u.id = ud.user_id
INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'admin'
GROUP BY u.id, u.name, u.email;
```

---

### 4. ✅ Notification Settings Enabled

Cek apakah user/admin tidak disable notifikasi:

```sql
-- Cek notification settings
SELECT * FROM notification_settings WHERE user_id = 1;
```

Default settings (jika belum ada record):
```php
complaint_created = true
complaint_status_changed = true
complaint_response = true
announcement_created = true
```

---

### 5. ✅ Event Service Provider Registered

Cek `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\EventServiceProvider::class,
],
```

**Refresh config**:
```bash
php artisan config:clear
php artisan cache:clear
php artisan event:clear
```

---

### 6. ✅ Database Table Exists

Pastikan tabel berikut ada:
- `user_devices` - Menyimpan FCM tokens
- `fcm_notifications` - Log notifikasi yang dikirim
- `notification_settings` - User preferences

**Create tables jika belum ada**:
```bash
php artisan migrate
```

---

## 🧪 TESTING MANUAL

### Test 1: User Buat Complaint → Notif ke Admin

```bash
# 1. Login sebagai user
POST /api/auth/login
{
  "email": "user@test.com",
  "password": "password"
}

# 2. Buat complaint
POST /api/complaints
Authorization: Bearer {user_token}
{
  "title": "Test Complaint",
  "description": "Testing notification",
  "category_id": 1,
  "location": "Test Location",
  "priority": "medium"
}

# 3. Cek queue jobs
php artisan queue:work -v

# 4. Cek database
SELECT * FROM fcm_notifications WHERE type = 'complaint_created' ORDER BY id DESC LIMIT 1;
```

---

### Test 2: Admin Update Status → Notif ke User

```bash
# 1. Login sebagai admin
POST /api/auth/login
{
  "email": "admin@test.com",
  "password": "password"
}

# 2. Update status complaint
PATCH /api/admin/complaints/1/status
Authorization: Bearer {admin_token}
{
  "status": "in_progress"
}

# 3. Cek notifikasi user
SELECT * FROM fcm_notifications 
WHERE type = 'complaint_status_changed' 
AND user_id = {complaint_user_id}
ORDER BY id DESC LIMIT 1;
```

---

### Test 3: Admin Beri Response → Notif ke User

```bash
POST /api/admin/complaints/1/response
Authorization: Bearer {admin_token}
{
  "message": "Sedang kami proses"
}

# Cek notifikasi
SELECT * FROM fcm_notifications 
WHERE type = 'complaint_response'
ORDER BY id DESC LIMIT 1;
```

---

### Test 4: Admin Buat Announcement → Notif ke Semua

```bash
POST /api/admin/announcements
Authorization: Bearer {admin_token}
{
  "title": "Test Announcement",
  "content": "Testing notification to all users",
  "priority": "high"
}

# Cek berapa user yang dapat notifikasi
SELECT COUNT(*) FROM fcm_notifications 
WHERE type = 'announcement_created' 
AND created_at > NOW() - INTERVAL 1 MINUTE;
```

---

## 🐛 DEBUG MODE

Enable debug logging di `.env`:
```env
LOG_LEVEL=debug
APP_DEBUG=true
```

Monitor logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 📊 MONITORING

### Cek Statistik Notifikasi

```sql
-- Total notifikasi per type
SELECT type, COUNT(*) as total, COUNT(CASE WHEN is_read THEN 1 END) as read_count
FROM fcm_notifications
GROUP BY type;

-- Notifikasi yang gagal terkirim (check logs)
SELECT * FROM jobs WHERE queue = 'default' AND available_at > UNIX_TIMESTAMP();

-- Failed jobs
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

---

## 🚀 PRODUCTION CHECKLIST

- [ ] Queue worker running (via Supervisor)
- [ ] Firebase credentials file uploaded
- [ ] All users have registered device tokens
- [ ] Notification settings table seeded with defaults
- [ ] Logs monitored for errors
- [ ] Failed jobs checked regularly
- [ ] Database indexes optimized

---

## 💡 TIPS

1. **Gunakan Queue Worker di Production**
   ```bash
   # Install supervisor
   sudo apt-get install supervisor
   
   # Config di /etc/supervisor/conf.d/laravel-worker.conf
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   numprocs=3
   ```

2. **Test Firebase Token di Postman**
   Gunakan Firebase Cloud Messaging API untuk test langsung

3. **Create Dummy Device Token untuk Testing**
   ```sql
   INSERT INTO user_devices (user_id, device_token, device_name, device_type, is_active)
   VALUES (1, 'test_token_123', 'Test Device', 'android', 1);
   ```

4. **Monitor Queue Performance**
   ```bash
   php artisan queue:monitor
   ```

---

## ❓ FAQ

**Q: Notifikasi tidak muncul di app mobile?**
A: 
1. Cek FCM token valid
2. Pastikan app foreground/background handling
3. Test dengan Firebase Console Send Test Message

**Q: Notifikasi masuk database tapi tidak terkirim?**
A: Queue worker mungkin tidak berjalan. Jalankan `php artisan queue:work`

**Q: Admin tidak dapat notifikasi?**
A: Cek:
1. User punya role 'admin'
2. Admin punya device token aktif
3. Admin tidak disable notification settings

**Q: Notifikasi duplicate/berulang?**
A: 
1. Jangan jalankan multiple queue worker di development
2. Check event tidak di-dispatch berkali-kali

---

## 📞 SUPPORT

Jika masih ada masalah, cek:
- `storage/logs/laravel.log` untuk error details
- Firebase Console untuk delivery status
- Database `fcm_notifications` untuk history
