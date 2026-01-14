# Announcement Bookmarks & Comments API Documentation

## Overview
API endpoints untuk fitur bookmark dan komentar pada pengumuman. Semua endpoints ini memerlukan autentikasi menggunakan Bearer token.

---

## 📌 Bookmark Endpoints

### 1. Toggle Bookmark
Toggle bookmark untuk sebuah pengumuman (tambah/hapus bookmark).

**Endpoint:** `POST /api/announcements/{id}/bookmark`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**URL Parameters:**
- `id` (integer, required) - ID pengumuman

**Response Success - Bookmark Added (200):**
```json
{
    "success": true,
    "message": "Announcement bookmarked successfully",
    "data": {
        "is_bookmarked": true,
        "message": "Announcement bookmarked successfully"
    }
}
```

**Response Success - Bookmark Removed (200):**
```json
{
    "success": true,
    "message": "Bookmark removed successfully",
    "data": {
        "is_bookmarked": false,
        "message": "Bookmark removed successfully"
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Announcement not found or not yet published"
}
```

---

### 2. Get Bookmarked Announcements
Mendapatkan daftar pengumuman yang sudah di-bookmark oleh user.

**Endpoint:** `GET /api/announcements/bookmarked`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (integer, optional) - Jumlah item per halaman (default: 10)
- `page` (integer, optional) - Nomor halaman (default: 1)

**Response Success (200):**
```json
{
    "success": true,
    "message": "Bookmarked announcements loaded successfully",
    "data": [
        {
            "id": 1,
            "title": "Pengumuman Penting",
            "content": "Isi pengumuman...",
            "priority": "high",
            "is_sticky": false,
            "published_at": "2026-01-10T10:00:00.000000Z",
            "pivot": {
                "user_id": 5,
                "announcement_id": 1,
                "created_at": "2026-01-14T05:30:00.000000Z"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 5,
        "last_page": 1,
        "from": 1,
        "to": 5
    }
}
```

---

## 💬 Comment Endpoints

### 3. Get Comments
Mendapatkan daftar komentar untuk sebuah pengumuman.

**Endpoint:** `GET /api/announcements/{id}/comments`

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `id` (integer, required) - ID pengumuman

**Query Parameters:**
- `per_page` (integer, optional) - Jumlah komentar per halaman (default: 20)
- `page` (integer, optional) - Nomor halaman (default: 1)

**Response Success (200):**
```json
{
    "success": true,
    "message": "Comments loaded successfully",
    "data": [
        {
            "id": 15,
            "content": "Terima kasih atas informasinya!",
            "created_at": "2026-01-14T05:30:00.000000Z",
            "user": {
                "id": 5,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 15,
        "last_page": 1,
        "from": 1,
        "to": 15
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Announcement not found or not yet published"
}
```

---

### 4. Add Comment (Already Exists)
Menambahkan komentar baru pada pengumuman.

**Endpoint:** `POST /api/announcements/{id}/comments`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**URL Parameters:**
- `id` (integer, required) - ID pengumuman

**Request Body:**
```json
{
    "content": "Ini adalah komentar saya"
}
```

**Validation Rules:**
- `content` (string, required) - Min: 5 karakter, Max: 1000 karakter

**Response Success (201):**
```json
{
    "success": true,
    "message": "Comment added successfully",
    "data": {
        "id": 20,
        "content": "Ini adalah komentar saya",
        "user_id": 5,
        "commentable_id": 1,
        "commentable_type": "App\\Models\\Announcement",
        "is_approved": true,
        "created_at": "2026-01-14T05:30:00.000000Z",
        "user": {
            "id": 5,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "Comments are not allowed for this announcement"
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "content": [
            "The content field is required."
        ]
    }
}
```

---

### 5. Delete Comment
Menghapus komentar. Hanya pemilik komentar atau admin yang bisa menghapus.

**Endpoint:** `DELETE /api/announcements/{announcementId}/comments/{commentId}`

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `announcementId` (integer, required) - ID pengumuman
- `commentId` (integer, required) - ID komentar yang akan dihapus

**Response Success (200):**
```json
{
    "success": true,
    "message": "Comment deleted successfully",
    "data": null
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "You are not authorized to delete this comment"
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Comment not found for this announcement"
}
```

---

## 🔐 Authentication
Semua endpoints ini memerlukan authentication token. Gunakan token yang didapat dari endpoint login:

```
Authorization: Bearer {your_token_here}
```

---

## 📝 Notes

### Bookmark Features:
- User hanya bisa bookmark pengumuman yang aktif dan sudah published
- Satu user tidak bisa bookmark pengumuman yang sama lebih dari sekali (unique constraint)
- Toggle bookmark akan otomatis menambah atau menghapus bookmark
- Bookmarked announcements diurutkan berdasarkan waktu bookmark (terbaru dulu)

### Comment Features:
- Komentar hanya bisa ditambahkan jika `allow_comments` = true pada pengumuman
- Komentar otomatis approved (is_approved = true)
- User hanya bisa menghapus komentar miliknya sendiri
- Admin bisa menghapus semua komentar
- Soft delete digunakan, jadi komentar tidak benar-benar dihapus dari database

---

## 📱 Example Usage (Android/Mobile)

### Kotlin Example - Toggle Bookmark:
```kotlin
// Toggle bookmark
fun toggleBookmark(announcementId: Int, token: String) {
    val url = "https://your-api.com/api/announcements/$announcementId/bookmark"
    
    val request = Request.Builder()
        .url(url)
        .addHeader("Authorization", "Bearer $token")
        .post(RequestBody.create(null, ""))
        .build()
    
    client.newCall(request).enqueue(object : Callback {
        override fun onResponse(call: Call, response: Response) {
            val json = JSONObject(response.body?.string())
            val isBookmarked = json.getJSONObject("data").getBoolean("is_bookmarked")
            // Update UI
        }
    })
}
```

### Kotlin Example - Get Bookmarked:
```kotlin
fun getBookmarkedAnnouncements(token: String) {
    val url = "https://your-api.com/api/announcements/bookmarked"
    
    val request = Request.Builder()
        .url(url)
        .addHeader("Authorization", "Bearer $token")
        .get()
        .build()
    
    client.newCall(request).enqueue(object : Callback {
        override fun onResponse(call: Call, response: Response) {
            val json = JSONObject(response.body?.string())
            val announcements = json.getJSONArray("data")
            // Display in RecyclerView
        }
    })
}
```

### Kotlin Example - Add Comment:
```kotlin
fun addComment(announcementId: Int, content: String, token: String) {
    val url = "https://your-api.com/api/announcements/$announcementId/comments"
    
    val jsonBody = JSONObject()
    jsonBody.put("content", content)
    
    val body = RequestBody.create(
        "application/json".toMediaType(),
        jsonBody.toString()
    )
    
    val request = Request.Builder()
        .url(url)
        .addHeader("Authorization", "Bearer $token")
        .post(body)
        .build()
    
    client.newCall(request).enqueue(object : Callback {
        override fun onResponse(call: Call, response: Response) {
            // Comment added successfully
        }
    })
}
```

---

## 🧪 Testing with Postman

### Collection Setup:
1. Import this collection to Postman
2. Set environment variable `base_url` = `http://localhost:8000/api`
3. Set environment variable `token` = your authentication token

### Test Scenarios:

**Scenario 1: Bookmark Flow**
1. POST `/announcements/1/bookmark` → Should add bookmark
2. POST `/announcements/1/bookmark` → Should remove bookmark
3. GET `/announcements/bookmarked` → Should list bookmarked items

**Scenario 2: Comment Flow**
1. GET `/announcements/1/comments` → Get existing comments
2. POST `/announcements/1/comments` → Add new comment
3. DELETE `/announcements/1/comments/5` → Delete your comment

---

## 🛠 Database Schema

### announcement_bookmarks Table:
```sql
CREATE TABLE announcement_bookmarks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    announcement_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_user_announcement (user_id, announcement_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE
);
```

---

## ✅ Checklist Implementation

- [x] Database migration created
- [x] Models updated with relationships
- [x] API routes registered
- [x] Controller methods implemented
- [x] Authentication middleware applied
- [x] Authorization checks implemented
- [x] Documentation created

---

## 📞 Support
Jika ada pertanyaan atau issue, silakan hubungi tim developer.
