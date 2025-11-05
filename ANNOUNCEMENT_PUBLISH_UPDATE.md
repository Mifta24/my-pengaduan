# Announcement Publish/Unpublish API Enhancement ✅

## Overview
Enhanced `publish` and `unpublish` endpoints untuk announcement dengan menambahkan meta informasi status dan detail author.

## Implementation Date
November 4, 2025

---

## Changes Made

### 1. Model Enhancement
**File**: `app/Models/Announcement.php`

**Added**:
- ✅ `status` accessor - Dynamic attribute untuk menentukan status published/unpublished
- ✅ `$appends = ['status']` - Otomatis include status dalam JSON response

**Status Logic**:
```php
public function getStatusAttribute()
{
    if ($this->is_active && $this->published_at && $this->published_at <= now()) {
        return 'published';
    }
    return 'unpublished';
}
```

**Status Rules**:
- `published`: `is_active = true` AND `published_at <= now()`
- `unpublished`: semua kondisi lainnya

---

### 2. Controller Enhancement
**File**: `app/Http/Controllers/Api/Admin/AnnouncementController.php`

**Updated Methods**:
- ✅ `publish($id)` - Publish announcement dengan meta status
- ✅ `unpublish($id)` - Unpublish announcement dengan meta status

**New Features**:
1. **Status Tracking**: Simpan previous_status sebelum update
2. **Author Information**: Include author id & name
3. **Meta Object**: Tampilkan perubahan status
4. **Structured Response**: Format data konsisten

---

## API Endpoints

### A. Publish Announcement
**Endpoint**: `POST /api/admin/announcements/{id}/publish`

**Authentication**: Required (Admin only)

**Request**:
```http
POST /api/admin/announcements/2/publish
Authorization: Bearer {token}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Announcement published successfully",
    "meta": {
        "previous_status": "unpublished",
        "current_status": "published"
    },
    "data": {
        "id": 2,
        "title": "Pengumuman Penting",
        "summary": "Ringkasan pengumuman",
        "content": "Isi lengkap pengumuman...",
        "priority": "high",
        "is_active": true,
        "status": "published",
        "is_sticky": false,
        "allow_comments": true,
        "published_at": "2025-11-04T10:30:00.000000Z",
        "updated_at": "2025-11-04T10:30:00Z",
        "views_count": 0,
        "author": {
            "id": 1,
            "name": "Admin RT"
        }
    }
}
```

**What Happens**:
1. Set `is_active = true`
2. Set `published_at = now()`
3. Status berubah dari `unpublished` → `published`
4. Trigger `AnnouncementCreated` event (untuk notifikasi)

---

### B. Unpublish Announcement
**Endpoint**: `POST /api/admin/announcements/{id}/unpublish`

**Authentication**: Required (Admin only)

**Request**:
```http
POST /api/admin/announcements/2/unpublish
Authorization: Bearer {token}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Announcement unpublished successfully",
    "meta": {
        "previous_status": "published",
        "current_status": "unpublished"
    },
    "data": {
        "id": 2,
        "title": "Pengumuman Penting",
        "summary": "Ringkasan pengumuman",
        "content": "Isi lengkap pengumuman...",
        "priority": "medium",
        "is_active": false,
        "status": "unpublished",
        "is_sticky": true,
        "allow_comments": true,
        "published_at": "2025-11-04T02:12:02.000000Z",
        "updated_at": "2025-11-04T02:13:48Z",
        "views_count": 1,
        "author": {
            "id": 1,
            "name": "Admin RT"
        }
    }
}
```

**What Happens**:
1. Set `is_active = false`
2. `published_at` tetap (tidak diubah)
3. Status berubah dari `published` → `unpublished`
4. Tidak ada event trigger (karena unpublish)

---

## Response Structure

### Meta Object
```json
{
    "previous_status": "published|unpublished",
    "current_status": "published|unpublished"
}
```

**Purpose**: 
- Tracking perubahan status
- Audit trail
- Frontend dapat menampilkan notifikasi perubahan

### Data Object
```json
{
    "id": 2,
    "title": "string",
    "summary": "string|null",
    "content": "string",
    "priority": "low|medium|high|urgent",
    "is_active": true|false,
    "status": "published|unpublished",
    "is_sticky": true|false,
    "allow_comments": true|false,
    "published_at": "ISO 8601 timestamp",
    "updated_at": "ISO 8601 timestamp",
    "views_count": integer,
    "author": {
        "id": integer,
        "name": "string"
    }
}
```

**Fields**:
- `id`: ID announcement
- `title`: Judul announcement
- `summary`: Ringkasan (optional)
- `content`: Isi lengkap
- `priority`: Tingkat prioritas
- `is_active`: Boolean status aktif
- `status`: Dynamic status (published/unpublished)
- `is_sticky`: Apakah di-pin di atas
- `allow_comments`: Izinkan komentar
- `published_at`: Waktu publikasi
- `updated_at`: Waktu update terakhir
- `views_count`: Jumlah views
- `author`: Informasi pembuat announcement

---

## Status vs is_active

### Perbedaan:
| Field | Type | Description |
|-------|------|-------------|
| `is_active` | boolean | Database field - true/false |
| `status` | string | Dynamic accessor - "published"/"unpublished" |

### Logic:
```
status = "published" when:
  - is_active = true
  - published_at exists
  - published_at <= now()

status = "unpublished" when:
  - is_active = false
  OR published_at is null
  OR published_at > now()
```

### Example Scenarios:

**Scenario 1: Scheduled Future Publish**
```json
{
    "is_active": true,
    "published_at": "2025-11-10T00:00:00Z",  // Future date
    "status": "unpublished"  // Not yet published
}
```

**Scenario 2: Currently Published**
```json
{
    "is_active": true,
    "published_at": "2025-11-01T00:00:00Z",  // Past date
    "status": "published"  // Active and published
}
```

**Scenario 3: Unpublished**
```json
{
    "is_active": false,
    "published_at": "2025-11-01T00:00:00Z",
    "status": "unpublished"  // Deactivated
}
```

---

## Error Responses

### Announcement Not Found
```json
{
    "success": false,
    "message": "Announcement not found"
}
```
**Status Code**: 404 Not Found

### Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized"
}
```
**Status Code**: 401 Unauthorized

---

## Testing Checklist

### Publish Endpoint
- [ ] ✅ Publish announcement yang unpublished
- [ ] ✅ Previous status = "unpublished"
- [ ] ✅ Current status = "published"
- [ ] ✅ `is_active` berubah jadi `true`
- [ ] ✅ `published_at` di-set ke waktu sekarang
- [ ] ✅ Author information muncul
- [ ] ✅ Event `AnnouncementCreated` triggered

### Unpublish Endpoint
- [ ] ✅ Unpublish announcement yang published
- [ ] ✅ Previous status = "published"
- [ ] ✅ Current status = "unpublished"
- [ ] ✅ `is_active` berubah jadi `false`
- [ ] ✅ `published_at` tidak berubah
- [ ] ✅ Author information muncul
- [ ] ✅ Tidak ada event trigger

### Edge Cases
- [ ] Publish announcement yang sudah published (previous & current sama)
- [ ] Unpublish announcement yang sudah unpublished (previous & current sama)
- [ ] Announcement tanpa author (null handling)
- [ ] Invalid announcement ID (404 response)

---

## Example Usage

### Frontend Integration (JavaScript)

**Publish Announcement**:
```javascript
async function publishAnnouncement(announcementId) {
    try {
        const response = await fetch(`/api/admin/announcements/${announcementId}/publish`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        
        if (result.success) {
            // Show success notification
            showNotification(
                `Announcement ${result.data.title} published successfully!`,
                'success'
            );
            
            // Log status change
            console.log(
                `Status changed: ${result.meta.previous_status} → ${result.meta.current_status}`
            );
            
            // Update UI
            updateAnnouncementStatus(result.data);
        }
    } catch (error) {
        console.error('Failed to publish:', error);
    }
}
```

**Unpublish Announcement**:
```javascript
async function unpublishAnnouncement(announcementId) {
    try {
        const response = await fetch(`/api/admin/announcements/${announcementId}/unpublish`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        
        if (result.success) {
            // Show success notification
            showNotification(
                `Announcement unpublished`,
                'info'
            );
            
            // Update UI to show unpublished state
            updateAnnouncementStatus(result.data);
        }
    } catch (error) {
        console.error('Failed to unpublish:', error);
    }
}
```

---

## Benefits

### 1. Status Transparency
- Clear status tracking dengan `previous_status` dan `current_status`
- Frontend bisa tampilkan "Status changed from X to Y"

### 2. Author Attribution
- Setiap announcement menampilkan siapa yang membuatnya
- Helpful untuk audit dan accountability

### 3. Consistent Response
- Format response yang konsisten dan terstruktur
- Mudah di-parse oleh frontend

### 4. Flexible Status Logic
- `status` accessor otomatis calculate berdasarkan `is_active` dan `published_at`
- Support scheduled publishing (future dates)

---

## Database Schema

**Table**: `announcements`

**Relevant Columns**:
```sql
id              BIGINT
title           VARCHAR(255)
summary         TEXT
content         TEXT
priority        ENUM('low', 'medium', 'high', 'urgent')
is_active       BOOLEAN
is_sticky       BOOLEAN
allow_comments  BOOLEAN
published_at    TIMESTAMP
views_count     INTEGER
author_id       BIGINT (FK to users.id)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## Summary

### Files Modified: 2
1. ✅ `app/Models/Announcement.php` - Added status accessor & appends
2. ✅ `app/Http/Controllers/Api/Admin/AnnouncementController.php` - Enhanced publish/unpublish methods

### New Features: 4
1. ✅ Dynamic `status` attribute
2. ✅ `previous_status` & `current_status` meta
3. ✅ Author information in response
4. ✅ Structured data format

### Breaking Changes: 0
- Backward compatible
- Hanya menambah informasi, tidak menghilangkan

### API Endpoints: 2
- `POST /api/admin/announcements/{id}/publish`
- `POST /api/admin/announcements/{id}/unpublish`

---

**Implementation Status**: ✅ **COMPLETE**  
**Tested**: Pending manual testing  
**Ready for**: Frontend integration  

