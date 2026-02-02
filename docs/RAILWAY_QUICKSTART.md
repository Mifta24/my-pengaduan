# 🚀 Quick Start: Deploy ke Railway dengan Queue Worker

## 📋 Prerequisites

- ✅ GitHub account
- ✅ Railway account (https://railway.app)
- ✅ Firebase service account JSON
- ✅ Laravel project ready

---

## 🚂 Langkah-langkah Deployment

### 1️⃣ Persiapan Firebase Credentials

```bash
# Convert Firebase JSON ke Base64
php convert-firebase-to-base64.php

# Output akan disimpan di: firebase-base64.txt
```

Copy base64 string untuk nanti dipakai di Railway.

---

### 2️⃣ Push ke GitHub

```bash
git add .
git commit -m "Setup Railway deployment with queue worker"
git push origin main
```

---

### 3️⃣ Deploy Web Service ke Railway

1. Login ke [Railway.app](https://railway.app)
2. **New Project** → **Deploy from GitHub repo**
3. Select repository: `mypengaduan`
4. Service akan auto-deploy

#### Environment Variables (Web):

Klik **Variables** tab, tambahkan:

```env
# Application
APP_NAME=MyPengaduan
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false

# Queue (PENTING!)
QUEUE_CONNECTION=database

# Firebase
FIREBASE_CREDENTIALS_BASE64=your-base64-string-here

# Database akan auto-connect jika pakai Railway Database
```

#### Custom Start Command (Web):

Settings → Start Command:
```bash
php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

### 4️⃣ Deploy Worker Service

1. Dari Railway Dashboard → **New Service**
2. **Deploy from GitHub** → Select repository yang sama
3. Service Name: `mypengaduan-worker`

#### Environment Variables (Worker):

**COPY SEMUA** variables dari Web Service, lalu pastikan ada:

```env
QUEUE_CONNECTION=database
FIREBASE_CREDENTIALS_BASE64=your-base64-string-here

# Plus semua DB_* variables dari Web Service
```

#### Custom Start Command (Worker):

Settings → Start Command:
```bash
php artisan queue:work --verbose --tries=3 --timeout=90
```

#### Service Settings:

- **Health Check Path**: (kosongkan/disable)
- **Restart Policy**: On Failure
- **Region**: Same as Web Service

---

### 5️⃣ Setup Database

**Option A: Railway Provided Database**

1. New → Database → MySQL
2. Connect ke Web & Worker services
3. Variables akan auto-populate

**Option B: External Database**

Manually add:
```env
DB_CONNECTION=mysql
DB_HOST=your-host
DB_PORT=3306
DB_DATABASE=your-db
DB_USERNAME=your-user
DB_PASSWORD=your-pass
```

---

## ✅ Verifikasi Deployment

### Check Web Service:

1. Railway Dashboard → Web Service → **Deployments**
2. Status harus **Success**
3. Click domain URL → Should see Laravel app

### Check Worker Service:

1. Railway Dashboard → Worker Service → **Deployments**
2. Status harus **Success**
3. Click **View Logs** → Should see:

```
[INFO] Processing jobs...
```

Jika ada job, akan show:
```
[timestamp] Processing: App\Listeners\SendComplaintNotificationToAdmin
[timestamp] Processed:  App\Listeners\SendComplaintNotificationToAdmin
```

---

## 🧪 Test Notifikasi

### 1. Register Device Token

```bash
curl -X POST https://your-app.railway.app/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "fcm_token_here",
    "device_name": "Test Device",
    "device_type": "android"
  }'
```

### 2. Create Complaint (as User)

```bash
curl -X POST https://your-app.railway.app/api/complaints \
  -H "Authorization: Bearer USER_TOKEN" \
  -F "title=Test Notifikasi" \
  -F "description=Testing queue worker di Railway" \
  -F "category_id=1" \
  -F "location=Railway" \
  -F "priority=medium"
```

### 3. Check Worker Logs

Railway Dashboard → Worker Service → View Logs

Harus muncul:
```
Processing: App\Listeners\SendComplaintNotificationToAdmin
Processed:  App\Listeners\SendComplaintNotificationToAdmin
```

### 4. Check Database

```bash
# Install Railway CLI
npm i -g @railway/cli

# Login & link
railway login
railway link

# Run query
railway run php artisan tinker
>>> DB::table('fcm_notifications')->orderBy('id', 'desc')->first();
```

---

## 🐛 Troubleshooting

### Worker tidak jalan?

**Check Logs:**
```bash
railway logs --service mypengaduan-worker
```

**Restart Worker:**
```bash
railway restart --service mypengaduan-worker
```

### Notifikasi tidak terkirim?

**Check Firebase:**
```bash
railway run --service mypengaduan-worker php artisan tinker
>>> app(\App\Services\FirebaseService::class)->isConfigured()
# Harus return: true
```

**Check Queue Jobs:**
```bash
railway run php artisan queue:failed
```

### Database connection error?

Pastikan Worker Service punya akses ke DB yang sama dengan Web Service.

Check variables: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## 💡 Pro Tips

### 1. Monitor Logs Real-time

```bash
railway logs --service mypengaduan-worker --follow
```

### 2. Scale Worker (jika perlu)

Railway Dashboard → Worker Service → Settings → **Replicas**: 2

### 3. Check Performance

Railway Dashboard → Metrics tab → Monitor CPU/Memory

### 4. Auto-restart on Failure

Default sudah ON. Worker akan auto-restart jika crash.

---

## 📊 Cost Estimate

**Free Plan:**
- $5 credit/month
- Good for testing
- Sleeps after inactivity

**Starter Plan ($5/month):**
- $5 credit included
- No sleep
- Enough untuk 2 services (Web + Worker)

**Estimated Usage:**
- Web Service: ~$3-4/month
- Worker Service: ~$1-2/month
- Database: $2-3/month (if Railway DB)

**Total: ~$6-9/month** (Starter plan recommended)

---

## 🎯 Checklist

### Before Deploy:
- [x] Firebase credentials converted to base64
- [x] Code pushed to GitHub
- [x] Database migrations ready
- [x] Environment variables documented

### Railway Setup:
- [x] Web service deployed
- [x] Worker service deployed
- [x] Database connected (Railway DB or external)
- [x] All environment variables set
- [x] Custom start commands configured

### After Deploy:
- [x] Web service accessible (check domain)
- [x] Worker service running (check logs)
- [x] Database connected (run migrations)
- [x] Firebase configured (check logs)
- [x] Test create complaint → admin gets notif
- [x] Test update status → user gets notif

---

## 📚 Resources

- 📖 [Full Documentation](RAILWAY_DEPLOYMENT.md)
- 🔧 [Troubleshooting Guide](NOTIFICATION_TROUBLESHOOTING.md)
- 🚂 [Railway Docs](https://docs.railway.app)
- 🔥 [Firebase Console](https://console.firebase.google.com)

---

## 🆘 Need Help?

1. Check worker logs: `railway logs --service mypengaduan-worker`
2. Check failed jobs: `railway run php artisan queue:failed`
3. Test Firebase: `railway run php test-notifications.php`
4. Restart services: `railway restart`

**Happy Deploying! 🎉**
