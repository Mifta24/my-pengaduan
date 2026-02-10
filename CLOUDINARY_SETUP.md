# Cloudinary Setup & Migration Guide

## 🎯 Kenapa Pakai Cloudinary?

✅ **No storage issues** - Tidak perlu worry tentang storage di Railway  
✅ **CDN built-in** - Foto load lebih cepat dari mana saja  
✅ **Auto optimization** - Automatic image compression & format conversion  
✅ **Reliable** - 99.9% uptime  
✅ **Easy migration** - Mudah pindah dari local storage  

---

## 📋 Step 1: Setup Cloudinary Account

### 1.1 Daftar di Cloudinary (FREE)
1. Buka: https://cloudinary.com/users/register_free
2. Sign up dengan email/Google
3. Verify email
4. Login ke dashboard

### 1.2 Get Credentials
1. Di dashboard Cloudinary, klik **Dashboard** (home icon)
2. Copy credentials:
   - **Cloud name**: `your-cloud-name`
   - **API Key**: `123456789012345`
   - **API Secret**: `abcdefghijklmnopqrstuvwxyz123456`

---

## 🔧 Step 2: Configure Laravel

### 2.1 Update `.env` Local

```env
FILESYSTEM_DISK=cloudinary

CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=123456789012345
CLOUDINARY_API_SECRET=abcdefghijklmnopqrstuvwxyz123456
CLOUDINARY_SECURE_URL=true
```

### 2.2 Update Railway Environment Variables

Di **Railway Dashboard > Variables**, tambahkan:

```
FILESYSTEM_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=123456789012345
CLOUDINARY_API_SECRET=abcdefghijklmnopqrstuvwxyz123456
CLOUDINARY_SECURE_URL=true
```

### 2.3 Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 💻 Step 3: Update Controllers

### 3.1 Add Trait to ComplaintController

File: `app/Http/Controllers/Api/ComplaintController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Traits\HandlesCloudinaryUpload; // ADD THIS

class ComplaintController extends Controller
{
    use HandlesCloudinaryUpload; // ADD THIS
    
    // ... rest of code
}
```

### 3.2 Update Upload Method

Replace the photo upload section in `store()` method:

```php
// OLD CODE - DELETE THIS:
if ($request->hasFile('photo')) {
    $photo = $request->file('photo');
    $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
    $photoPath = 'complaints/photos/' . $fileName;
    // ... compression code ...
    Storage::disk('public')->put($photoPath, (string) $encodedImage);
    $complaint->update(['photo' => $photoPath]);
}

// NEW CODE - USE THIS:
if ($request->hasFile('photo')) {
    if ($this->isCloudinaryEnabled()) {
        // Upload to Cloudinary
        $upload = $this->uploadToCloudinary(
            $request->file('photo'),
            'complaints/photos',
            1920,
            85
        );
        
        $complaint->update([
            'photo' => $upload['url'] // Store full Cloudinary URL
        ]);
        
        \Log::info('Photo uploaded to Cloudinary', $upload);
    } else {
        // Fallback to local storage
        $photo = $request->file('photo');
        $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $photoPath = 'complaints/photos/' . $fileName;
        
        $image = Image::read($photo->getRealPath());
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }
        
        $extension = strtolower($photo->getClientOriginalExtension());
        if (in_array($extension, ['jpg', 'jpeg'])) {
            $encodedImage = $image->toJpeg(quality: 85);
        } elseif ($extension === 'webp') {
            $encodedImage = $image->toWebp(quality: 85);
        } else {
            $encodedImage = $image->toPng();
        }
        
        Storage::disk('public')->put($photoPath, (string) $encodedImage);
        $complaint->update(['photo' => $photoPath]);
    }
}
```

### 3.3 Update Delete Method

In `destroy()` method, add Cloudinary delete:

```php
public function destroy(Request $request, Complaint $complaint)
{
    // ... existing authorization checks ...
    
    // Delete photo from Cloudinary if exists
    if ($complaint->photo) {
        if ($this->isCloudinaryEnabled() && filter_var($complaint->photo, FILTER_VALIDATE_URL)) {
            // Extract public_id from Cloudinary URL
            // Example: https://res.cloudinary.com/cloud-name/image/upload/v123456/complaints/photos/abc123
            preg_match('/\/v\d+\/(.+)$/', $complaint->photo, $matches);
            if (isset($matches[1])) {
                $publicId = pathinfo($matches[1], PATHINFO_DIRNAME) . '/' . pathinfo($matches[1], PATHINFO_FILENAME);
                $this->deleteFromCloudinary($publicId);
            }
        } else {
            // Delete from local storage
            Storage::disk('public')->delete($complaint->photo);
        }
    }
    
    // Delete attachments
    foreach ($complaint->attachments as $attachment) {
        if ($this->isCloudinaryEnabled() && filter_var($attachment->file_path, FILTER_VALIDATE_URL)) {
            preg_match('/\/v\d+\/(.+)$/', $attachment->file_path, $matches);
            if (isset($matches[1])) {
                $publicId = pathinfo($matches[1], PATHINFO_DIRNAME) . '/' . pathinfo($matches[1], PATHINFO_FILENAME);
               $this->deleteFromCloudinary($publicId);
            }
        } else {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();
    }
    
    $complaint->delete();
    
    return $this->success(null, 'Complaint deleted successfully');
}
```

---

## 🧪 Step 4: Testing

### 4.1 Test Upload Local

```bash
php artisan serve
```

Upload foto via Postman/web, cek di Cloudinary Dashboard:
- **Media Library** > folder `complaints/photos`
- Foto harus muncul di sana

### 4.2 Test di Railway

1. Push code ke Railway:
   ```bash
   git add .
   git commit -m "feat: Add Cloudinary integration for photo storage"
   git push origin main
   ```

2. Set environment variables di Railway (Step 2.2)

3. Tunggu deployment selesai

4. Upload foto baru dari mobile app

5. Cek response API - `photo` field harus berisi Cloudinary URL:
   ```json
   {
     "photo": "https://res.cloudinary.com/your-cloud/image/upload/v1234567/complaints/photos/abc123.jpg",
     "photo_url": "https://res.cloudinary.com/your-cloud/image/upload/v1234567/complaints/photos/abc123.jpg"
   }
   ```

---

## 📝 Step 5: Update Model (Optional)

File: `app/Models/Complaint.php`

Update `getPhotoUrlAttribute` to handle both Cloudinary URLs and local paths:

```php
public function getPhotoUrlAttribute()
{
    if (!$this->photo) {
        return null;
    }

    // If already a full URL (Cloudinary), return as is
    if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
        return $this->photo;
    }

    // Local storage path
    return url('storage/' . $this->photo);
}
```

---

## 🔄 Step 6: Migrate Old Photos (Optional)

Jika ingin migrate foto lama dari local/Railway ke Cloudinary:

```bash
# Jalankan di Railway Shell atau local
php artisan tinker
```

```php
use App\Models\Complaint;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;

// Get complaints with local storage photos
$complaints = Complaint::whereNotNull('photo')
    ->where('photo', 'not like', 'https://%')
    ->get();

foreach ($complaints as $complaint) {
    try {
        $localPath = 'complaints/photos/' . basename($complaint->photo);
        
        if (Storage::disk('public')->exists($localPath)) {
            $fullPath = storage_path('app/public/' . $localPath);
            
            // Upload to Cloudinary
            $uploaded = Cloudinary::upload($fullPath, [
                'folder' => 'complaints/photos',
                'resource_type' => 'image'
            ]);
            
            // Update database with Cloudinary URL
            $complaint->update([
                'photo' => $uploaded->getSecurePath()
            ]);
            
            echo "✅ Migrated complaint #{$complaint->id}\n";
        }
    } catch (\Exception $e) {
        echo "❌ Failed complaint #{$complaint->id}: {$e->getMessage()}\n";
    }
}

echo "\nMigration complete!\n";
```

---

## ✅ Checklist

- [ ] Cloudinary account created
- [ ] Credentials copied
- [ ] `.env` local updated with Cloudinary credentials
- [ ] Railway environment variables set
- [ ] Trait `HandlesCloudinaryUpload` created
- [ ] Controller updated with trait
- [ ] Upload method updated
- [ ] Delete method updated
- [ ] Tested upload local
- [ ] Code pushed to Railway
- [ ] Test upload di production
- [ ] (Optional) Old photos migrated

---

## 🐛 Troubleshooting

### Error: "Class 'Cloudinary' not found"

```bash
composer dump-autoload
php artisan config:clear
```

### Error: "Invalid cloud_name"

Check `.env` credentials, make sure:
- No spaces
- No quotes
- Correct cloud name from dashboard

### Photos not showing

1. Check `FILESYSTEM_DISK=cloudinary` in Railway
2. Clear config: `php artisan config:clear`
3. Check Cloudinary dashboard - photo uploaded?
4. Check API response - using Cloudinary URL?

### Upload too slow

Cloudinary free tier has limits:
- 25 monthly credits
- ~25k transformations/month
- 25GB storage

For production, upgrade to paid plan.

---

## 📊 Benefits Summary

| Feature | Local Storage | Cloudinary |
|---------|--------------|------------|
| **Storage** | Limited (Railway) | 25GB free |
| **CDN** | ❌ No | ✅ Yes |
| **Auto optimization** | ❌ Manual | ✅ Automatic |
| **Reliability** | ⚠️ Depend on server | ✅ 99.9% uptime |
| **Speed** | 🐌 Slow | 🚀 Fast (global CDN) |
| **Cost** | Free but limited | Free tier available |

---

## 🔗 Resources

- Cloudinary Dashboard: https://cloudinary.com/console
- Laravel Package Docs: https://github.com/cloudinary-labs/cloudinary-laravel
- Cloudinary Docs: https://cloudinary.com/documentation

---

**Need Help?** Check Railway logs or Cloudinary dashboard for errors.
