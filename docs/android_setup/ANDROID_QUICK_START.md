# 📱 Mobile Development - Quick Start Summary

> **🎯 Recommended:** Gunakan **Flutter** untuk development! 
> Lihat [FLUTTER_QUICK_START.md](./FLUTTER_QUICK_START.md) untuk panduan lengkap.
>
> Flutter mendukung **Android + iOS** sekaligus dengan satu codebase!

---

## ✅ Yang Sudah Siap untuk Mobile (Android/iOS/Flutter)

### 1. **Backend API** ✓
- ✅ RESTful API dengan Laravel 10.x
- ✅ Authentication dengan Sanctum (Bearer Token)
- ✅ ISO 8601 timestamp format untuk semua endpoint
- ✅ Consistent response format (success, message, meta, data)
- ✅ Pagination support dengan metadata lengkap

### 2. **Firebase Cloud Messaging (FCM)** ✓
- ✅ FCM notification system terintegrasi
- ✅ Database logging untuk notification history
- ✅ Device token management (register, list, delete)
- ✅ Notification preferences/settings

### 3. **API Endpoints Ready** ✓

#### Authentication
- `POST /auth/register` - Register user
- `POST /auth/login` - Login
- `GET /auth/profile` - Get profile
- `PUT /auth/profile` - Update profile
- `PUT /auth/change-password` - Change password
- `POST /auth/logout` - Logout
- `POST /auth/logout-all` - Logout from all devices

#### Complaints
- `GET /complaints` - List complaints (dengan filter & pagination)
- `POST /complaints` - Create complaint
- `GET /complaints/{id}` - Get detail
- `GET /complaints/{id}/track` - Track status
- `PUT /complaints/{id}` - Update complaint
- `DELETE /complaints/{id}` - Delete complaint
- `GET /complaints/categories` - Get categories
- `GET /complaints/statistics` - Get statistics

#### Notifications
- `GET /notifications` - List notifications (dengan filter & pagination)
- `POST /notifications/{id}/read` - Mark as read
- `POST /notifications/read-all` - Mark all as read
- `GET /notification-settings` - Get settings
- `PUT /notification-settings` - Update settings

#### Device Tokens (FCM)
- `POST /device-tokens` - Register device token
- `GET /device-tokens` - List devices
- `DELETE /device-tokens/{id}` - Remove device

#### Announcements
- `GET /announcements` - List announcements
- `GET /announcements/urgent` - Urgent announcements
- `GET /announcements/latest` - Latest announcements
- `GET /announcements/{id}` - Get detail
- `POST /announcements/{id}/comments` - Add comment

---

## 📚 Dokumentasi yang Tersedia

1. **ANDROID_INTEGRATION_GUIDE.md**
   - Setup & Configuration
   - Complete API Documentation
   - Authentication Flow
   - FCM Integration
   - Data Models
   - Error Handling
   - Best Practices
   - Code Examples

2. **NOTIFICATION_TYPES_ANDROID.md**
   - Semua tipe notifikasi (6 types)
   - Data structure untuk setiap type
   - FCM payload examples
   - Complete Android implementation code
   - Notification channels setup
   - UI/UX recommendations

---

## 🚀 Quick Setup Steps untuk Android Developer

### Step 1: Setup Dependencies
```gradle
// Firebase
implementation platform('com.google.firebase:firebase-bom:32.7.0')
implementation 'com.google.firebase:firebase-messaging-ktx'

// Networking
implementation 'com.squareup.retrofit2:retrofit:2.9.0'
implementation 'com.squareup.retrofit2:converter-gson:2.9.0'

// Coroutines
implementation 'org.jetbrains.kotlinx:kotlinx-coroutines-android:1.7.3'
```

### Step 2: Configure Base URL
```kotlin
const val BASE_URL = "http://192.168.1.100:8000/api/" // Development
// const val BASE_URL = "https://your-domain.com/api/" // Production
```

### Step 3: Setup Firebase
1. Download `google-services.json` dari Firebase Console
2. Letakkan di folder `app/`
3. Implement `FirebaseMessagingService`
4. Register service di `AndroidManifest.xml`

### Step 4: Create Retrofit Service
```kotlin
interface ApiService {
    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>
    
    @GET("notifications")
    suspend fun getNotifications(
        @Query("status") status: String? = null,
        @Query("page") page: Int = 1
    ): Response<NotificationResponse>
    
    @POST("device-tokens")
    suspend fun registerDeviceToken(@Body request: DeviceTokenRequest): Response<ApiResponse<DeviceToken>>
    
    // ... other endpoints
}
```

### Step 5: Implement FCM Token Registration
```kotlin
FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
    if (task.isSuccessful) {
        val token = task.result
        // Send to backend after login
        apiService.registerDeviceToken(
            DeviceTokenRequest(
                device_token = token,
                device_type = "android",
                device_model = Build.MODEL,
                os_version = Build.VERSION.RELEASE
            )
        )
    }
}
```

---

## 🔔 Notification Types

Backend akan mengirim 6 tipe notifikasi:

1. **complaint_created** - Pengaduan baru dibuat
2. **complaint_status_changed** - Status berubah
3. **admin_response** - Admin memberi tanggapan
4. **complaint_resolved** - Pengaduan selesai
5. **announcement_created** - Pengumuman baru
6. **comment_added** - Komentar baru

Setiap notifikasi memiliki:
- `type` - Tipe notifikasi
- `title` - Judul notifikasi
- `body` - Isi notifikasi
- `data` - Custom data (JSON object)

Detail lengkap: Lihat **NOTIFICATION_TYPES_ANDROID.md**

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

**Format**: ISO 8601 dengan microseconds
```
2025-01-15T10:30:45.123456Z
```

**Parsing di Kotlin**:
```kotlin
import java.time.ZonedDateTime
import java.time.format.DateTimeFormatter

val timestamp = ZonedDateTime.parse("2025-01-15T10:30:45.123456Z")

// Format untuk display
val formatter = DateTimeFormatter.ofPattern("dd MMM yyyy, HH:mm")
val displayTime = timestamp.format(formatter) // "15 Jan 2025, 10:30"
```

---

## 🔐 Authentication Flow

1. **Login** → Get token
2. **Save token** → SharedPreferences/DataStore
3. **Add token** → Setiap request header: `Authorization: Bearer {token}`
4. **Register FCM token** → Setelah login berhasil
5. **Handle 401** → Token expired, logout user

```kotlin
// Retrofit Interceptor
class AuthInterceptor(private val tokenManager: TokenManager) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val request = chain.request().newBuilder()
        tokenManager.getToken()?.let { token ->
            request.addHeader("Authorization", "Bearer $token")
        }
        request.addHeader("Accept", "application/json")
        return chain.proceed(request.build())
    }
}
```

---

## ⚠️ Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `401` - Unauthorized (token invalid/expired)
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Validation Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Email sudah terdaftar"],
        "password": ["Password minimal 8 karakter"]
    }
}
```

---

## 📝 Testing Endpoints

### Menggunakan Postman
1. Import collection: `MyPengaduan_API_FIXED.postman_collection.json`
2. Import environment: `MyPengaduan_Local.postman_environment.json`
3. Test semua endpoint sebelum implementasi di Android

### Testing Flow
1. Register/Login → Dapatkan token
2. Test device token registration
3. Test get notifications
4. Test create complaint
5. Test mark notification as read
6. Test logout

---

## 🎯 Priority Implementation

### Phase 1: Core Features
1. ✅ Authentication (login, register, logout)
2. ✅ FCM setup & device token registration
3. ✅ List & create complaints
4. ✅ View complaint details
5. ✅ Notification list & mark as read

### Phase 2: Enhanced Features
1. Upload complaint attachments
2. Filter & search complaints
3. View announcements
4. Notification settings
5. User profile management

### Phase 3: Polish
1. Offline mode
2. Local caching
3. Push notification customization
4. Analytics tracking
5. Performance optimization

---

## 🛠️ Development Tools

### Recommended Libraries
- **Retrofit** - HTTP client
- **Gson** - JSON parsing
- **Coroutines** - Async operations
- **Room** - Local database (optional)
- **Coil/Glide** - Image loading
- **Firebase** - FCM & Analytics
- **DataStore** - Preferences storage

### Debug Tools
- **Postman** - API testing
- **Firebase Console** - Monitor FCM
- **Android Studio Profiler** - Performance
- **Logcat** - Debug logs

---

## 📞 Backend Information

- **Framework**: Laravel 10.x
- **Database**: PostgreSQL
- **Authentication**: Laravel Sanctum
- **Push Notifications**: Firebase FCM
- **File Storage**: Local storage (public/storage)

---

## 🔗 Resources

### For Flutter (Recommended) 🎯
1. **FLUTTER_QUICK_START.md** - Quick start guide untuk Flutter
2. **FLUTTER_INTEGRATION_GUIDE.md** - Complete Flutter integration guide
3. **NOTIFICATION_TYPES_ANDROID.md** - FCM notification details
4. **Postman Collection** - API testing collection

### For Native Android
1. **ANDROID_INTEGRATION_GUIDE.md** - Native Android API documentation
2. **NOTIFICATION_TYPES_ANDROID.md** - FCM notification details
3. **API_TESTING_GUIDE.md** - Backend testing guide

---

## ✨ Next Steps

1. **Read** complete documentation:
   - ANDROID_INTEGRATION_GUIDE.md
   - NOTIFICATION_TYPES_ANDROID.md

2. **Setup** development environment:
   - Android Studio
   - Firebase project
   - Postman for API testing

3. **Test** API endpoints:
   - Import Postman collection
   - Test all endpoints
   - Understand response format

4. **Implement** authentication:
   - Login/Register flow
   - Token storage
   - Auto-login

5. **Integrate** FCM:
   - Setup Firebase
   - Register device token
   - Handle notifications

6. **Build** core features:
   - Complaint list & create
   - Notification list
   - User profile

---

## 🎉 Ready to Start!

Semua yang diperlukan untuk membangun aplikasi Android sudah siap:
- ✅ Backend API fully functional
- ✅ FCM notification system
- ✅ Complete documentation
- ✅ Postman collection for testing
- ✅ Code examples in Kotlin

**Selamat coding! 🚀**

---

## 📧 Support

Jika ada pertanyaan atau menemukan issue:
1. Check dokumentasi terlebih dahulu
2. Test endpoint di Postman
3. Contact backend team

**Documentation Version**: 1.0.0  
**Last Updated**: November 5, 2025  
**Laravel Version**: 10.x  
**Target Android**: API 24+ (Android 7.0+)
