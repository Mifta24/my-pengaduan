# API Quick Testing Guide

## Testing dengan Postman atau cURL

### 1. Setup Authentication

#### Login untuk mendapatkan token
```bash
POST http://localhost/api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxx",
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    }
  }
}
```

**Simpan token** untuk request selanjutnya!

---

## 2. Test User API Endpoints

### User Dashboard
```bash
GET http://localhost/api/dashboard
Authorization: Bearer {user_token}
```

Response:
```json
{
  "success": true,
  "data": {
    "stats": {
      "total": 10,
      "pending": 3,
      "in_progress": 5,
      "resolved": 2,
      "rejected": 0
    },
    "recent_complaints": [...],
    "announcements": [...]
  }
}
```

### Create Complaint
```bash
POST http://localhost/api/complaints
Authorization: Bearer {user_token}
Content-Type: multipart/form-data

title: Jalan Rusak
description: Jalan berlubang di depan kantor
category_id: 1
location: Jl. Merdeka No. 123
attachments[0]: [file upload]
attachments[1]: [file upload]
```

### Track Complaint
```bash
GET http://localhost/api/complaints/1/track
Authorization: Bearer {user_token}
```

Response:
```json
{
  "success": true,
  "data": {
    "complaint": {
      "id": 1,
      "title": "Jalan Rusak",
      "status": "in_progress",
      ...
    },
    "timeline": [
      {
        "type": "response",
        "title": "Tanggapan dari Admin",
        "description": "Sedang kami proses",
        "created_at": "2025-10-23T10:30:00"
      },
      {
        "type": "status_change",
        "status": "in_progress",
        "title": "Sedang Diproses",
        "created_at": "2025-10-23T10:00:00"
      },
      {
        "type": "created",
        "status": "pending",
        "title": "Pengaduan Dibuat",
        "created_at": "2025-10-23T09:00:00"
      }
    ]
  }
}
```

### Get Active Categories
```bash
GET http://localhost/api/categories
Authorization: Bearer {user_token}
```

### List Announcements
```bash
GET http://localhost/api/announcements?per_page=10
Authorization: Bearer {user_token}
```

### Add Comment to Announcement
```bash
POST http://localhost/api/announcements/1/comments
Authorization: Bearer {user_token}
Content-Type: application/json

{
  "content": "Terima kasih atas informasinya"
}
```

---

## 3. Test Admin API Endpoints

### Admin Dashboard
```bash
GET http://localhost/api/admin/dashboard
Authorization: Bearer {admin_token}
```

Response:
```json
{
  "success": true,
  "data": {
    "complaints": {
      "total": 150,
      "pending": 20,
      "in_progress": 50,
      "resolved": 70,
      "rejected": 10,
      "by_category": [...]
    },
    "users": {
      "total": 100,
      "verified": 80,
      "unverified": 20
    },
    "monthly_data": [...],
    "recent_activities": [...]
  }
}
```

### Quick Stats (untuk mobile)
```bash
GET http://localhost/api/admin/dashboard/quick-stats
Authorization: Bearer {admin_token}
```

### List All Complaints
```bash
GET http://localhost/api/admin/complaints?status=pending&per_page=20
Authorization: Bearer {admin_token}
```

Filter parameters:
- `status` - pending, in_progress, resolved, rejected
- `category` - category ID
- `search` - search keyword
- `per_page` - items per page

### Update Complaint Status
```bash
PUT http://localhost/api/admin/complaints/1/status
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "status": "in_progress",
  "admin_note": "Sedang kami proses, akan diselesaikan dalam 3 hari"
}
```

### Add Admin Response
```bash
POST http://localhost/api/admin/complaints/1/response
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "response": "Laporan Anda sudah kami terima dan sedang dalam proses",
  "is_internal": false
}
```

### Complaint Statistics
```bash
GET http://localhost/api/admin/complaints/statistics
Authorization: Bearer {admin_token}
```

### Bulk Update Complaints
```bash
POST http://localhost/api/admin/complaints/bulk-update
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "complaint_ids": [1, 2, 3],
  "action": "update_status",
  "status": "in_progress"
}
```

### Category Management
```bash
# List categories
GET http://localhost/api/admin/categories

# Create category
POST http://localhost/api/admin/categories
{
  "name": "Infrastruktur",
  "description": "Pengaduan terkait infrastruktur",
  "is_active": true
}

# Update category
PUT http://localhost/api/admin/categories/1
{
  "name": "Infrastruktur Jalan",
  "description": "Update description"
}

# Toggle status
PUT http://localhost/api/admin/categories/1/toggle-status

# Delete category
DELETE http://localhost/api/admin/categories/1
```

### User Management
```bash
# List users
GET http://localhost/api/admin/users?role=user&is_verified=1

# Create user
POST http://localhost/api/admin/users
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "user"
}

# Verify email
PUT http://localhost/api/admin/users/1/verify-email

# Verify identity
PUT http://localhost/api/admin/users/1/verify-user

# Change role
PUT http://localhost/api/admin/users/1/change-role
{
  "role": "admin"
}

# Reset password
POST http://localhost/api/admin/users/1/reset-password
{
  "password": "newpassword123"
}
```

### Announcement Management
```bash
# List announcements
GET http://localhost/api/admin/announcements

# Create announcement
POST http://localhost/api/admin/announcements
Content-Type: multipart/form-data

title: Pemeliharaan Website
content: Website akan maintenance pada tanggal...
priority: high
is_sticky: 1
allow_comments: 1
image: [file upload]

# Publish announcement
PUT http://localhost/api/admin/announcements/1/publish

# Toggle sticky
PUT http://localhost/api/admin/announcements/1/toggle-sticky
```

---

## 4. Common Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "title": ["Judul wajib diisi"],
    "category_id": ["Kategori tidak valid"]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "Anda tidak memiliki akses ke pengaduan ini"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Pengaduan tidak ditemukan"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Terjadi kesalahan saat memproses request"
}
```

---

## 5. Testing Checklist

### User Endpoints
- [ ] Login berhasil & dapat token
- [ ] Dashboard menampilkan stats user
- [ ] Buat pengaduan baru dengan attachment
- [ ] Lihat daftar pengaduan user
- [ ] Track pengaduan dengan timeline
- [ ] Lihat detail pengaduan
- [ ] Update pengaduan (jika status pending)
- [ ] Hapus pengaduan (jika status pending)
- [ ] Lihat daftar kategori aktif
- [ ] Lihat daftar pengumuman
- [ ] Tambah komentar di pengumuman

### Admin Endpoints
- [ ] Login sebagai admin
- [ ] Dashboard admin dengan statistik lengkap
- [ ] Quick stats untuk mobile
- [ ] Lihat semua pengaduan dengan filter
- [ ] Update status pengaduan
- [ ] Tambah response/tanggapan
- [ ] Lihat statistik pengaduan
- [ ] Bulk update pengaduan
- [ ] CRUD kategori
- [ ] Bulk action kategori
- [ ] CRUD user
- [ ] Verify email & identity user
- [ ] Change role user
- [ ] Reset password user
- [ ] CRUD announcement
- [ ] Upload gambar announcement
- [ ] Publish/unpublish announcement
- [ ] Toggle sticky announcement

---

## 6. Postman Collection Setup

### Environment Variables
Buat environment di Postman dengan variables:
```
base_url: http://localhost
user_token: (akan diisi setelah login)
admin_token: (akan diisi setelah login admin)
```

### Pre-request Script (Global)
```javascript
// Auto-set authorization header
if (pm.environment.get("user_token")) {
    pm.request.headers.add({
        key: "Authorization",
        value: "Bearer " + pm.environment.get("user_token")
    });
}
```

### Test Script (untuk Login)
```javascript
// Save token after login
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("user_token", jsonData.data.token);
}
```

---

## 7. Quick cURL Examples

### Login
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Dashboard (dengan token)
```bash
curl -X GET http://localhost/api/dashboard \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxx"
```

### Create Complaint dengan file
```bash
curl -X POST http://localhost/api/complaints \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxx" \
  -F "title=Jalan Rusak" \
  -F "description=Jalan berlubang" \
  -F "category_id=1" \
  -F "location=Jl. Merdeka" \
  -F "attachments[]=@/path/to/image1.jpg" \
  -F "attachments[]=@/path/to/image2.jpg"
```

---

## 8. Tips Testing

1. **Gunakan Postman** untuk testing yang lebih mudah
2. **Simpan token** setelah login di environment variables
3. **Test validasi** dengan mengirim data tidak valid
4. **Test authorization** dengan token user ke endpoint admin
5. **Test file upload** dengan berbagai format file
6. **Check response status code** untuk setiap request
7. **Verify database** setelah create/update/delete
8. **Test edge cases** (data tidak ada, data sudah dihapus, dll)

---

## 9. Database Check

Setelah testing, cek database:

```sql
-- Check complaints
SELECT * FROM complaints ORDER BY created_at DESC LIMIT 5;

-- Check attachments
SELECT * FROM attachments WHERE complaint_id = 1;

-- Check responses
SELECT * FROM responses WHERE complaint_id = 1;

-- Check comments
SELECT * FROM comments WHERE commentable_type = 'App\\Models\\Announcement';

-- Check notifications sent
SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;
```

---

## 10. Mobile Developer Integration

Kirim informasi ini ke mobile developer:

1. **Base URL**: `http://your-domain.com/api`
2. **Authentication**: Bearer Token (dari `/api/login`)
3. **Token Header**: `Authorization: Bearer {token}`
4. **Accept Header**: `application/json`
5. **API Documentation**: `API_COMPLETION_SUMMARY.md`

Endpoint yang prioritas untuk mobile:
- `/api/login` - Authentication
- `/api/register` - Registration
- `/api/dashboard` - User dashboard
- `/api/complaints` - CRUD complaints
- `/api/complaints/{id}/track` - Track complaint
- `/api/categories` - Get categories
- `/api/announcements` - List announcements

---

**Happy Testing! 🚀**
