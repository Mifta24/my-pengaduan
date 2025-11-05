# Panduan Menggunakan Postman Collection

## 📦 File yang Tersedia

1. **MyPengaduan_API.postman_collection.json** - Collection dengan 71 endpoints
2. **MyPengaduan_Local.postman_environment.json** - Environment untuk development lokal
3. **MyPengaduan_Production.postman_environment.json** - Environment untuk production

---

## 🚀 Cara Import ke Postman

### Step 1: Import Collection

1. Buka **Postman**
2. Klik **Import** (tombol di kiri atas)
3. Pilih tab **File**
4. Drag & drop atau browse file: `MyPengaduan_API.postman_collection.json`
5. Klik **Import**

✅ Collection "MyPengaduan API" akan muncul di sidebar kiri

---

### Step 2: Import Environment

1. Klik icon **Environments** (⚙️ di kiri atas atau sidebar)
2. Klik **Import**
3. Pilih file:
   - `MyPengaduan_Local.postman_environment.json` (untuk testing lokal)
   - `MyPengaduan_Production.postman_environment.json` (untuk production)
4. Klik **Import**

✅ Environment "MyPengaduan - Local" dan "MyPengaduan - Production" akan tersedia

---

### Step 3: Pilih Environment

1. Di kanan atas Postman, pilih dropdown environment
2. Pilih **"MyPengaduan - Local"** untuk testing lokal

---

## 🔐 Cara Testing

### 1. Login untuk Mendapatkan Token

1. Buka folder **"Authentication"** di collection
2. Pilih request **"Login"**
3. Di tab **Body**, ubah email & password sesuai user Anda:
   ```json
   {
       "email": "admin@example.com",
       "password": "password"
   }
   ```
4. Klik **Send**
5. ✅ **Token otomatis tersimpan** ke environment variable `token`

**Atau Register Dulu:**
- Pilih request **"Register"**
- Isi data user baru
- Token juga otomatis tersimpan

---

### 2. Test Endpoint User

#### Dashboard User
1. Pilih **"User - Dashboard"** > **"Get User Dashboard"**
2. Klik **Send**
3. Lihat response dengan statistik user

#### Buat Pengaduan
1. Pilih **"User - Complaints"** > **"Create Complaint"**
2. Di tab **Body** (form-data), ubah data sesuai kebutuhan:
   - `title`: "Jalan Rusak Parah"
   - `description`: "Jalan berlubang..."
   - `category_id`: "1"
   - `location`: "Jl. Merdeka No. 123"
   - `attachments[]`: Upload gambar (optional)
3. Klik **Send**
4. ✅ `complaint_id` otomatis tersimpan ke environment

#### Track Pengaduan
1. Pilih **"User - Complaints"** > **"Track Complaint"**
2. Klik **Send**
3. Lihat timeline lengkap pengaduan

---

### 3. Test Endpoint Admin

#### Login sebagai Admin Dulu
1. Pilih **"Authentication"** > **"Login"**
2. Ubah email menjadi admin:
   ```json
   {
       "email": "admin@example.com",
       "password": "password"
   }
   ```
3. Klik **Send**

#### Dashboard Admin
1. Pilih **"Admin - Dashboard"** > **"Get Admin Dashboard"**
2. Klik **Send**
3. Lihat statistik lengkap sistem

#### Update Status Pengaduan
1. Pilih **"Admin - Complaints"** > **"Update Status"**
2. Pastikan `{{complaint_id}}` sudah terisi (dari create complaint tadi)
3. Di tab **Body**, ubah status:
   ```json
   {
       "status": "in_progress",
       "admin_note": "Sedang kami proses"
   }
   ```
4. Klik **Send**

#### Kelola Kategori
1. Buka folder **"Admin - Categories"**
2. Test **"Create Category"**:
   ```json
   {
       "name": "Infrastruktur",
       "description": "Kategori infrastruktur",
       "is_active": true
   }
   ```
3. Test **"Get All Categories"**
4. Test **"Update Category"**, **"Toggle Status"**, dll

---

## 🎯 Struktur Collection

### Authentication (7 endpoints)
- ✅ Register (auto-save token)
- ✅ Login (auto-save token)
- ✅ Get Profile
- ✅ Update Profile
- ✅ Change Password
- ✅ Logout
- ✅ Logout All Devices

### User - Dashboard (1 endpoint)
- ✅ Get User Dashboard (stats + recent complaints + announcements)

### User - Complaints (7 endpoints)
- ✅ Get My Complaints
- ✅ Create Complaint (auto-save complaint_id)
- ✅ Get Complaint Detail
- ✅ Track Complaint (with timeline)
- ✅ Update Complaint
- ✅ Delete Complaint
- ✅ Get My Statistics

### User - Categories (1 endpoint)
- ✅ Get Active Categories

### User - Announcements (5 endpoints)
- ✅ Get Announcements
- ✅ Get Urgent Announcements
- ✅ Get Latest Announcements
- ✅ Get Announcement Detail
- ✅ Add Comment

### Admin - Dashboard (2 endpoints)
- ✅ Get Admin Dashboard
- ✅ Get Quick Stats (Mobile)

### Admin - Complaints (7 endpoints)
- ✅ Get All Complaints (with filters)
- ✅ Get Complaint Detail
- ✅ Update Status
- ✅ Add Response
- ✅ Delete Attachment
- ✅ Get Statistics
- ✅ Bulk Update

### Admin - Categories (8 endpoints)
- ✅ Get All Categories
- ✅ Get Active Categories
- ✅ Get Category Detail
- ✅ Create Category (auto-save category_id)
- ✅ Update Category
- ✅ Delete Category
- ✅ Toggle Status
- ✅ Bulk Action

### Admin - Users (9 endpoints)
- ✅ Get All Users
- ✅ Get User Detail
- ✅ Create User
- ✅ Update User
- ✅ Delete User
- ✅ Verify Email
- ✅ Verify User Identity
- ✅ Change Role
- ✅ Reset Password

### Admin - Announcements (9 endpoints)
- ✅ Get All Announcements
- ✅ Get Announcement Detail
- ✅ Create Announcement (auto-save announcement_id)
- ✅ Update Announcement
- ✅ Delete Announcement
- ✅ Toggle Status
- ✅ Toggle Sticky
- ✅ Publish
- ✅ Unpublish

---

## 📝 Environment Variables

Variables yang otomatis tersimpan:

| Variable | Deskripsi | Auto-saved dari |
|----------|-----------|-----------------|
| `base_url` | URL aplikasi | Manual set |
| `token` | Auth token | Login/Register |
| `user_id` | User ID | Login/Register |
| `complaint_id` | Complaint ID | Create Complaint |
| `category_id` | Category ID | Create Category |
| `announcement_id` | Announcement ID | Create Announcement |
| `attachment_id` | Attachment ID | Manual set jika perlu |

---

## 🔍 Filter & Query Parameters

Banyak endpoint mendukung filter via query parameters:

### Get All Complaints (Admin)
```
?per_page=20
&status=pending
&category=1
&search=jalan
```

### Get All Users (Admin)
```
?per_page=20
&role=user
&is_verified=1
&search=john
```

### Get Announcements
```
?per_page=10
&priority=urgent
&search=pemeliharaan
```

Enable/disable query params dengan checkbox di Postman!

---

## 🎨 Tips & Tricks

### 1. Auto-save Token
Script sudah ditambahkan di request Login & Register:
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```

### 2. Auto-save IDs
Script sudah ditambahkan di request Create:
- Create Complaint → save `complaint_id`
- Create Category → save `category_id`
- Create Announcement → save `announcement_id`

### 3. Bearer Token Authentication
Collection sudah configured dengan:
```
Authorization: Bearer {{token}}
```
Token otomatis terkirim di setiap request!

### 4. Testing Flow
**Recommended testing flow:**
1. Login → token tersimpan
2. Create Complaint → complaint_id tersimpan
3. Track Complaint → pakai complaint_id
4. Update Status (Admin) → pakai complaint_id
5. Get Dashboard → lihat statistik

### 5. Duplicate Request
Untuk test dengan data berbeda:
1. Right-click request → **Duplicate**
2. Rename, misal: "Create Complaint - Test 2"
3. Ubah data di Body

---

## 📤 Upload File

Untuk upload file (attachments & images):

1. Pilih request yang support file upload
2. Di tab **Body**, pilih **form-data**
3. Hover field `attachments[]` atau `image`
4. Change type dari **Text** ke **File**
5. Klik **Select Files** dan pilih file
6. Klik **Send**

**Requests dengan file upload:**
- Create Complaint → `attachments[]` (multiple)
- Create Announcement → `image` (single)
- Update Announcement → `image` (single)

---

## 🌐 Switch ke Production

1. Klik dropdown environment (kanan atas)
2. Pilih **"MyPengaduan - Production"**
3. Update `base_url` di environment:
   - Klik icon ⚙️ **Environments**
   - Edit "MyPengaduan - Production"
   - Set `base_url` = `https://your-domain.com`
4. Login ulang untuk dapat token production

---

## ✅ Validasi Response

Setiap response memiliki format konsisten:

### Success Response
```json
{
    "success": true,
    "message": "Operasi berhasil",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Terjadi kesalahan",
    "errors": { ... }
}
```

### Pagination Response
```json
{
    "success": true,
    "data": {
        "items": [...],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 10,
            "total": 50
        }
    }
}
```

---

## 🐛 Troubleshooting

### Token Invalid/Expired
**Error**: `401 Unauthenticated`
**Solution**: Login ulang untuk dapat token baru

### Complaint ID Not Found
**Error**: `404 Not Found`
**Solution**: 
1. Cek variable `{{complaint_id}}` di environment
2. Atau create complaint baru dulu

### Admin Endpoint Forbidden
**Error**: `403 Forbidden`
**Solution**: Login dengan akun admin, bukan user biasa

### File Upload Failed
**Error**: `422 Validation Error`
**Solution**:
1. Pastikan type sudah **File**, bukan Text
2. Pastikan file size tidak terlalu besar
3. Pastikan format file valid (jpg, png, pdf)

### Base URL Wrong
**Error**: Connection timeout
**Solution**: Cek `{{base_url}}` di environment:
- Local: `http://localhost`
- Production: `https://your-domain.com`

---

## 📊 Testing Checklist

### Authentication
- [ ] Register user baru
- [ ] Login user
- [ ] Login admin
- [ ] Get profile
- [ ] Update profile
- [ ] Change password
- [ ] Logout

### User Flow
- [ ] Dashboard user
- [ ] Create complaint dengan attachment
- [ ] Track complaint
- [ ] Update complaint
- [ ] Get announcements
- [ ] Add comment

### Admin Flow
- [ ] Dashboard admin
- [ ] Get all complaints
- [ ] Update complaint status
- [ ] Add response
- [ ] Create category
- [ ] Create user
- [ ] Create announcement
- [ ] Bulk operations

---

## 🎉 Selesai!

Postman collection siap digunakan dengan:
- ✅ 71 endpoints lengkap
- ✅ Auto-save token & IDs
- ✅ Bearer authentication
- ✅ Environment variables
- ✅ Request examples
- ✅ Documentation

**Happy Testing! 🚀**

---

## 📞 Sharing Collection

Untuk share ke team:
1. Export collection: Right-click collection → **Export**
2. Pilih **Collection v2.1**
3. Share file `.json` ke team
4. Team tinggal import

Atau gunakan Postman workspace untuk kolaborasi real-time!

---

**Created**: October 27, 2025
**Version**: 1.0
**Total Endpoints**: 71
