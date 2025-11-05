# 📱 Android Integration Guide - MyPengaduan API

Panduan lengkap untuk integrasi aplikasi Android dengan MyPengaduan Backend API.

## 📋 Table of Contents
- [Overview](#overview)
- [Base Configuration](#base-configuration)
- [Authentication](#authentication)
- [Firebase Cloud Messaging (FCM)](#firebase-cloud-messaging-fcm)
- [API Endpoints](#api-endpoints)
- [Data Models](#data-models)
- [Error Handling](#error-handling)
- [Best Practices](#best-practices)

---

## 🎯 Overview

MyPengaduan API menggunakan:
- **Authentication**: Laravel Sanctum (Bearer Token)
- **Push Notifications**: Firebase Cloud Messaging (FCM)
- **Response Format**: JSON dengan ISO 8601 timestamps
- **Pagination**: Laravel pagination dengan metadata lengkap

---

## ⚙️ Base Configuration

### Base URL
```kotlin
// Development
const val BASE_URL = "http://192.168.1.100:8000/api/"

// Production
const val BASE_URL = "https://your-domain.com/api/"
```

### Headers
```kotlin
// Authenticated requests
headers = mapOf(
    "Accept" to "application/json",
    "Content-Type" to "application/json",
    "Authorization" to "Bearer ${userToken}"
)

// Public requests
headers = mapOf(
    "Accept" to "application/json",
    "Content-Type" to "application/json"
)
```

---

## 🔐 Authentication

### 1. Register User
**Endpoint**: `POST /auth/register`

```kotlin
data class RegisterRequest(
    val name: String,
    val email: String,
    val phone: String,
    val password: String,
    val password_confirmation: String,
    val address: String
)

data class AuthResponse(
    val success: Boolean,
    val message: String,
    val data: AuthData
)

data class AuthData(
    val user: User,
    val token: String
)
```

**Example Request**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "Jl. Example No. 123"
}
```

### 2. Login
**Endpoint**: `POST /auth/login`

```kotlin
data class LoginRequest(
    val email: String,
    val password: String
)
```

### 3. Get Profile
**Endpoint**: `GET /auth/profile`

**Response**:
```json
{
    "success": true,
    "message": "Profile loaded successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "081234567890",
        "address": "Jl. Example No. 123",
        "roles": ["user"],
        "created_at": "2025-01-15T10:30:45.123456Z"
    }
}
```

### 4. Logout
**Endpoint**: `POST /auth/logout`

### 5. Logout All Devices
**Endpoint**: `POST /auth/logout-all`

---

## 🔔 Firebase Cloud Messaging (FCM)

### Setup FCM di Android

#### 1. Tambahkan Dependencies
```gradle
// build.gradle (app)
dependencies {
    implementation platform('com.google.firebase:firebase-bom:32.7.0')
    implementation 'com.google.firebase:firebase-messaging-ktx'
}
```

#### 2. Create FCM Service
```kotlin
class MyFirebaseMessagingService : FirebaseMessagingService() {
    
    override fun onNewToken(token: String) {
        super.onNewToken(token)
        // Send token to backend
        sendTokenToServer(token)
    }
    
    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)
        
        // Handle notification
        message.notification?.let {
            showNotification(it.title, it.body, message.data)
        }
    }
    
    private fun sendTokenToServer(token: String) {
        val deviceInfo = DeviceTokenRequest(
            device_token = token,
            device_type = "android",
            device_model = Build.MODEL,
            os_version = Build.VERSION.RELEASE,
            app_version = BuildConfig.VERSION_NAME
        )
        
        // Call API to register device token
        apiService.registerDeviceToken(deviceInfo)
    }
}
```

### Register Device Token
**Endpoint**: `POST /device-tokens`

```kotlin
data class DeviceTokenRequest(
    val device_token: String,
    val device_type: String, // "android" or "ios"
    val device_model: String? = null,
    val os_version: String? = null,
    val app_version: String? = null
)
```

**Example**:
```json
{
    "device_token": "fcm_token_here...",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "13",
    "app_version": "1.0.0"
}
```

### Get Device Tokens
**Endpoint**: `GET /device-tokens`

### Delete Device Token
**Endpoint**: `DELETE /device-tokens/{id}`

---

## 📬 Notification Endpoints

### 1. Get Notifications
**Endpoint**: `GET /notifications`

**Query Parameters**:
- `status`: `read` | `unread` (optional)
- `type`: notification type (optional)
- `per_page`: items per page (default: 15)
- `page`: page number

```kotlin
data class NotificationResponse(
    val success: Boolean,
    val message: String,
    val meta: PaginationMeta,
    val unread_count: Int,
    val data: List<Notification>
)

data class Notification(
    val id: Int,
    val type: String,
    val title: String,
    val body: String,
    val data: Map<String, Any>?,
    val is_read: Boolean,
    val read_at: String?, // ISO 8601 format
    val created_at: String, // ISO 8601 format: "2025-01-15T10:30:45.123456Z"
    val updated_at: String  // ISO 8601 format
)

data class PaginationMeta(
    val current_page: Int,
    val from: Int?,
    val last_page: Int,
    val path: String,
    val per_page: Int,
    val to: Int?,
    val total: Int
)
```

**Example Response**:
```json
{
    "success": true,
    "message": "Notifications loaded successfully",
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "per_page": 15,
        "to": 15,
        "total": 42
    },
    "unread_count": 5,
    "data": [
        {
            "id": 1,
            "type": "complaint_status_changed",
            "title": "Status Pengaduan Diperbarui",
            "body": "Pengaduan Anda telah diproses",
            "data": {
                "complaint_id": 123,
                "status": "in_progress"
            },
            "is_read": false,
            "read_at": null,
            "created_at": "2025-01-15T10:30:45.123456Z",
            "updated_at": "2025-01-15T10:30:45.123456Z"
        }
    ]
}
```

### 2. Mark as Read
**Endpoint**: `POST /notifications/{id}/read`

**Response**:
```json
{
    "success": true,
    "message": "Notification marked as read",
    "data": {
        "id": 1,
        "is_read": true,
        "read_at": "2025-01-15T10:35:00.123456Z"
    }
}
```

### 3. Mark All as Read
**Endpoint**: `POST /notifications/read-all`

### 4. Get Notification Settings
**Endpoint**: `GET /notification-settings`

### 5. Update Notification Settings
**Endpoint**: `PUT /notification-settings`

```kotlin
data class NotificationSettings(
    val complaint_created: Boolean,
    val complaint_status_changed: Boolean,
    val announcement_created: Boolean,
    val admin_response: Boolean,
    val comment_added: Boolean,
    val push_enabled: Boolean
)
```

---

## 📝 Complaint Endpoints

### 1. Get Complaints
**Endpoint**: `GET /complaints`

**Query Parameters**:
- `status`: `pending` | `in_progress` | `resolved` | `rejected`
- `category_id`: filter by category
- `search`: search by title/description
- `sort_by`: `created_at` | `report_date` | `status`
- `sort_order`: `asc` | `desc`
- `per_page`: items per page

```kotlin
data class Complaint(
    val id: Int,
    val user_id: Int,
    val category_id: Int,
    val title: String,
    val description: String,
    val location: String,
    val status: String, // pending, in_progress, resolved, rejected
    val priority: String, // low, medium, high
    val report_date: String, // ISO 8601: "2025-01-15T10:30:45.123456Z"
    val estimated_resolution: String?, // ISO 8601
    val attachments: List<String>,
    val category: Category?,
    val responses: List<Response>,
    val created_at: String, // ISO 8601
    val updated_at: String  // ISO 8601
)
```

### 2. Create Complaint
**Endpoint**: `POST /complaints`

```kotlin
data class CreateComplaintRequest(
    val category_id: Int,
    val title: String,
    val description: String,
    val location: String,
    val report_date: String, // Format: "Y-m-d H:i:s"
    val priority: String, // "low", "medium", "high"
    val attachments: List<File>? = null
)
```

**Note**: Gunakan `multipart/form-data` untuk upload file

### 3. Get Complaint Detail
**Endpoint**: `GET /complaints/{id}`

### 4. Track Complaint
**Endpoint**: `GET /complaints/{id}/track`

**Response includes**: complaint details + status history

### 5. Update Complaint
**Endpoint**: `PUT /complaints/{id}`

### 6. Delete Complaint
**Endpoint**: `DELETE /complaints/{id}`

### 7. Get Categories
**Endpoint**: `GET /complaints/categories`

### 8. Get Statistics
**Endpoint**: `GET /complaints/statistics`

---

## 📢 Announcement Endpoints

### 1. Get Announcements
**Endpoint**: `GET /announcements`

### 2. Get Urgent Announcements
**Endpoint**: `GET /announcements/urgent`

### 3. Get Latest Announcements
**Endpoint**: `GET /announcements/latest`

### 4. Get Announcement Detail
**Endpoint**: `GET /announcements/{id}`

### 5. Add Comment
**Endpoint**: `POST /announcements/{id}/comments`

---

## 📊 Data Models

### Timestamp Parsing
Semua timestamp menggunakan **ISO 8601 format** dengan microseconds:

```kotlin
// Format: "2025-01-15T10:30:45.123456Z"

import java.time.ZonedDateTime
import java.time.format.DateTimeFormatter

fun parseTimestamp(timestamp: String): ZonedDateTime {
    val formatter = DateTimeFormatter.ISO_DATE_TIME
    return ZonedDateTime.parse(timestamp, formatter)
}

// Usage with Gson
class TimestampAdapter : JsonDeserializer<ZonedDateTime> {
    override fun deserialize(
        json: JsonElement,
        typeOfT: Type,
        context: JsonDeserializationContext
    ): ZonedDateTime {
        return ZonedDateTime.parse(json.asString)
    }
}

// Register adapter
val gson = GsonBuilder()
    .registerTypeAdapter(ZonedDateTime::class.java, TimestampAdapter())
    .create()
```

### Standard Response Format

```kotlin
data class ApiResponse<T>(
    val success: Boolean,
    val message: String,
    val data: T?
)

data class PaginatedResponse<T>(
    val success: Boolean,
    val message: String,
    val meta: PaginationMeta,
    val data: List<T>,
    // Additional fields (e.g., statistics, unread_count)
    val unread_count: Int? = null
)
```

---

## ⚠️ Error Handling

### Error Response Format
```json
{
    "success": false,
    "message": "Error message here",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (Invalid token)
- `403` - Forbidden (Insufficient permissions)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Example Error Handling
```kotlin
sealed class Resource<T> {
    data class Success<T>(val data: T) : Resource<T>()
    data class Error<T>(val message: String, val errors: Map<String, List<String>>? = null) : Resource<T>()
    data class Loading<T>(val isLoading: Boolean = true) : Resource<T>()
}

suspend fun <T> safeApiCall(apiCall: suspend () -> Response<T>): Resource<T> {
    return try {
        val response = apiCall()
        if (response.isSuccessful) {
            Resource.Success(response.body()!!)
        } else {
            val errorBody = response.errorBody()?.string()
            val errorResponse = Gson().fromJson(errorBody, ErrorResponse::class.java)
            Resource.Error(errorResponse.message, errorResponse.errors)
        }
    } catch (e: Exception) {
        Resource.Error(e.message ?: "Unknown error occurred")
    }
}
```

---

## 🎯 Best Practices

### 1. Token Management
```kotlin
class TokenManager(private val context: Context) {
    private val prefs = context.getSharedPreferences("auth_prefs", Context.MODE_PRIVATE)
    
    fun saveToken(token: String) {
        prefs.edit().putString("auth_token", token).apply()
    }
    
    fun getToken(): String? {
        return prefs.getString("auth_token", null)
    }
    
    fun clearToken() {
        prefs.edit().remove("auth_token").apply()
    }
}
```

### 2. Retrofit Setup
```kotlin
object RetrofitClient {
    private const val BASE_URL = "http://192.168.1.100:8000/api/"
    
    private val okHttpClient = OkHttpClient.Builder()
        .addInterceptor { chain ->
            val token = TokenManager.getToken()
            val request = chain.request().newBuilder()
                .addHeader("Accept", "application/json")
                .apply {
                    if (token != null) {
                        addHeader("Authorization", "Bearer $token")
                    }
                }
                .build()
            chain.proceed(request)
        }
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .build()
    
    val apiService: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}
```

### 3. FCM Notification Handler
```kotlin
private fun showNotification(title: String?, body: String?, data: Map<String, String>) {
    val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
    
    // Create notification channel (Android O+)
    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
        val channel = NotificationChannel(
            "complaints_channel",
            "Complaints Notifications",
            NotificationManager.IMPORTANCE_HIGH
        )
        notificationManager.createNotificationChannel(channel)
    }
    
    // Create intent based on notification type
    val intent = when (data["type"]) {
        "complaint_status_changed" -> {
            Intent(this, ComplaintDetailActivity::class.java).apply {
                putExtra("complaint_id", data["complaint_id"]?.toInt())
            }
        }
        "admin_response" -> {
            Intent(this, ComplaintDetailActivity::class.java).apply {
                putExtra("complaint_id", data["complaint_id"]?.toInt())
            }
        }
        else -> Intent(this, MainActivity::class.java)
    }
    
    val pendingIntent = PendingIntent.getActivity(
        this, 0, intent,
        PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
    )
    
    val notification = NotificationCompat.Builder(this, "complaints_channel")
        .setContentTitle(title)
        .setContentText(body)
        .setSmallIcon(R.drawable.ic_notification)
        .setAutoCancel(true)
        .setContentIntent(pendingIntent)
        .setPriority(NotificationCompat.PRIORITY_HIGH)
        .build()
    
    notificationManager.notify(System.currentTimeMillis().toInt(), notification)
}
```

### 4. Pagination Helper
```kotlin
class PaginationScrollListener(
    private val layoutManager: LinearLayoutManager,
    private val onLoadMore: () -> Unit
) : RecyclerView.OnScrollListener() {
    
    private var loading = false
    private var lastPage = false
    
    override fun onScrolled(recyclerView: RecyclerView, dx: Int, dy: Int) {
        super.onScrolled(recyclerView, dx, dy)
        
        val visibleItemCount = layoutManager.childCount
        val totalItemCount = layoutManager.itemCount
        val firstVisibleItemPosition = layoutManager.findFirstVisibleItemPosition()
        
        if (!loading && !lastPage) {
            if ((visibleItemCount + firstVisibleItemPosition) >= totalItemCount
                && firstVisibleItemPosition >= 0) {
                onLoadMore()
            }
        }
    }
    
    fun setLoading(isLoading: Boolean) {
        loading = isLoading
    }
    
    fun setLastPage(isLastPage: Boolean) {
        lastPage = isLastPage
    }
}
```

---

## 📝 Testing Checklist

- [ ] Authentication flow (register, login, logout)
- [ ] FCM token registration on app start
- [ ] Receive push notifications
- [ ] Display notification list with pagination
- [ ] Mark notification as read
- [ ] Create complaint with attachments
- [ ] View complaint details
- [ ] Track complaint status
- [ ] View announcements
- [ ] Handle offline mode
- [ ] Handle token expiration (401 errors)
- [ ] Test with different Android versions
- [ ] Test notification permissions (Android 13+)

---

## 🔗 Additional Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/10.x/sanctum)
- [Firebase Cloud Messaging (FCM)](https://firebase.google.com/docs/cloud-messaging)
- [Retrofit Documentation](https://square.github.io/retrofit/)
- [ISO 8601 Date Format](https://en.wikipedia.org/wiki/ISO_8601)

---

## 📞 Support

Jika ada pertanyaan atau issue, hubungi backend team.

**Happy Coding! 🚀**
