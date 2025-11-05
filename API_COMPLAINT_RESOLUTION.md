# 🎯 Complaint Resolution API - Complete Guide

## Overview
Endpoint khusus untuk menyelesaikan complaint dengan respon penyelesaian dan foto dokumentasi hasil perbaikan.

**Date**: November 4, 2025  
**Version**: 1.0.0

---

## Endpoint Details

### Mark Complaint as Resolved

**Endpoint**: `POST /api/admin/complaints/{id}/resolve`

**Method**: POST

**Authentication**: Required (Admin only)

**Content-Type**: `multipart/form-data`

---

## Request Parameters

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | ✅ Yes | ID complaint yang akan diselesaikan |

### Body Parameters

| Parameter | Type | Required | Max Size | Description |
|-----------|------|----------|----------|-------------|
| `resolution_response` | string | ✅ Yes | - | Penjelasan bagaimana keluhan diselesaikan |
| `resolution_photos[]` | file[] | ❌ No | 3 files | Foto dokumentasi penyelesaian (max 3 foto) |
| `resolution_photos.*` | image | - | 2 MB | Format: JPEG, PNG, JPG |

---

## Request Example

### cURL
```bash
curl -X POST http://localhost:8000/api/admin/complaints/15/resolve \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -F "resolution_response=Jalan sudah diperbaiki. Tim sudah melakukan pengaspalan ulang pada area yang rusak. Perbaikan selesai pada tanggal 4 November 2025." \
  -F "resolution_photos[]=@/path/to/foto_setelah_1.jpg" \
  -F "resolution_photos[]=@/path/to/foto_setelah_2.jpg"
```

### JavaScript (Fetch API)
```javascript
const formData = new FormData();
formData.append('resolution_response', 'Jalan sudah diperbaiki...');
formData.append('resolution_photos[]', photoFile1);
formData.append('resolution_photos[]', photoFile2);

const response = await fetch('/api/admin/complaints/15/resolve', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  },
  body: formData
});

const result = await response.json();
```

### Axios
```javascript
const formData = new FormData();
formData.append('resolution_response', 'Jalan sudah diperbaiki...');
formData.append('resolution_photos[]', photoFile1);
formData.append('resolution_photos[]', photoFile2);

const response = await axios.post(
  '/api/admin/complaints/15/resolve',
  formData,
  {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'multipart/form-data'
    }
  }
);
```

---

## Response Format

### Success Response (200 OK)

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
      "user_id": 10,
      "title": "Jalan Rusak di RT 05",
      "description": "Jalan berlubang sudah 2 minggu tidak diperbaiki",
      "category_id": 1,
      "location": "Jl. Raya RT 05 RW 03",
      "priority": "high",
      "status": "resolved",
      "photo": "complaints/photos/abc123.jpg",
      "photo_url": "http://localhost:8000/storage/complaints/photos/abc123.jpg",
      "admin_response": "Jalan sudah diperbaiki. Tim sudah melakukan pengaspalan ulang pada area yang rusak.",
      "report_date": "2025-11-03",
      "created_at": "2025-11-03T10:30:25Z",
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

### Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "resolution_response": [
      "The resolution response field is required."
    ],
    "resolution_photos.0": [
      "The resolution photos.0 must be an image.",
      "The resolution photos.0 must not be greater than 2048 kilobytes."
    ]
  }
}
```

#### Not Found (404)
```json
{
  "success": false,
  "message": "Complaint not found"
}
```

#### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## What Happens Behind the Scenes

### 1. Status Update
```php
// Complaint status changed to 'resolved'
$complaint->status = 'resolved';
```

### 2. Admin Response Saved
```php
// Resolution message saved to admin_response field
$complaint->admin_response = 'Jalan sudah diperbaiki...';
```

### 3. Response Record Created
```php
// Creates new Response record for history
ComplaintResponse::create([
    'complaint_id' => $complaint->id,
    'user_id' => $admin_id,
    'content' => 'Jalan sudah diperbaiki...'
]);
```

### 4. Photos Uploaded
```php
// Photos saved to storage/complaints/resolutions/
foreach ($photos as $photo) {
    $path = $photo->store('complaints/resolutions', 'public');
    
    Attachment::create([
        'attachable_type' => Complaint::class,
        'attachable_id' => $complaint->id,
        'file_path' => $path,
        'attachment_type' => 'resolution'  // Important!
    ]);
}
```

### 5. Event Triggered
```php
// Sends notification to user
event(new ComplaintStatusChanged($complaint, $oldStatus, 'resolved'));
```

---

## Database Schema

### Attachments Table

Resolution photos are stored with special `attachment_type`:

```sql
INSERT INTO attachments (
    attachable_type,
    attachable_id,
    file_name,
    file_path,
    file_size,
    mime_type,
    attachment_type,  -- 'resolution' for resolution photos
    created_at,
    updated_at
) VALUES (
    'App\Models\Complaint',
    15,
    'setelah_perbaikan.jpg',
    'complaints/resolutions/xyz789.jpg',
    1572864,
    'image/jpeg',
    'resolution',
    NOW(),
    NOW()
);
```

### Attachment Types

| Type | Description | Used For |
|------|-------------|----------|
| `complaint` | Original complaint attachments | User uploads saat buat complaint |
| `resolution` | Resolution documentation | Admin uploads saat selesaikan complaint |
| `response` | Response attachments | Admin/User uploads di response |

---

## Query Resolution Photos

### Get All Resolution Photos for a Complaint

```php
$resolutionPhotos = Attachment::where('attachable_type', Complaint::class)
    ->where('attachable_id', $complaintId)
    ->where('attachment_type', 'resolution')
    ->get();
```

### In Eloquent Relationship

```php
// In Complaint model
public function resolutionPhotos()
{
    return $this->morphMany(Attachment::class, 'attachable')
        ->where('attachment_type', 'resolution');
}

// Usage
$complaint->resolutionPhotos;
```

---

## Use Cases

### Use Case 1: Simple Resolution (Text Only)

**Scenario**: Masalah sudah selesai, tidak perlu foto dokumentasi

```bash
POST /api/admin/complaints/20/resolve

{
  "resolution_response": "Lampu sudah diperbaiki dan menyala normal kembali."
}
```

**Result**:
- Status: `resolved`
- Admin response: Updated
- Response record: Created
- Photos: None
- Notification: Sent to user

---

### Use Case 2: Resolution with Photos

**Scenario**: Perbaikan infrastruktur, perlu bukti foto sebelum-sesudah

```bash
POST /api/admin/complaints/15/resolve

{
  "resolution_response": "Jalan sudah diperbaiki dengan pengaspalan total. Hasil perbaikan terlampir.",
  "resolution_photos[]": [foto1.jpg, foto2.jpg, foto3.jpg]
}
```

**Result**:
- Status: `resolved`
- Admin response: Updated
- Response record: Created
- Photos: 3 uploaded to `/storage/complaints/resolutions/`
- Notification: Sent to user with photo links

---

### Use Case 3: Multiple Resolutions Same Day

**Scenario**: Admin menyelesaikan beberapa complaint sekaligus

```javascript
const complaints = [15, 20, 25];

for (const id of complaints) {
  const formData = new FormData();
  formData.append('resolution_response', `Complaint ${id} sudah diselesaikan.`);
  
  await fetch(`/api/admin/complaints/${id}/resolve`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}` },
    body: formData
  });
}
```

---

## Validation Rules

### resolution_response
- **Type**: String
- **Required**: ✅ Yes
- **Min**: No minimum
- **Max**: No maximum (but reasonable text)
- **Example**: "Jalan sudah diperbaiki..."

### resolution_photos
- **Type**: Array of files
- **Required**: ❌ No (optional)
- **Max Count**: 3 files
- **Each File**:
  - Format: JPEG, PNG, JPG
  - Max Size: 2 MB (2048 KB)
  - Type: Image only

---

## File Storage Structure

```
storage/
└── app/
    └── public/
        └── complaints/
            ├── photos/              # Original complaint photos
            │   ├── abc123.jpg
            │   └── def456.jpg
            ├── attachments/         # Additional complaint attachments
            │   ├── doc1.pdf
            │   └── doc2.pdf
            └── resolutions/         # Resolution documentation photos
                ├── xyz789.jpg       # ← Stored here
                ├── uvw321.jpg
                └── rst654.jpg
```

**Public URL**: `http://localhost:8000/storage/complaints/resolutions/xyz789.jpg`

---

## Differences: resolve vs updateStatus

| Feature | `/resolve` | `/status` |
|---------|-----------|-----------|
| Purpose | Specifically mark as resolved | Update to any status |
| Status Change | Always to `resolved` | To any status (pending, in_progress, resolved, rejected) |
| Response | Required | Optional (notes) |
| Photos | Support resolution photos (max 3) | Support resolution photos (unlimited) |
| Response Record | Always created | Only if notes provided |
| Use Case | Final completion with docs | General status updates |

### When to Use Each

**Use `/resolve`** when:
- ✅ Complaint is completely finished
- ✅ Need to upload resolution photos
- ✅ Want to create response automatically
- ✅ Final step in complaint lifecycle

**Use `/status`** when:
- ✅ Need to change to any status
- ✅ Intermediate status updates (pending → in_progress)
- ✅ Rejecting complaints
- ✅ Simple status change without photos

---

## Frontend Integration Example

### React Component

```jsx
import { useState } from 'react';

function ResolveComplaintModal({ complaintId, onSuccess }) {
  const [response, setResponse] = useState('');
  const [photos, setPhotos] = useState([]);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData();
    formData.append('resolution_response', response);
    
    photos.forEach(photo => {
      formData.append('resolution_photos[]', photo);
    });

    try {
      const res = await fetch(`/api/admin/complaints/${complaintId}/resolve`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();

      if (data.success) {
        alert('Complaint resolved successfully!');
        onSuccess(data.data);
      }
    } catch (error) {
      console.error('Failed to resolve:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <div>
        <label>Respon Penyelesaian *</label>
        <textarea
          value={response}
          onChange={(e) => setResponse(e.target.value)}
          placeholder="Jelaskan bagaimana keluhan ini diselesaikan..."
          required
        />
      </div>

      <div>
        <label>Foto Dokumentasi Penyelesaian (Opsional)</label>
        <input
          type="file"
          accept="image/jpeg,image/png,image/jpg"
          multiple
          max="3"
          onChange={(e) => setPhotos(Array.from(e.target.files).slice(0, 3))}
        />
        <small>PNG, JPG, JPEG hingga 2MB (Maksimal 3 foto)</small>
      </div>

      <button type="submit" disabled={loading}>
        {loading ? 'Mengirim...' : 'Selesaikan Keluhan'}
      </button>
    </form>
  );
}
```

---

## Testing Checklist

### ✅ Functional Tests

- [ ] Resolve complaint with response only (no photos)
- [ ] Resolve complaint with 1 photo
- [ ] Resolve complaint with 3 photos (max)
- [ ] Reject 4+ photos (validation error)
- [ ] Reject file > 2MB (validation error)
- [ ] Reject non-image files (validation error)
- [ ] Resolve already resolved complaint (should work, updates response)
- [ ] Check notification sent to user
- [ ] Check response record created
- [ ] Check photos saved to correct folder
- [ ] Check attachment_type = 'resolution'
- [ ] Check meta info (previous_status, current_status)

### ✅ Error Handling Tests

- [ ] Invalid complaint ID → 404
- [ ] Missing resolution_response → 422
- [ ] Invalid file type → 422
- [ ] File too large → 422
- [ ] Unauthorized user → 401
- [ ] Non-admin user → 403

---

## Performance Considerations

### File Upload Limits

**Max Upload Size**: 2 MB per file  
**Max Files**: 3 files  
**Total Max**: 6 MB per request

**PHP Configuration**:
```ini
upload_max_filesize = 10M
post_max_size = 20M
max_file_uploads = 20
```

### Storage Space

**Estimated Storage per Resolved Complaint**:
- 3 photos × 1.5 MB average = **4.5 MB**
- 100 resolved complaints = **450 MB**
- 1000 resolved complaints = **4.5 GB**

**Recommendation**: Setup storage cleanup for old complaints

---

## Summary

### ✅ Endpoint Created
- **POST** `/api/admin/complaints/{id}/resolve`

### ✅ Features
- Mark complaint as resolved
- Add resolution response text
- Upload up to 3 resolution photos
- Automatic notification to user
- Status tracking (previous → current)

### ✅ Integration Points
- Attachment system (type: 'resolution')
- Response system (auto-create response)
- Event system (ComplaintStatusChanged)
- Storage system (complaints/resolutions/)

---

**Status**: ✅ Production Ready  
**Last Updated**: November 4, 2025  
**API Version**: 2.2.0
