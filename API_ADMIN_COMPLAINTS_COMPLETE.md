# 🎯 Admin Complaints API - Complete Endpoints

## 📋 Overview
Dokumentasi lengkap untuk semua endpoint Admin Complaint Management API yang sudah ditambahkan.

---

## ✅ Endpoints Summary

| Method | Endpoint | Description | Status |
|--------|----------|-------------|--------|
| GET | `/api/admin/complaints` | List all complaints | ✅ Complete |
| POST | `/api/admin/complaints` | Create new complaint | ✅ Complete |
| GET | `/api/admin/complaints/statistics` | Get statistics | ✅ Complete |
| GET | `/api/admin/complaints/{id}` | Get complaint details | ✅ Complete |
| PUT | `/api/admin/complaints/{id}` | Update complaint | ✅ Complete |
| DELETE | `/api/admin/complaints/{id}` | Delete complaint | ✅ Complete |
| PATCH | `/api/admin/complaints/{id}/status` | Update status | ✅ Complete |
| POST | `/api/admin/complaints/{id}/resolve` | Mark as re  solved with photos | ✅ **NEW** |
| POST | `/api/admin/complaints/{id}/response` | Add response | ✅ Complete |
| DELETE | `/api/admin/complaints/attachments/{id}` | Delete attachment | ✅ Complete |
| POST | `/api/admin/complaints/bulk-update` | Bulk actions | ✅ Complete |

**Total Endpoints**: 11

---

## 🆕 New Endpoints Detail

### 1. Create Complaint (POST)

**Endpoint**: `POST /api/admin/complaints`

**Description**: Admin can create complaint on behalf of user

**Request Body**:
```json
{
  "user_id": 10,
  "title": "Jalan Rusak di RT 05",
  "description": "Jalan berlubang sudah 2 minggu tidak diperbaiki",
  "category_id": 1,
  "location": "Jl. Raya RT 05 RW 03",
  "priority": "high",
  "status": "pending",
  "report_date": "2025-11-03",
  "photo": "(file)",
  "attachments[]": ["(file1)", "(file2)"]
}
```

**Validation Rules**:
- `user_id`: required, must exist in users table
- `title`: required, max 255 chars
- `description`: required
- `category_id`: required, must exist
- `location`: required, max 255 chars
- `priority`: optional, enum(low, medium, high, urgent)
- `status`: optional, enum(pending, in_progress, resolved, rejected)
- `photo`: optional, image (jpeg,png,jpg,gif), max 2MB
- `attachments.*`: optional, file, max 10MB each
- `report_date`: optional, date format

**Success Response** (201):
```json
{
  "success": true,
  "message": "Complaint created successfully",
  "data": {
    "id": 15,
    "user_id": 10,
    "title": "Jalan Rusak di RT 05",
    "description": "Jalan berlubang sudah 2 minggu tidak diperbaiki",
    "category_id": 1,
    "location": "Jl. Raya RT 05 RW 03",
    "priority": "high",
    "status": "pending",
    "photo": "complaints/photos/abc123.jpg",
    "photo_url": "http://localhost:8000/storage/complaints/photos/abc123.jpg",
    "report_date": "2025-11-03",
    "created_at": "2025-11-03T10:30:25Z",
    "updated_at": "2025-11-03T10:30:25Z",
    "user": {
      "id": 10,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "category": {
      "id": 1,
      "name": "Infrastruktur",
      "icon": "road",
      "color": "#FF5733"
    },
    "attachments": [
      {
        "id": 20,
        "file_name": "bukti.jpg",
        "file_url": "http://localhost:8000/storage/complaints/bukti.jpg",
        "file_size_human": "1.2 MB"
      }
    ]
  }
}
```

---

### 2. Update Complaint (PUT)

**Endpoint**: `PUT /api/admin/complaints/{id}`

**Description**: Update complaint data (title, description, category, etc.)

**Request Body** (all fields optional):
```json
{
  "title": "Jalan Rusak di RT 05 (Updated)",
  "description": "Updated description",
  "category_id": 2,
  "location": "New location",
  "priority": "urgent",
  "status": "in_progress",
  "admin_response": "Sedang ditindaklanjuti",
  "estimated_resolution": "2025-11-10",
  "photo": "(file)",
  "attachments[]": ["(file1)", "(file2)"],
  "delete_attachments": [5, 8]
}
```

**Validation Rules**:
- `title`: sometimes required, max 255
- `description`: sometimes required
- `category_id`: sometimes required, must exist
- `location`: sometimes required, max 255
- `priority`: sometimes required, enum(low,medium,high,urgent)
- `status`: sometimes required, enum(pending,in_progress,resolved,rejected)
- `admin_response`: optional
- `estimated_resolution`: optional, date
- `photo`: optional, image, max 2MB (replaces old photo)
- `attachments.*`: optional, file, max 10MB
- `delete_attachments`: optional, array of attachment IDs to delete

**Success Response** (200):
```json
{
  "success": true,
  "message": "Complaint updated successfully",
  "data": {
    "id": 15,
    "title": "Jalan Rusak di RT 05 (Updated)",
    "description": "Updated description",
    "priority": "urgent",
    "status": "in_progress",
    "admin_response": "Sedang ditindaklanjuti",
    "estimated_resolution": "2025-11-10",
    "updated_at": "2025-11-03T11:15:30Z",
    "user": { ... },
    "category": { ... },
    "attachments": [ ... ]
  }
}
```

**Use Cases**:
- Edit complaint details
- Change priority level
- Update location
- Change category
- Add/remove attachments
- Replace photo

---

### 3. Delete Complaint (DELETE)

**Endpoint**: `DELETE /api/admin/complaints/{id}`

**Description**: Permanently delete complaint and all associated files

**Success Response** (200):
```json
{
  "success": true,
  "message": "Complaint deleted successfully"
}
```

**What Gets Deleted**:
- ✅ Complaint record from database
- ✅ Main photo file from storage
- ✅ All attachment files from storage
- ✅ All attachment records from database
- ✅ All responses related to complaint

**Error Response** (404):
```json
{
  "success": false,
  "message": "Complaint not found"
}
```

---

### 4. Mark Complaint as Resolved (NEW)

**Endpoint**: `POST /api/admin/complaints/{id}/resolve`

**Description**: Mark complaint as resolved with resolution response and documentation photos

**Request Body** (multipart/form-data):
```json
{
  "resolution_response": "Jalan sudah diperbaiki. Tim sudah melakukan pengaspalan ulang pada area yang rusak.",
  "resolution_photos[]": ["(photo1)", "(photo2)", "(photo3)"]
}
```

**Validation Rules**:
- `resolution_response`: required, string (penjelasan bagaimana keluhan diselesaikan)
- `resolution_photos`: optional, array, max 3 photos
- `resolution_photos.*`: image (jpeg,png,jpg), max 2MB per photo

**Success Response** (200):
```json
{
  "success": true,
  "message": "Complaint marked as resolved successfully",
  "meta": {
    "previous_status": "in_progress",
    "current_status": "resolved",
    "resolution_photos_count": 2
  },
  "data": {
    "complaint": {
      "id": 15,
      "title": "Jalan Rusak di RT 05",
      "status": "resolved",
      "admin_response": "Jalan sudah diperbaiki. Tim sudah melakukan pengaspalan ulang pada area yang rusak.",
      "updated_at": "2025-11-04T08:45:30Z",
      "user": {
        "id": 10,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "category": {
        "id": 1,
        "name": "Infrastruktur",
        "icon": "road",
        "color": "#FF5733"
      }
    },
    "resolution_photos": [
      {
        "id": 25,
        "file_name": "setelah_perbaikan_1.jpg",
        "file_url": "http://localhost:8000/storage/complaints/resolutions/xyz789.jpg",
        "file_size_human": "1.5 MB"
      },
      {
        "id": 26,
        "file_name": "setelah_perbaikan_2.jpg",
        "file_url": "http://localhost:8000/storage/complaints/resolutions/abc456.jpg",
        "file_size_human": "1.8 MB"
      }
    ]
  }
}
```

**What Happens**:
1. ✅ Complaint status changed to `resolved`
2. ✅ `admin_response` updated with resolution text
3. ✅ Creates a new Response record with resolution message
4. ✅ Uploads resolution photos to `complaints/resolutions/` folder
5. ✅ Attachments created with `attachment_type` = `'resolution'`
6. ✅ Triggers `ComplaintStatusChanged` event (sends notification to user)

**Use Case**:
Admin sudah menyelesaikan complaint dan ingin:
- Memberitahu user bahwa masalah sudah selesai
- Upload foto dokumentasi hasil perbaikan
- Otomatis ubah status ke "resolved"

**Difference from `updateStatus`**:
- `updateStatus`: General status update (any status)
- `markAsResolved`: Specifically for completing complaints with documentation

---

## 🔧 Updated Endpoints

### 5. Add Response (Fixed)

**Endpoint**: `POST /api/admin/complaints/{id}/response`

**Previous Issue**: 
```
❌ SQLSTATE[23502]: null value in column "user_id"
Using: admin_id (doesn't exist) and message (wrong field)
```

**Fixed Mapping**:
```php
// ✅ Now uses correct fields
'user_id' => $request->user()->id,  // Correct field
'content' => $request->message,      // Correct field
```

**Request Body**:
```json
{
  "message": "Terima kasih laporannya, sedang ditindaklanjuti"
}
```

**Success Response** (201):
```json
{
  "success": true,
  "message": "Response added successfully",
  "data": {
    "id": 1,
    "complaint_id": 3,
    "user_id": 1,
    "content": "Terima kasih laporannya, sedang ditindaklanjuti",
    "photo": null,
    "photo_url": null,
    "created_at": "2025-11-03T03:32:20Z",
    "updated_at": "2025-11-03T03:32:20Z",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    }
  }
}
```

---

## 📊 Complete Feature Comparison

### Web Controller vs API Controller

| Feature | Web | API | Status |
|---------|-----|-----|--------|
| List complaints with filters | ✅ | ✅ | Complete |
| Create complaint | ✅ | ✅ | Complete |
| View detail | ✅ | ✅ | Complete |
| Update complaint | ✅ | ✅ | Complete |
| Delete complaint | ✅ | ✅ | Complete |
| Update status | ✅ | ✅ | Complete |
| Mark as resolved with photos | ✅ | ✅ | **Added** |
| Add response | ✅ | ✅ | Complete |
| Delete attachment | ✅ | ✅ | Complete |
| Bulk actions | ✅ | ✅ | Complete |
| Statistics | ✅ | ✅ | Complete |
| Print complaint | ✅ | ❌ | Optional |
| Export PDF | ⏳ | ❌ | Optional |
| Export Excel | ⏳ | ❌ | Optional |

**CRUD Completeness**: ✅ **100%**

---

## 🎯 CRUD Operations Summary

### ✅ CREATE
- **POST** `/api/admin/complaints`
- Admin can create complaint on behalf of user
- Supports photo upload and multiple attachments

### ✅ READ
- **GET** `/api/admin/complaints` - List with filters
- **GET** `/api/admin/complaints/{id}` - Single detail
- **GET** `/api/admin/complaints/statistics` - Statistics

### ✅ UPDATE
- **PUT** `/api/admin/complaints/{id}` - Full update
- **PATCH** `/api/admin/complaints/{id}/status` - Status only
- **POST** `/api/admin/complaints/{id}/resolve` - Mark as resolved with photos
- **POST** `/api/admin/complaints/{id}/response` - Add response
- **POST** `/api/admin/complaints/bulk-update` - Bulk actions

### ✅ DELETE
- **DELETE** `/api/admin/complaints/{id}` - Delete complaint
- **DELETE** `/api/admin/complaints/attachments/{id}` - Delete attachment

---

## 📝 Use Case Examples

### Use Case 1: Admin Creates Complaint for Elderly User (No Smartphone)

**Scenario**: Pak Ahmad (70 tahun) lapor ke kantor kelurahan secara langsung

**Steps**:
1. Admin login
2. GET `/api/admin/users?search=ahmad` - Find user
3. POST `/api/admin/complaints` with user_id
4. Upload photo yang difoto admin

**Request**:
```bash
POST /api/admin/complaints
Content-Type: multipart/form-data

{
  "user_id": 45,
  "title": "Lampu Jalan Mati",
  "description": "Lampu jalan RT 03 sudah mati 1 minggu",
  "category_id": 3,
  "location": "Jl. Melati RT 03",
  "priority": "high",
  "photo": (photo file taken by admin)
}
```

---

### Use Case 2: Admin Edits Complaint Details

**Scenario**: User salah input kategori, admin perbaiki

**Request**:
```bash
PUT /api/admin/complaints/15

{
  "category_id": 5,
  "priority": "urgent",
  "admin_response": "Kategori telah diperbaiki"
}
```

---

### Use Case 3: Admin Adds Response with Status Update

**Scenario**: Admin update status + kasih tanggapan

**Step 1** - Update status:
```bash
PATCH /api/admin/complaints/15/status

{
  "status": "in_progress",
  "notes": "Tim sudah turun ke lokasi untuk survey"
}
```

**Step 2** - Add additional response:
```bash
POST /api/admin/complaints/15/response

{
  "message": "Estimasi selesai 3 hari kerja"
}
```

---

### Use Case 5: Mark Complaint as Resolved with Documentation

**Scenario**: Perbaikan jalan sudah selesai, admin upload foto hasil perbaikan

**Request**:
```bash
POST /api/admin/complaints/15/resolve
Content-Type: multipart/form-data

{
  "resolution_response": "Jalan sudah diperbaiki. Tim sudah melakukan pengaspalan ulang pada area yang rusak. Perbaikan selesai pada tanggal 4 November 2025.",
  "resolution_photos[]": [
    (foto_setelah_perbaikan_1.jpg),
    (foto_setelah_perbaikan_2.jpg)
  ]
}
```

**Response includes**:
- Updated complaint with status = "resolved"
- List of uploaded resolution photos
- Meta information (previous status → current status)

---

### Use Case 6: Delete Spam Complaint

**Scenario**: Ada complaint spam/test yang harus dihapus

**Request**:
```bash
DELETE /api/admin/complaints/99
```

---

## 🔐 Authorization

All endpoints require:
- ✅ Authentication: `Bearer Token` (Sanctum)
- ✅ Role: `admin`

**Headers**:
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: multipart/form-data (for file uploads)
```

---

## 🎨 Response Format

All endpoints follow standardized format:

**Success**:
```json
{
  "success": true,
  "message": "...",
  "data": { ... }
}
```

**Success with Pagination**:
```json
{
  "success": true,
  "message": "...",
  "meta": {
    "current_page": 1,
    "has_next_page": true,
    "has_prev_page": false,
    "next_page": "?page=2"
  },
  "data": [ ... ]
}
```

**Error**:
```json
{
  "success": false,
  "message": "...",
  "errors": { ... }
}
```

---

## ✅ Summary

### What's New:
1. ✅ **POST /complaints** - Create complaint
2. ✅ **PUT /complaints/{id}** - Update complaint  
3. ✅ **DELETE /complaints/{id}** - Delete complaint
4. ✅ **POST /complaints/{id}/resolve** - Mark as resolved with resolution photos

### What's Fixed:
5. ✅ **POST /complaints/{id}/response** - Fixed database field mapping

### Total Endpoints: 11
- ✅ 4 NEW endpoints
- ✅ 1 FIXED endpoint
- ✅ 6 existing endpoints

### CRUD Status: ✅ **100% COMPLETE**

---

**Last Updated**: November 4, 2025  
**API Version**: 2.2.0  
**Status**: ✅ Production Ready
