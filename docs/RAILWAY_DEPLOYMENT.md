# 🚂 Deployment ke Railway - Laravel Queue Worker

## 📋 Overview

Railway support multiple services dalam satu project. Untuk Laravel dengan queue worker, kita perlu:
1. **Web Service** - Serve aplikasi Laravel
2. **Worker Service** - Menjalankan queue worker untuk notifikasi

---

## 🔧 Setup Railway Project

### 1. **Buat 2 Services di Railway**

Railway Dashboard → New Project → Deploy from GitHub

**Service 1: Web (Main Application)**
- Service Name: `mypengaduan-web`
- Build dari repository GitHub Anda
- Akan otomatis detect Laravel

**Service 2: Worker (Queue Worker)**
- Service Name: `mypengaduan-worker`
- Deploy dari repository yang SAMA
- Custom start command untuk worker

---

## ⚙️ Konfigurasi Web Service

### Environment Variables (Web):

```env
# Application
APP_NAME=MyPengaduan
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Database (Railway akan auto-provide jika pakai Railway Database)
DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

# Queue Configuration
QUEUE_CONNECTION=database
QUEUE_DRIVER=database

# Firebase Notifications
FIREBASE_CREDENTIALS=firebase-credentials.json
FIREBASE_PROJECT_ID=your-project-id

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=database

# Filesystem
FILESYSTEM_DISK=public

# Mail Configuration (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Other
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### Custom Start Command (Web):
```bash
php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## 👷 Konfigurasi Worker Service

### Settings untuk Worker Service:

**1. Root Directory:** (sama dengan web service)
```
/
```

**2. Custom Start Command:**
```bash
php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3 --daemon
```

**3. Environment Variables (Worker):**

Copy SEMUA environment variables dari Web Service ke Worker Service.

**PENTING:** Worker service perlu akses ke:
- Database yang sama (untuk baca queue jobs)
- Firebase credentials (untuk kirim notifikasi)
- App Key yang sama

```env
# Copy ALL variables from Web Service, then add:

# Worker Specific
QUEUE_CONNECTION=database
QUEUE_DRIVER=database

# Optional: Increase memory for worker
PHP_MEMORY_LIMIT=256M
```

**4. Service Configuration:**

- ✅ **Restart Policy**: ON_FAILURE
- ✅ **Health Check**: Disabled (worker tidak ada HTTP endpoint)
- ✅ **Replica**: 1 (cukup 1 worker untuk start)

---

## 📁 Upload Firebase Credentials

Railway tidak support file upload via dashboard, jadi gunakan salah satu cara:

### Option 1: Base64 Environment Variable (RECOMMENDED)

**Di local machine:**
```bash
# Convert JSON ke base64
base64 -w 0 storage/app/firebase-credentials.json
```

**Di Railway Environment Variables:**
```env
FIREBASE_CREDENTIALS_BASE64=ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsC...
```

**Update code untuk decode (di FirebaseService.php):**
```php
// Check if using base64 encoded credentials
if ($base64Creds = env('FIREBASE_CREDENTIALS_BASE64')) {
    $credentials = base64_decode($base64Creds);
    $tempFile = tempnam(sys_get_temp_dir(), 'firebase_');
    file_put_contents($tempFile, $credentials);
    $credentialsPath = $tempFile;
} else {
    $credentialsPath = storage_path('app/' . env('FIREBASE_CREDENTIALS'));
}
```

### Option 2: Commit ke Repository (NOT RECOMMENDED for security)

```bash
# Add firebase credentials to git (ONLY if private repo)
git add storage/app/firebase-credentials.json -f
git commit -m "Add firebase credentials"
git push
```

⚠️ **SECURITY WARNING**: Jangan commit credentials ke public repository!

---

## 🚀 Deployment Steps

### Step 1: Push Code ke GitHub
```bash
git add .
git commit -m "Setup Railway deployment with worker"
git push origin main
```

### Step 2: Create Railway Project
1. Login ke [Railway.app](https://railway.app)
2. New Project → Deploy from GitHub repo
3. Select repository: `mypengaduan`

### Step 3: Setup Web Service
1. Railway akan auto-detect Laravel
2. Add MySQL Database (jika belum ada)
3. Add all environment variables
4. Custom Start Command:
   ```bash
   php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT
   ```
5. Deploy!

### Step 4: Setup Worker Service
1. New Service → Deploy from GitHub (same repo)
2. Service Name: `mypengaduan-worker`
3. **Custom Start Command:**
   ```bash
   php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3 --daemon
   ```
4. Copy ALL environment variables dari Web Service
5. Deploy!

### Step 5: Verify Worker Running
Check logs di Railway Dashboard:
- Worker service logs harus show: `Processing: App\Jobs\...`
- Tidak ada error messages

---

## 📊 Monitoring Queue Worker

### Check Worker Logs di Railway:

```
[timestamp] Processing: App\Listeners\SendComplaintNotificationToAdmin
[timestamp] Processed:  App\Listeners\SendComplaintNotificationToAdmin
```

### Jika Worker Crash/Stop:

Railway akan auto-restart karena `restartPolicyType: ON_FAILURE`

### Check Failed Jobs:

```bash
# Connect ke Railway via Railway CLI
railway run php artisan queue:failed

# Retry failed jobs
railway run php artisan queue:retry all
```

---

## 🔍 Troubleshooting

### Worker tidak memproses jobs?

**Cek 1: Worker Service Running?**
- Railway Dashboard → Worker Service → Logs
- Harus show "queue:work" command running

**Cek 2: Database Connection?**
- Worker HARUS punya access ke database yang sama
- Check DB_* environment variables

**Cek 3: Queue Jobs exist?**
```sql
SELECT COUNT(*) FROM jobs;
```

**Cek 4: Failed Jobs?**
```sql
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

### Notifikasi tidak terkirim?

**Cek 1: Firebase Credentials?**
- Worker logs harus show "Firebase initialized successfully"
- Jika "Firebase not configured", cek FIREBASE_CREDENTIALS_BASE64

**Cek 2: Device Tokens?**
```sql
SELECT COUNT(*) FROM user_devices WHERE is_active = 1;
```

**Cek 3: Queue Connection?**
- QUEUE_CONNECTION harus "database"
- Bukan "sync" atau "redis"

---

## 💰 Railway Pricing

**Free Plan:**
- $5 credit per month
- Cukup untuk testing
- Auto-sleep after inactivity

**Hobby Plan ($5/month):**
- $5 credit included
- No sleep
- Perfect untuk production kecil

**Worker Service:**
- Shared CPU/Memory dengan Web Service
- Tidak perlu separate billing

---

## 🔄 Alternative: Single Service dengan Supervisor

Jika ingin hemat resource, jalankan worker dalam web service yang sama:

### Install Supervisor (via nixpacks.toml):

```toml
[phases.setup]
nixPkgs = ['php82', 'nginx', 'supervisor']

[phases.build]
cmds = ['composer install --optimize-autoloader --no-dev']

[start]
cmd = 'supervisord -c /path/to/supervisord.conf'
```

### Create supervisord.conf:

```ini
[supervisord]
nodaemon=true

[program:php-fpm]
command=php artisan serve --host=0.0.0.0 --port=$PORT
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stderr_logfile=/dev/stderr

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
stdout_logfile=/dev/stdout
stderr_logfile=/dev/stderr
```

---

## ✅ Checklist Deployment

### Before Deploy:
- [ ] Push code ke GitHub
- [ ] Firebase credentials ready (base64 or file)
- [ ] Database migrations tested
- [ ] .env variables documented

### Railway Setup:
- [ ] Web service deployed
- [ ] Worker service deployed
- [ ] Database connected
- [ ] Environment variables configured
- [ ] Firebase credentials uploaded
- [ ] Custom start commands configured

### After Deploy:
- [ ] Web service accessible
- [ ] Worker service running (check logs)
- [ ] Test create complaint → notif ke admin
- [ ] Test update status → notif ke user
- [ ] Check failed jobs (should be 0)

---

## 📱 Test Notifications in Production

### 1. Register Device Token:
```bash
curl -X POST https://your-app.railway.app/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "fcm_token_from_app",
    "device_name": "iPhone 13",
    "device_type": "ios"
  }'
```

### 2. Create Test Complaint:
```bash
curl -X POST https://your-app.railway.app/api/complaints \
  -H "Authorization: Bearer USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Notification",
    "description": "Testing queue worker",
    "category_id": 1,
    "location": "Railway Test",
    "priority": "medium"
  }'
```

### 3. Check Worker Logs:
Railway Dashboard → Worker Service → Logs → Should see:
```
Processing: App\Listeners\SendComplaintNotificationToAdmin
Processed:  App\Listeners\SendComplaintNotificationToAdmin
```

### 4. Check Database:
```sql
SELECT * FROM fcm_notifications 
WHERE type = 'complaint_created' 
ORDER BY id DESC LIMIT 1;
```

---

## 🎯 Quick Commands

```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link to project
railway link

# View logs
railway logs

# Run commands on Railway
railway run php artisan queue:failed
railway run php artisan queue:retry all
railway run php artisan tinker

# SSH into service (if needed)
railway shell
```

---

## 📚 References

- [Railway Docs - Laravel](https://docs.railway.app/guides/laravel)
- [Railway Docs - Multiple Services](https://docs.railway.app/develop/services)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Supervisor Documentation](http://supervisord.org/)

---

## 🆘 Support

Jika masih ada masalah:
1. Check Railway logs (Web & Worker)
2. Check `storage/logs/laravel.log` via Railway CLI
3. Run: `railway run php test-notifications.php`
4. Check database jobs & failed_jobs tables

**Happy Deploying! 🚀**
