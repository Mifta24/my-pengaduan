# Railway Worker Setup Guide

## Problem
Notifikasi tidak terkirim karena queue worker tidak berjalan di Railway.

## Solution: Setup Worker Service di Railway

### Step 1: Create Worker Service
1. Buka Railway dashboard: https://railway.app/
2. Pilih project **mypengaduan**
3. Click **+ New Service**
4. Pilih **GitHub Repo** yang sama
5. Nama service: **mypengaduan-worker**

### Step 2: Configure Worker Service
Di Worker Service settings:

#### Variables Tab
Copy semua environment variables dari Web service, tambahkan:
```bash
# Wajib sama dengan Web service
DATABASE_URL=postgresql://...
FIREBASE_CREDENTIALS_BASE64=...
APP_KEY=...
APP_URL=...

# Queue configuration
QUEUE_CONNECTION=database
```

#### Settings Tab
- **Start Command**:
  ```bash
  php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3
  ```

- **Build Command**: (kosongkan, karena sudah di-build oleh web service)

- **Root Directory**: `/` (sama dengan web service)

### Step 3: Deploy
1. Click **Deploy** pada worker service
2. Tunggu sampai status menjadi **Active**

### Step 4: Verify Worker is Running
Check logs di Railway dashboard worker service, harus melihat:
```
INFO  Processing: App\Listeners\SendAnnouncementNotificationToAll
INFO  Processed:  App\Listeners\SendAnnouncementNotificationToAll
```

## Testing Notifications

### 1. Test via API (Postman/Insomnia)
```bash
POST https://mypengaduan.miftahaldi.my.id/api/admin/announcements
Authorization: Bearer YOUR_TOKEN

Body (form-data):
title: Test Pengumuman
content: Ini test notification
priority: high
is_active: 1
```

### 2. Test via Web
1. Login as admin
2. Buat pengumuman baru
3. Publish

### 3. Check Queue Jobs
```bash
# Di terminal lokal atau Railway shell
php artisan queue:work --once

# Lihat jobs yang pending
php artisan queue:failed
```

### 4. Check FCM Notifications Table
```sql
SELECT * FROM fcm_notifications 
WHERE type = 'announcement_created' 
ORDER BY created_at DESC 
LIMIT 10;
```

## Troubleshooting

### Worker tidak processing jobs
- Pastikan `QUEUE_CONNECTION=database` di environment variables
- Cek tabel `jobs` di database, jika ada jobs tapi tidak terproses berarti worker belum jalan
- Restart worker service di Railway

### Notification tidak sampai ke device
- Cek tabel `fcm_notifications`, jika data ada berarti listener jalan
- Cek Firebase console untuk delivery status
- Pastikan device token valid (belum expired)

### Check if queue has pending jobs
```sql
SELECT * FROM jobs ORDER BY created_at DESC;
```

## Important Notes
- Worker service HARUS menggunakan database yang sama dengan web service
- Worker service HARUS memiliki FIREBASE_CREDENTIALS_BASE64 yang sama
- Jangan gunakan `--daemon` flag di Railway, use `--sleep=3` saja
- Worker akan auto-restart jika crash (Railway default behavior)
