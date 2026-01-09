# MyPengaduan API Documentation

## 📚 Akses Dokumentasi

Dokumentasi API MyPengaduan telah di-generate menggunakan **Scribe** dan dapat diakses melalui:

### 🌐 Web Interface (Recommended)
```
http://localhost/docs
```

### 📥 Export Options

1. **Postman Collection**
   ```
   http://localhost/docs.postman
   ```
   - Download dan import ke Postman
   - Semua endpoint sudah ter-configure
   - Include authentication setup

2. **OpenAPI Specification**
   ```
   http://localhost/docs.openapi
   ```
   - Standar OpenAPI 3.0.3
   - Compatible dengan Swagger UI, Insomnia, dll

---

## 🔑 Authentication

### Mendapatkan Token

**Endpoint:** `POST /api/login`

**Body:**
```json
{
  "email": "warga1@test.com",
  "password": "password"
}
```

**Response:**
```json
{
  "status": true,
  "message": "Login berhasil",
  "data": {
    "user": {...},
    "token": "1|abc123..."
  }
}
```

### Menggunakan Token

Sertakan token di setiap request yang memerlukan autentikasi:

```
Authorization: Bearer {token}
```

---

## 📖 Struktur Endpoint

### Public Endpoints (No Auth Required)
- `POST /api/register` - Register user baru
- `POST /api/login` - Login user
- `GET /api/announcements` - List pengumuman
- `GET /api/announcements/{id}` - Detail pengumuman

### User Endpoints (Auth Required)
- **Profile**
  - `GET /api/auth/profile` - Get profil user
  - `PUT /api/auth/profile` - Update profil
  - `PUT /api/auth/change-password` - Ganti password
  - `POST /api/auth/logout` - Logout

- **Complaints**
  - `GET /api/complaints` - List pengaduan user
  - `POST /api/complaints` - Buat pengaduan baru
  - `GET /api/complaints/{id}` - Detail pengaduan
  - `PUT /api/complaints/{id}` - Update pengaduan
  - `DELETE /api/complaints/{id}` - Hapus pengaduan
  - `GET /api/complaints/{id}/track` - Track status pengaduan
  - `GET /api/complaints/statistics` - Statistik pengaduan user
  - `GET /api/complaints/categories` - List kategori

- **Device Tokens (FCM)**
  - `POST /api/device-tokens` - Register device token
  - `GET /api/device-tokens` - List device tokens
  - `DELETE /api/device-tokens/{id}` - Hapus device token

- **Notifications**
  - `GET /api/notifications` - List notifikasi
  - `POST /api/notifications/{id}/read` - Mark as read
  - `POST /api/notifications/read-all` - Mark all as read
  - `GET /api/notification-settings` - Get settings
  - `PUT /api/notification-settings` - Update settings

### Admin Endpoints (Auth + Admin Role)
- **Dashboard**
  - `GET /api/admin/dashboard` - Dashboard data
  - `GET /api/admin/dashboard/quick-stats` - Quick statistics

- **Complaint Management**
  - `GET /api/admin/complaints` - List semua pengaduan
  - `POST /api/admin/complaints` - Buat pengaduan (behalf user)
  - `GET /api/admin/complaints/{id}` - Detail pengaduan
  - `PUT /api/admin/complaints/{id}` - Update pengaduan
  - `DELETE /api/admin/complaints/{id}` - Hapus pengaduan
  - `PATCH /api/admin/complaints/{id}/status` - Update status
  - `POST /api/admin/complaints/{id}/resolve` - Mark as resolved
  - `POST /api/admin/complaints/{id}/response` - Add response
  - `POST /api/admin/complaints/bulk-update` - Bulk update
  - `DELETE /api/admin/complaints/attachments/{id}` - Delete attachment
  - `GET /api/admin/complaints/statistics` - Statistics

- **Category Management**
  - `GET /api/admin/categories` - List kategori
  - `POST /api/admin/categories` - Buat kategori
  - `PUT /api/admin/categories/{id}` - Update kategori
  - `DELETE /api/admin/categories/{id}` - Hapus kategori
  - `PATCH /api/admin/categories/{id}/toggle-status` - Toggle active
  - `POST /api/admin/categories/bulk-action` - Bulk action

- **User Management**
  - `GET /api/admin/users` - List users
  - `POST /api/admin/users` - Buat user baru
  - `PUT /api/admin/users/{id}` - Update user
  - `DELETE /api/admin/users/{id}` - Hapus user
  - `PATCH /api/admin/users/{id}/verify-email` - Verify email
  - `PATCH /api/admin/users/{id}/verify-user` - Verify user (KTP)
  - `PATCH /api/admin/users/{id}/change-role` - Change role
  - `PATCH /api/admin/users/{id}/reset-password` - Reset password

- **Announcement Management**
  - `GET /api/admin/announcements` - List pengumuman
  - `POST /api/admin/announcements` - Buat pengumuman
  - `PUT /api/admin/announcements/{id}` - Update pengumuman
  - `DELETE /api/admin/announcements/{id}` - Hapus pengumuman
  - `PATCH /api/admin/announcements/{id}/toggle-status` - Toggle active
  - `PATCH /api/admin/announcements/{id}/toggle-sticky` - Toggle sticky
  - `POST /api/admin/announcements/{id}/publish` - Publish
  - `POST /api/admin/announcements/{id}/unpublish` - Unpublish

- **Reports**
  - `GET /api/admin/reports/overview` - Report overview
  - `GET /api/admin/reports/complaints` - Complaint reports
  - `GET /api/admin/reports/users` - User reports
  - `POST /api/admin/reports/export` - Export report (Excel)

---

## 🔄 Re-generate Documentation

Jika ada perubahan pada API (endpoint baru, perubahan request/response), generate ulang dokumentasi:

```bash
php artisan scribe:generate
```

### Tips untuk Dokumentasi yang Lebih Baik:

1. **Tambahkan PHPDoc di Controller:**
   ```php
   /**
    * @group Nama Group
    * 
    * Deskripsi group endpoint
    */
   class MyController extends Controller
   {
       /**
        * Judul Endpoint
        * 
        * Deskripsi detail endpoint ini.
        * 
        * @authenticated
        * 
        * @bodyParam field_name type required Deskripsi field. Example: contoh value
        * 
        * @response 200 {
        *   "status": true,
        *   "data": {}
        * }
        */
       public function myMethod(Request $request)
       {
           // ...
       }
   }
   ```

2. **Annotations yang tersedia:**
   - `@group` - Grouping endpoints
   - `@authenticated` / `@unauthenticated` - Auth requirement
   - `@bodyParam` - Request body parameter
   - `@queryParam` - Query string parameter
   - `@urlParam` - URL path parameter
   - `@response` - Example response
   - `@responseFile` - Response dari file

3. **Generate ulang setiap kali:**
   - Tambah endpoint baru
   - Ubah validation rules
   - Tambah/ubah PHPDoc
   - Update response format

---

## 🎨 Customization

### Mengubah Warna/Theme

Edit file: `config/scribe.php`

```php
'theme' => 'default', // atau 'elements'
```

### Mengubah Intro Text

Edit file: `.scribe/intro.md`

### Mengubah Auth Info

Edit file: `.scribe/auth.md`

---

## 📱 Integration dengan Flutter

### Setup Base URL
```dart
class ApiConfig {
  static const String baseUrl = 'http://localhost/api';
  static const String docsUrl = 'http://localhost/docs';
}
```

### Download Postman Collection
1. Buka `http://localhost/docs.postman`
2. Import ke Postman
3. Test semua endpoint
4. Share dengan team

### Use OpenAPI Spec
1. Download dari `http://localhost/docs.openapi`
2. Import ke Insomnia/Swagger
3. Generate client SDK (optional)

---

## 🐛 Troubleshooting

### Dokumentasi tidak muncul
```bash
# Clear cache dan regenerate
php artisan cache:clear
php artisan config:clear
php artisan scribe:generate
```

### Route tidak ter-detect
- Pastikan route ada di `routes/api.php`
- Pastikan prefix route `api/*` di `config/scribe.php`
- Check middleware di route

### Response example tidak muncul
- Tambahkan `@response` annotation di PHPDoc
- Atau buat file response: `.scribe/responses/*.json`

---

## 📞 Support

Untuk pertanyaan atau issue terkait API:
- Check dokumentasi web: `http://localhost/docs`
- Lihat Postman collection untuk contoh request
- Baca file dokumentasi lain di folder `docs/`

---

## ✅ Checklist Production

Sebelum deploy ke production:

- [ ] Generate dokumentasi terbaru
- [ ] Test semua endpoint di Postman
- [ ] Update base URL di config
- [ ] Set proper middleware untuk docs route (jika perlu)
- [ ] Export OpenAPI spec untuk frontend team
- [ ] Update environment variables
- [ ] Verify authentication flow
- [ ] Test file upload limits
- [ ] Check CORS configuration

---

**Generated with:** Scribe v5.6.0  
**Last Updated:** 2025-01-09  
**API Version:** 1.0
