# 🚂 Railway Deployment - Queue Worker Setup

## 📁 File yang Ditambahkan

### Configuration Files:

- **`Procfile`** - Definisi web & worker processes untuk Railway
- **`railway.json`** - Konfigurasi build & deploy Railway
- **`.gitignore`** - Updated untuk exclude Firebase credentials

### Helper Scripts:

- **`convert-firebase-to-base64.php`** - Convert Firebase JSON ke base64
- **`test-notifications.php`** - Test sistem notifikasi
- **`fix-notifications.php`** - Quick fix untuk setup notifikasi

### Documentation:

- **`docs/RAILWAY_DEPLOYMENT.md`** - Full deployment guide
- **`docs/RAILWAY_QUICKSTART.md`** - Quick start guide
- **`docs/NOTIFICATION_TROUBLESHOOTING.md`** - Troubleshooting notifikasi

---

## 🚀 Quick Start

### 1. Convert Firebase Credentials

```bash
php convert-firebase-to-base64.php
```

### 2. Deploy ke Railway

Ikuti: [docs/RAILWAY_QUICKSTART.md](docs/RAILWAY_QUICKSTART.md)

### 3. Setup 2 Services:

**Web Service:**
- Start Command: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

**Worker Service:**
- Start Command: `php artisan queue:work --verbose --tries=3 --timeout=90`

---

## ✅ Yang Sudah Diperbaiki

1. ✅ Event `ComplaintCreated` sekarang di-dispatch
2. ✅ FirebaseService support base64 credentials
3. ✅ Procfile untuk Railway
4. ✅ Documentation lengkap
5. ✅ Testing tools

---

## 📚 Documentation

- 🚀 [Quick Start](docs/RAILWAY_QUICKSTART.md)
- 📖 [Full Deployment Guide](docs/RAILWAY_DEPLOYMENT.md)
- 🔧 [Troubleshooting](docs/NOTIFICATION_TROUBLESHOOTING.md)

---

## 🎯 Railway Setup Checklist

- [ ] Web Service deployed
- [ ] Worker Service deployed
- [ ] Database connected
- [ ] Environment variables configured
- [ ] Firebase credentials (base64) added
- [ ] Custom start commands set
- [ ] Tested notifications working

---

## 🔔 Notification Flow

```
User creates complaint
    ↓
Event: ComplaintCreated
    ↓
Queue Job: SendComplaintNotificationToAdmin
    ↓
Worker processes job
    ↓
Firebase sends notification to all admins
```

---

## 🛠️ Development vs Production

**Development (Local):**
```bash
# Run queue worker manually
php artisan queue:work -v
```

**Production (Railway):**
- Worker Service runs automatically
- Auto-restart on failure
- Separate service = better reliability

---

## 💡 Important Notes

1. **Queue Driver**: Harus `database` bukan `sync`
2. **Firebase Credentials**: Use base64 di Railway
3. **Worker Service**: Needs same DB access as Web Service
4. **Environment Variables**: Must be identical between Web & Worker

---

## 📊 Monitoring

**Railway Dashboard:**
- View logs per service
- Check CPU/Memory usage
- Monitor restart count

**Laravel Commands:**
```bash
railway run php artisan queue:failed
railway run php artisan queue:retry all
```

---

## 🆘 Support

Jika ada masalah:

1. Check logs: `railway logs --service mypengaduan-worker`
2. Test Firebase: `railway run php test-notifications.php`
3. Read: [docs/NOTIFICATION_TROUBLESHOOTING.md](docs/NOTIFICATION_TROUBLESHOOTING.md)

---

**Happy Coding! 🚀**
