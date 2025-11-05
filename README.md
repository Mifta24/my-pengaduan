# MyPengaduan - Sistem Pengaduan Masyarakat

Aplikasi web untuk manajemen pengaduan masyarakat dengan fitur lengkap untuk admin dan user, dilengkapi dengan REST API dan sistem notifikasi Firebase FCM.

---

## 🚀 Fitur Utama

### Web Application
- ✅ **Authentication & Authorization** (Spatie Laravel Permission)
- ✅ **User Management** - Registrasi, verifikasi email, verifikasi identitas
- ✅ **Complaint Management** - CRUD pengaduan dengan attachment
- ✅ **Category Management** - Kelola kategori pengaduan
- ✅ **Announcement System** - Pengumuman dengan prioritas dan sticky
- ✅ **Comment System** - Komentar di pengumuman (polymorphic)
- ✅ **Admin Dashboard** - Statistik lengkap sistem
- ✅ **User Dashboard** - Statistik personal user

### REST API (71 Endpoints)
- ✅ **Authentication API** - Login, register, profile management
- ✅ **User API** - 37 endpoints untuk user operations
- ✅ **Admin API** - 34 endpoints untuk admin operations
- ✅ **Sanctum Token Authentication** - Secure API authentication
- ✅ **Role-based Access Control** - Admin vs User permissions

### Firebase Notification System
- ✅ **FCM Integration** - Push notification ke mobile/web
- ✅ **Event-driven Notifications** - Auto-send pada event tertentu
- ✅ **Queue System** - Asynchronous notification processing
- ✅ **Monitoring Tools** - CLI tool untuk monitoring notifikasi

---

## 📋 Tech Stack

- **Framework**: Laravel 12.33.0
- **PHP**: 8.3.10
- **Database**: PostgreSQL (Railway)
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Queue**: Redis/Database
- **Notification**: Firebase Cloud Messaging (FCM)
- **Storage**: Local/Cloud Storage

---

## 📁 Dokumentasi Lengkap

### API Documentation
1. **[API_COMPLETION_SUMMARY.md](API_COMPLETION_SUMMARY.md)** - Dokumentasi lengkap 71 endpoints
2. **[API_TESTING_GUIDE.md](API_TESTING_GUIDE.md)** - Panduan testing dengan Postman/cURL
3. **[API_RINGKASAN_INDONESIA.md](API_RINGKASAN_INDONESIA.md)** - Ringkasan bahasa Indonesia

### Postman Collection
1. **[MyPengaduan_API.postman_collection.json](MyPengaduan_API.postman_collection.json)** - Collection 71 endpoints
2. **[MyPengaduan_Local.postman_environment.json](MyPengaduan_Local.postman_environment.json)** - Local environment
3. **[MyPengaduan_Production.postman_environment.json](MyPengaduan_Production.postman_environment.json)** - Production environment
4. **[POSTMAN_GUIDE.md](POSTMAN_GUIDE.md)** - Panduan import & menggunakan Postman

### Firebase Notification
1. **[NOTIFICATION_QUICK_START.md](NOTIFICATION_QUICK_START.md)** - Quick start guide
2. **[NOTIFICATION_SUMMARY.md](NOTIFICATION_SUMMARY.md)** - Summary lengkap sistem notifikasi
3. **[NOTIFICATION_MONITORING.md](NOTIFICATION_MONITORING.md)** - Panduan monitoring
4. **[monitor-notifications.php](monitor-notifications.php)** - CLI monitoring tool

### Development Guides
1. **[BACKEND_SETUP_COMPLETE.md](BACKEND_SETUP_COMPLETE.md)** - Setup backend guide
2. **[TESTING_GUIDE.md](TESTING_GUIDE.md)** - Testing guide lengkap
3. **[PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md)** - Deployment guide

---

## 🎯 API Endpoints

### Authentication (7 endpoints)
```
POST   /api/register
POST   /api/login
GET    /api/profile
PUT    /api/profile
POST   /api/change-password
POST   /api/logout
POST   /api/logout-all
```

### User Endpoints (30 endpoints)
```
# Dashboard
GET    /api/dashboard

# Complaints
GET    /api/complaints
POST   /api/complaints
GET    /api/complaints/{id}
PUT    /api/complaints/{id}
DELETE /api/complaints/{id}
GET    /api/complaints/{id}/track
GET    /api/complaints/statistics

# Categories
GET    /api/categories

# Announcements
GET    /api/announcements
GET    /api/announcements/urgent
GET    /api/announcements/latest
GET    /api/announcements/{id}
POST   /api/announcements/{id}/comments
```

### Admin Endpoints (34 endpoints)
```
# Dashboard
GET    /api/admin/dashboard
GET    /api/admin/dashboard/quick-stats

# Complaints (7)
GET    /api/admin/complaints
GET    /api/admin/complaints/{id}
PUT    /api/admin/complaints/{id}/status
POST   /api/admin/complaints/{id}/response
DELETE /api/admin/complaints/attachments/{id}
GET    /api/admin/complaints/statistics
POST   /api/admin/complaints/bulk-update

# Categories (8)
GET    /api/admin/categories
GET    /api/admin/categories/active
GET    /api/admin/categories/{id}
POST   /api/admin/categories
PUT    /api/admin/categories/{id}
DELETE /api/admin/categories/{id}
PUT    /api/admin/categories/{id}/toggle-status
POST   /api/admin/categories/bulk-action

# Users (9)
GET    /api/admin/users
GET    /api/admin/users/{id}
POST   /api/admin/users
PUT    /api/admin/users/{id}
DELETE /api/admin/users/{id}
PUT    /api/admin/users/{id}/verify-email
PUT    /api/admin/users/{id}/verify-user
PUT    /api/admin/users/{id}/change-role
POST   /api/admin/users/{id}/reset-password

# Announcements (9)
GET    /api/admin/announcements
GET    /api/admin/announcements/{id}
POST   /api/admin/announcements
PUT    /api/admin/announcements/{id}
DELETE /api/admin/announcements/{id}
PUT    /api/admin/announcements/{id}/toggle-status
PUT    /api/admin/announcements/{id}/toggle-sticky
PUT    /api/admin/announcements/{id}/publish
PUT    /api/admin/announcements/{id}/unpublish
```

---

## 🔧 Installation

### 1. Clone Repository
```bash
git clone https://github.com/Mifta24/my-pengaduan.git
cd my-pengaduan
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 5. Configure Firebase
Edit `.env`:
```env
FIREBASE_CREDENTIALS=path/to/firebase-credentials.json
```

Put your `firebase-credentials.json` in `storage/app/firebase/`

### 6. Run Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Build Assets
```bash
npm run build
```

### 9. Run Application
```bash
php artisan serve
```

Access: `http://localhost:8000`

---

## 🧪 Testing API

### Menggunakan Postman

1. **Import Collection**
   - Import file: `MyPengaduan_API.postman_collection.json`
   - Import environment: `MyPengaduan_Local.postman_environment.json`

2. **Login**
   ```
   POST http://localhost/api/login
   {
       "email": "admin@example.com",
       "password": "password"
   }
   ```
   Token otomatis tersimpan!

3. **Test Endpoints**
   - Semua request sudah configured dengan Bearer token
   - Variable (complaint_id, user_id, dll) auto-saved
   - Lihat [POSTMAN_GUIDE.md](POSTMAN_GUIDE.md) untuk detail

---

## 📱 Mobile Integration

### Base URL
```
Development: http://localhost/api
Production: https://your-domain.com/api
```

### Authentication
```javascript
// Login
POST /api/login
Headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
}
Body: {
    "email": "user@example.com",
    "password": "password"
}

// Use token in subsequent requests
Headers: {
    "Authorization": "Bearer {token}",
    "Accept": "application/json"
}
```

### Firebase FCM Token Registration
```javascript
// Register FCM token after login
POST /api/fcm-token
Headers: {
    "Authorization": "Bearer {token}"
}
Body: {
    "fcm_token": "device_fcm_token_here"
}
```

Lihat [NOTIFICATION_MOBILE_SETUP.md](NOTIFICATION_MOBILE_SETUP.md) untuk detail integrasi mobile.

---

## 🔔 Notification System

### Events yang Mengirim Notifikasi

1. **ComplaintCreated** - Saat user membuat pengaduan
   - Notifikasi ke: Admin
   - Channel: FCM, Database

2. **ComplaintStatusChanged** - Saat admin update status pengaduan
   - Notifikasi ke: User (pemilik pengaduan)
   - Channel: FCM, Database

3. **AnnouncementCreated** - Saat admin publish pengumuman
   - Notifikasi ke: Semua user verified
   - Channel: FCM, Database

### Monitoring Notifikasi

```bash
# Monitor real-time logs
php monitor-notifications.php watch

# Check queue status
php monitor-notifications.php queue

# View statistics
php monitor-notifications.php stats

# See all commands
php monitor-notifications.php
```

---

## 🗂️ Project Structure

```
mypengaduan/
├── app/
│   ├── Events/                      # Event classes
│   ├── Listeners/                   # Event listeners
│   ├── Services/                    # Service classes (FirebaseService)
│   ├── Http/Controllers/
│   │   ├── Api/                     # API Controllers
│   │   │   ├── Admin/              # Admin API (5 controllers)
│   │   │   ├── AuthController.php
│   │   │   ├── ComplaintController.php
│   │   │   └── AnnouncementController.php
│   │   └── [Web Controllers...]
│   └── Models/                      # Eloquent models
├── config/
│   └── firebase.php                # Firebase configuration
├── database/
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── routes/
│   ├── api.php                     # API routes (71 endpoints)
│   └── web.php                     # Web routes
├── storage/
│   └── app/
│       ├── firebase/               # Firebase credentials
│       └── public/                 # Uploaded files
└── [Documentation files...]
```

---

## 👥 Default Users

Setelah seeding, tersedia user default:

```
Admin:
Email: admin@example.com
Password: password

User:
Email: user@example.com  
Password: password
```

---

## 🛠️ Development Tools

### Queue Worker
```bash
php artisan queue:work
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Run Tests
```bash
php artisan test
```

---

## 📊 Database Schema

Lihat [docs/Erd.md](docs/Erd.md) untuk ERD lengkap.

**Main Tables:**
- `users` - User data dengan role
- `complaints` - Pengaduan masyarakat
- `categories` - Kategori pengaduan
- `attachments` - File attachment pengaduan
- `responses` - Tanggapan admin
- `announcements` - Pengumuman sistem
- `comments` - Komentar (polymorphic)
- `notifications` - Notifikasi database
- `fcm_tokens` - FCM device tokens

---

## 🚀 Deployment

Lihat [PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md) untuk panduan deployment lengkap.

**Quick Steps:**
1. Setup production server (Apache/Nginx + PHP 8.3 + PostgreSQL)
2. Clone repository & install dependencies
3. Configure environment variables
4. Run migrations
5. Setup queue worker (supervisor)
6. Configure SSL/HTTPS
7. Setup Firebase credentials

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Credits

- **Developer**: Mifta24
- **Framework**: Laravel 12.33.0
- **Firebase**: Kreait Firebase PHP SDK

---

## 📞 Support

Untuk pertanyaan atau bantuan:
- **GitHub Issues**: [https://github.com/Mifta24/my-pengaduan/issues](https://github.com/Mifta24/my-pengaduan/issues)
- **Email**: mifta24@example.com

---

**Built with ❤️ using Laravel**

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
