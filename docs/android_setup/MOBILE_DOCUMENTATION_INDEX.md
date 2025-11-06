# 📱 MyPengaduan - Mobile App Documentation Index

Dokumentasi lengkap untuk development aplikasi mobile MyPengaduan.

---

## 🎯 **Mulai Dari Sini!**

### Untuk Flutter (Recommended) ⭐
> **Flutter mendukung Android + iOS dengan satu codebase!**

1. **[FLUTTER_QUICK_START.md](./FLUTTER_QUICK_START.md)** 🚀
   - Setup project Flutter
   - Dependencies & configuration
   - Implementation priority
   - Common issues & solutions
   - **START HERE untuk Flutter!**

2. **[FLUTTER_INTEGRATION_GUIDE.md](./FLUTTER_INTEGRATION_GUIDE.md)** 📚
   - Complete API integration guide
   - Models & Services
   - State management with Provider
   - UI examples (Login, Notifications)
   - FCM setup lengkap

### Untuk Native Android (Kotlin)

1. **[ANDROID_INTEGRATION_GUIDE.md](./ANDROID_INTEGRATION_GUIDE.md)** 📚
   - Complete API documentation
   - Retrofit setup
   - Kotlin code examples
   - Authentication flow

2. **[NOTIFICATION_TYPES_ANDROID.md](./NOTIFICATION_TYPES_ANDROID.md)** 🔔
   - 6 tipe notifikasi FCM
   - Data structure untuk setiap type
   - Complete implementation code
   - Notification channels setup

---

## 📋 Dokumentasi Berdasarkan Topik

### Authentication
- **Flutter:** [FLUTTER_INTEGRATION_GUIDE.md#authentication](./FLUTTER_INTEGRATION_GUIDE.md#authentication)
- **Android:** [ANDROID_INTEGRATION_GUIDE.md#authentication](./ANDROID_INTEGRATION_GUIDE.md#authentication)

### Firebase Cloud Messaging (FCM)
- **Flutter:** [FLUTTER_INTEGRATION_GUIDE.md#firebase-cloud-messaging-fcm](./FLUTTER_INTEGRATION_GUIDE.md#firebase-cloud-messaging-fcm)
- **Android:** [ANDROID_INTEGRATION_GUIDE.md#firebase-cloud-messaging-fcm](./ANDROID_INTEGRATION_GUIDE.md#firebase-cloud-messaging-fcm)
- **Notification Types:** [NOTIFICATION_TYPES_ANDROID.md](./NOTIFICATION_TYPES_ANDROID.md)

### API Integration
- **Flutter:** [FLUTTER_INTEGRATION_GUIDE.md#api-integration](./FLUTTER_INTEGRATION_GUIDE.md#api-integration)
- **Android:** [ANDROID_INTEGRATION_GUIDE.md#api-endpoints](./ANDROID_INTEGRATION_GUIDE.md#api-endpoints)

### Backend API Testing
- **[API_TESTING_GUIDE.md](./API_TESTING_GUIDE.md)** - Postman testing guide
- **Postman Collection:** `MyPengaduan_API_FIXED.postman_collection.json`

---

## 🏗️ System Architecture

```
┌─────────────────┐
│  Flutter App    │
│  (Android/iOS)  │
└────────┬────────┘
         │
         │ HTTPS/REST API
         │ (Bearer Token)
         │
┌────────▼────────┐
│  Laravel 10.x   │
│   Backend API   │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼──┐  ┌──▼──────┐
│ PostgreSQL │  Firebase │
│  Database  │    FCM    │
└────────┘  └─────────┘
```

---

## 📱 Available API Endpoints

### 🔐 Authentication
```
POST   /auth/register         - Register user
POST   /auth/login            - Login
GET    /auth/profile          - Get profile
PUT    /auth/profile          - Update profile
PUT    /auth/change-password  - Change password
POST   /auth/logout           - Logout
POST   /auth/logout-all       - Logout all devices
```

### 📝 Complaints
```
GET    /complaints                   - List complaints (paginated)
POST   /complaints                   - Create complaint
GET    /complaints/{id}              - Get detail
GET    /complaints/{id}/track        - Track status
PUT    /complaints/{id}              - Update complaint
DELETE /complaints/{id}              - Delete complaint
GET    /complaints/categories        - Get categories
GET    /complaints/statistics        - Get statistics
```

### 🔔 Notifications
```
GET    /notifications                - List notifications (paginated)
POST   /notifications/{id}/read      - Mark as read
POST   /notifications/read-all       - Mark all as read
GET    /notification-settings        - Get settings
PUT    /notification-settings        - Update settings
```

### 📱 Device Tokens (FCM)
```
POST   /device-tokens       - Register FCM token
GET    /device-tokens       - List devices
DELETE /device-tokens/{id}  - Remove device
```

### 📢 Announcements
```
GET    /announcements              - List announcements
GET    /announcements/urgent       - Urgent announcements
GET    /announcements/latest       - Latest announcements
GET    /announcements/{id}         - Get detail
POST   /announcements/{id}/comments - Add comment
```

---

## 🔔 Notification Types

Backend mengirim 6 tipe notifikasi FCM:

| Type | Description | Action |
|------|-------------|--------|
| `complaint_created` | Pengaduan baru dibuat | Navigate to complaint detail |
| `complaint_status_changed` | Status berubah | Show status update |
| `admin_response` | Admin memberi tanggapan | Navigate to responses |
| `complaint_resolved` | Pengaduan selesai | Show rating dialog |
| `announcement_created` | Pengumuman baru | Navigate to announcement |
| `comment_added` | Komentar baru | Navigate to comments |

**Detail lengkap:** [NOTIFICATION_TYPES_ANDROID.md](./NOTIFICATION_TYPES_ANDROID.md)

---

## 📊 Response Format

### Standard Response
```json
{
    "success": true,
    "message": "Success message",
    "data": { ... }
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Data loaded successfully",
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 72
    },
    "data": [ ... ]
}
```

### With Additional Data
```json
{
    "success": true,
    "message": "Notifications loaded",
    "meta": { ... },
    "unread_count": 5,  // Additional data
    "data": [ ... ]
}
```

---

## ⏰ Timestamp Format

**Format:** ISO 8601 dengan microseconds
```
2025-01-15T10:30:45.123456Z
```

**Parsing:**

**Flutter/Dart:**
```dart
final timestamp = DateTime.parse("2025-01-15T10:30:45.123456Z");
```

**Kotlin:**
```kotlin
val timestamp = ZonedDateTime.parse("2025-01-15T10:30:45.123456Z")
```

---

## 🔐 Authentication Flow

```
1. Login/Register → Get token
2. Save token → Local storage
3. Add to headers → Authorization: Bearer {token}
4. Register FCM token → POST /device-tokens
5. Handle 401 → Token expired, logout
```

---

## 🛠️ Development Setup

### Backend (Laravel)

```powershell
# Start server
cd C:\laragon\www\mypengaduan
php artisan serve --host=0.0.0.0 --port=8000

# Server akan jalan di:
# http://192.168.1.100:8000/api/
```

### Frontend (Flutter)

```powershell
# Accept Android licenses
flutter doctor --android-licenses

# Setup Firebase
flutterfire configure

# Run app
flutter run
```

---

## 📚 Additional Documentation

### Backend Documentation
- [BACKEND_SETUP_COMPLETE.md](./BACKEND_SETUP_COMPLETE.md)
- [API_TESTING_GUIDE.md](./API_TESTING_GUIDE.md)
- [NOTIFICATION_SYSTEM_PLAN.md](./NOTIFICATION_SYSTEM_PLAN.md)

### Development Guides
- [DEVELOPMENT_SUMMARY.md](./docs/DEVELOPMENT_SUMMARY.md)
- [COMPLAINT_SYSTEM_IMPROVEMENTS.md](./docs/COMPLAINT_SYSTEM_IMPROVEMENTS.md)

---

## ✅ Pre-Development Checklist

### Backend
- [x] Laravel API running
- [x] PostgreSQL database configured
- [x] FCM notifications integrated
- [x] API tested with Postman
- [x] ISO 8601 timestamps implemented
- [x] Bearer token authentication working

### Mobile (Flutter)
- [ ] Flutter SDK installed (v3.35.1+)
- [ ] Android SDK configured
- [ ] Android licenses accepted
- [ ] Firebase project created
- [ ] VS Code + Flutter extension
- [ ] Emulator/Device ready

---

## 🎯 Implementation Priority

### Week 1: Core Features
1. ✅ Authentication (Login/Register)
2. ✅ FCM Setup & Token Registration
3. ✅ List & Create Complaints
4. ✅ View Complaint Details
5. ✅ Notification List & Mark as Read

### Week 2: Enhanced Features
1. Upload Complaint Attachments
2. Filter & Search Complaints
3. View Announcements
4. User Profile Management
5. Notification Settings

### Week 3: Polish & Testing
1. Offline Mode
2. Local Caching
3. UI/UX Polish
4. Performance Optimization
5. Bug Fixes & Testing

---

## 🚀 Quick Links

| Platform | Quick Start | Complete Guide | API Docs |
|----------|-------------|----------------|----------|
| **Flutter** | [Quick Start](./FLUTTER_QUICK_START.md) | [Complete Guide](./FLUTTER_INTEGRATION_GUIDE.md) | [API Testing](./API_TESTING_GUIDE.md) |
| **Android** | [Quick Start](./ANDROID_QUICK_START.md) | [Complete Guide](./ANDROID_INTEGRATION_GUIDE.md) | [API Testing](./API_TESTING_GUIDE.md) |
| **Notifications** | [Types](./NOTIFICATION_TYPES_ANDROID.md) | [System Plan](./NOTIFICATION_SYSTEM_PLAN.md) | - |

---

## 🆘 Need Help?

1. **Check documentation** - Most questions answered here
2. **Test API with Postman** - Ensure backend works first
3. **Check console logs** - Error messages are helpful
4. **Google the error** - Add "flutter" or "android" to search

---

## 📞 Backend Information

- **Framework:** Laravel 10.x
- **Database:** PostgreSQL
- **Authentication:** Laravel Sanctum (Bearer Token)
- **Push Notifications:** Firebase Cloud Messaging (FCM)
- **Base URL (Local):** `http://192.168.1.100:8000/api/`

---

## 📝 Documentation Version

- **Version:** 1.0.0
- **Last Updated:** November 6, 2025
- **Laravel Version:** 10.x
- **Flutter Version:** 3.35.1
- **Target Android:** API 24+ (Android 7.0+)
- **Target iOS:** iOS 12.0+

---

## 🎉 Ready to Start!

Semua yang diperlukan untuk development sudah siap:
- ✅ Backend API fully functional
- ✅ FCM notification system
- ✅ Complete documentation
- ✅ Postman collection
- ✅ Code examples

**Choose your platform and start coding! 🚀**

---

**Happy Coding!** 💙
