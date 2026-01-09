# Admin Response Endpoint Documentation

## Overview
Dedicated endpoint untuk admin memberikan response/reply ke complaint yang dibuat user. Endpoint ini mendukung text response, photo upload, dan multiple attachments, serta otomatis mengirim notifikasi ke user.

## Endpoint Details

### Add Response to Complaint
**POST** `/api/admin/complaints/{id}/response`

#### Authentication
- **Required:** Yes
- **Type:** Bearer Token (Sanctum)
- **Role:** Admin only

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `message` | string | Yes | Response content/message dari admin |
| `photo` | file | No | Single photo (max 2MB, jpeg/png/jpg) |
| `attachments` | array | No | Multiple files (max 5 files, each max 10MB) |
| `attachments.*` | file | No | Individual attachment file |

#### Validation Rules
```php
'message' => 'required|string',
'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
'attachments' => 'nullable|array|max:5',
'attachments.*' => 'file|max:10240'
```

#### Success Response (201 Created)
```json
{
    "success": true,
    "message": "Response added successfully",
    "data": {
        "response": {
            "id": 1,
            "complaint_id": 15,
            "user_id": 1,
            "content": "Kami telah menerima laporan Anda dan akan segera menindaklanjuti",
            "photo": "responses/photos/xyz.jpg",
            "photo_url": "http://localhost/storage/responses/photos/xyz.jpg",
            "created_at": "2026-01-09T10:30:00.000000Z",
            "updated_at": "2026-01-09T10:30:00.000000Z",
            "user": {
                "id": 1,
                "name": "Admin User",
                "email": "admin@example.com"
            }
        },
        "attachments": [
            {
                "id": 25,
                "file_name": "evidence.pdf",
                "file_url": "http://localhost/storage/responses/attachments/evidence.pdf",
                "file_size_human": "1.5 MB"
            }
        ],
        "attachments_count": 1
    }
}
```

#### Error Responses

**400 Bad Request** - Validation Error
```json
{
    "success": false,
    "message": "Validation error",
    "data": {
        "message": ["The message field is required."]
    }
}
```

**401 Unauthorized** - Not authenticated or not admin
```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

**404 Not Found** - Complaint not found
```json
{
    "success": false,
    "message": "Failed to add response"
}
```

**500 Server Error**
```json
{
    "success": false,
    "message": "Failed to add response"
}
```

## Features

### 1. Response Creation
- Create Response record dengan content message
- Link response ke complaint dan admin user
- Update `admin_response` field di complaint table dengan message terbaru

### 2. Photo Upload
- Support single photo upload per response
- Stored in `storage/app/public/responses/photos/`
- Max size: 2MB
- Formats: jpeg, png, jpg

### 3. Multiple Attachments
- Support up to 5 files per response
- Stored in `storage/app/public/responses/attachments/`
- Max size per file: 10MB
- Attachments polymorphic relationship with Response model

### 4. Automatic Notification
- Send FCM push notification ke user yang membuat complaint
- Save notification to `fcm_notifications` table
- Notification data includes:
  - Type: `complaint_response`
  - Complaint ID
  - Response ID
  - Complaint title

### 5. Error Handling
- Notification failure tidak menggagalkan response creation
- Logged ke Laravel log untuk debugging
- User tetap bisa melihat response meskipun notifikasi gagal

## Usage Examples

### cURL Example
```bash
curl -X POST \
  http://localhost/api/admin/complaints/15/response \
  -H 'Authorization: Bearer your-admin-token' \
  -H 'Content-Type: multipart/form-data' \
  -F 'message=Terima kasih atas laporannya. Tim kami akan segera menindaklanjuti.' \
  -F 'photo=@/path/to/photo.jpg' \
  -F 'attachments[]=@/path/to/document1.pdf' \
  -F 'attachments[]=@/path/to/document2.pdf'
```

### JavaScript (Fetch API)
```javascript
const formData = new FormData();
formData.append('message', 'Terima kasih atas laporannya...');
formData.append('photo', photoFile);
formData.append('attachments[]', file1);
formData.append('attachments[]', file2);

fetch('http://localhost/api/admin/complaints/15/response', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer your-admin-token'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

### Flutter Example
```dart
Future<void> addResponse(int complaintId, String message, {
  File? photo,
  List<File>? attachments
}) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('$baseUrl/api/admin/complaints/$complaintId/response'),
  );
  
  request.headers['Authorization'] = 'Bearer $token';
  request.fields['message'] = message;
  
  if (photo != null) {
    request.files.add(await http.MultipartFile.fromPath('photo', photo.path));
  }
  
  if (attachments != null) {
    for (var file in attachments) {
      request.files.add(
        await http.MultipartFile.fromPath('attachments[]', file.path)
      );
    }
  }
  
  var response = await request.send();
  var responseData = await response.stream.bytesToString();
  print(responseData);
}
```

## User-Side Viewing Responses

### View Complaint with Responses
**GET** `/api/complaints/{id}`

Response includes `responses` array:
```json
{
    "success": true,
    "data": {
        "id": 15,
        "title": "Jalan Rusak",
        "description": "...",
        "responses": [
            {
                "id": 1,
                "content": "Terima kasih atas laporannya",
                "photo": "responses/photos/xyz.jpg",
                "photo_url": "http://localhost/storage/responses/photos/xyz.jpg",
                "created_at": "2026-01-09T10:30:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "Admin User",
                    "email": "admin@example.com"
                },
                "attachments": []
            }
        ],
        "responses_count": 1
    }
}
```

### Track Complaint Timeline
**GET** `/api/complaints/{id}/track`

Includes responses in timeline:
```json
{
    "success": true,
    "data": {
        "complaint": {...},
        "timeline": [
            {
                "type": "response",
                "status": "in_progress",
                "title": "Response from Admin",
                "description": "Terima kasih atas laporannya",
                "photo": "responses/photos/xyz.jpg",
                "created_at": "2026-01-09T10:30:00.000000Z"
            }
        ]
    }
}
```

## Database Structure

### responses table
```sql
id                  bigint unsigned auto_increment primary key
complaint_id        bigint unsigned not null (FK to complaints)
user_id             bigint unsigned not null (FK to users - admin)
content             text not null
photo               varchar(255) nullable
created_at          timestamp
updated_at          timestamp
```

### attachments table (polymorphic)
```sql
id                  bigint unsigned auto_increment primary key
attachable_type     varchar(255) not null (App\Models\Response)
attachable_id       bigint unsigned not null (response.id)
file_name           varchar(255) not null
file_path           varchar(255) not null
file_size           integer not null
mime_type           varchar(255) not null
attachment_type     varchar(255) nullable (response)
created_at          timestamp
updated_at          timestamp
```

## Notes

1. **Authorization**: Endpoint ini HANYA bisa diakses oleh user dengan role `admin`
2. **Notification**: FCM notification otomatis terkirim jika user memiliki `fcm_token`
3. **Database Logging**: Semua notification disimpan ke database untuk history
4. **Storage**: File disimpan di `storage/app/public/` dan accessible via `/storage/` URL
5. **Polymorphic Relation**: Attachments menggunakan polymorphic relationship, bisa attach ke Complaint atau Response
6. **Error Resilience**: Jika FCM gagal, response tetap tersimpan dan user tetap bisa lihat di app
7. **Latest Response**: Field `admin_response` di complaint table selalu berisi response terbaru dari admin

## Related Files

- Controller: `app/Http/Controllers/Api/Admin/ComplaintController.php`
- Response Model: `app/Models/Response.php`
- Attachment Model: `app/Models/Attachment.php`
- Firebase Service: `app/Services/FirebaseService.php`
- Route: `routes/api.php` (line 142)
- User Controller: `app/Http/Controllers/Api/ComplaintController.php` (show & track methods)

---
Last Updated: 2026-01-09
