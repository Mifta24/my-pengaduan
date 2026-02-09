# Railway Photo Upload Fix

## Masalah
Foto yang di-upload tidak tampil di aplikasi yang sudah di-deploy di Railway.

## Penyebab
1. **FILESYSTEM_DISK salah** - menggunakan `local` instead of `public`
2. **Storage link tidak terbuat** - symbolic link dari `public/storage` ke `storage/app/public`
3. **Path foto tidak konsisten** - menggunakan `storage_path()` dan `mkdir()` manual

## Solusi yang Diterapkan

### 1. Update Environment Variable di Railway
```bash
FILESYSTEM_DISK=public
```

**Cara set di Railway Dashboard:**
1. Buka project Railway > Variables
2. Tambah/Edit variable: `FILESYSTEM_DISK` = `public`
3. Redeploy aplikasi

### 2. Update Procfile
Tambahkan `php artisan storage:link` di awal web command:
```
web: php artisan storage:link && php artisan migrate --force && ...
```

### 3. Fix Controller Code
Semua controller sudah diupdate untuk menggunakan `Storage::disk('public')` facade:

**File yang diupdate:**
- ✅ `app/Http/Controllers/Api/ComplaintController.php`
- ✅ `app/Http/Controllers/Api/Admin/ComplaintController.php`

**Perubahan:**
```php
// ❌ SEBELUM (SALAH)
$storagePath = storage_path('app/public/complaints/photos');
if (!file_exists($storagePath)) {
    mkdir($storagePath, 0755, true);
}
$image->toJpeg(quality: 85)->save($fullPath);

// ✅ SESUDAH (BENAR)
$photoPath = 'complaints/photos/' . $fileName;
$encodedImage = $image->toJpeg(quality: 85);
Storage::disk('public')->put($photoPath, (string) $encodedImage);
```

## Cara Verifikasi

### 1. Cek Storage Link di Railway
```bash
# Via Railway CLI atau shell
ls -la public/
# Harus ada: storage -> ../storage/app/public
```

### 2. Cek Environment
```bash
php artisan tinker
Config::get('filesystems.default'); // harus return "public"
```

### 3. Test Upload Foto
1. Upload foto via API
2. Cek path di database: `complaints.photo` column
3. Path harus: `complaints/photos/xxxxx.jpg`
4. URL harus: `https://your-app.railway.app/storage/complaints/photos/xxxxx.jpg`

### 4. Cek Log
```bash
# Di Railway logs, cari:
"Photo uploaded" => [
    "path" => "complaints/photos/...",
    "disk" => "public",
    "exists" => true,
    "url" => "https://..."
]
```

## Deployment Steps

### Deploy ke Railway:
```bash
# 1. Commit semua perubahan
git add .
git commit -m "Fix: Photo upload with Storage facade for Railway deployment"

# 2. Push ke Railway
git push origin main

# 3. Set environment variable di Railway Dashboard
FILESYSTEM_DISK=public

# 4. Redeploy (otomatis atau manual)
```

## Troubleshooting

### Foto masih tidak tampil?

**1. Cek Environment Variable**
```bash
# Via Railway shell
echo $FILESYSTEM_DISK
# Harus output: public
```

**2. Cek Storage Link**
```bash
ls -la public/storage
# Harus ada symbolic link
```

**3. Cek Path di Database**
```bash
php artisan tinker
DB::table('complaints')->whereNotNull('photo')->latest()->first(['photo']);
# Path harus: complaints/photos/xxxx.jpg
# BUKAN: /var/app/storage/app/public/...
```

**4. Cek File Exists**
```bash
php artisan tinker
Storage::disk('public')->exists('complaints/photos/xxxx.jpg');
# Harus return: true
```

**5. Cek URL Generation**
```bash
php artisan tinker
Storage::disk('public')->url('complaints/photos/xxxx.jpg');
# Harus return: https://your-app.railway.app/storage/complaints/photos/xxxx.jpg
```

### Error "No such file or directory"

Solusi:
```bash
# Manual run storage link
php artisan storage:link --force

# Verify
ls -la public/storage
```

### Error "File not found" tapi file ada di storage

Kemungkinan:
1. **APP_URL tidak sesuai** - set `APP_URL=https://your-app.railway.app`
2. **Nginx/Apache config** - pastikan `public/storage` bisa diakses
3. **Permission issue** - jalankan `chmod -R 755 storage public`

## Production Checklist

- [x] `FILESYSTEM_DISK=public` di Railway
- [x] Procfile include `php artisan storage:link`
- [x] Controllers menggunakan `Storage::disk('public')`
- [x] `APP_URL` sesuai dengan domain Railway
- [x] Test upload foto baru
- [x] Verify foto bisa diakses via URL

## Notes

### Kenapa Pakai `Storage::disk('public')` Facade?

1. ✅ **Cross-platform** - work di Windows, Linux, Docker
2. ✅ **Automatic directory creation** - tidak perlu `mkdir()` manual
3. ✅ **Better error handling** - Laravel handle permission issues
4. ✅ **Consistent paths** - always relative to storage disk
5. ✅ **URL generation** - automatic dengan `Storage::url()`

### Struktur Folder di Railway

```
/app
├── public/
│   ├── storage -> ../storage/app/public (symlink)
│   ├── index.php
│   └── ...
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── complaints/
│   │   │   │   ├── photos/
│   │   │   │   └── attachments/
│   │   │   └── responses/
│   │   │       ├── photos/
│   │   │       └── attachments/
│   │   └── ...
│   ├── framework/
│   └── logs/
└── ...
```

## Related Files

- `config/filesystems.php` - Storage disk configuration
- `Procfile` - Railway startup commands
- `.env` - Environment variables
- `app/Http/Controllers/Api/ComplaintController.php`
- `app/Http/Controllers/Api/Admin/ComplaintController.php`
- `app/Models/Complaint.php` - `photo_url` accessor
- `app/Models/Attachment.php` - `file_url` accessor

## References

- Laravel Storage: https://laravel.com/docs/11.x/filesystem
- Railway Docs: https://docs.railway.app/
- Intervention Image: https://image.intervention.io/
