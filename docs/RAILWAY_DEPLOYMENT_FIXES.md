# 🔧 Railway Deployment Error Fixes

## Error: "Class Knuckles\Scribe\Config\AuthIn not found"

### Masalah:
Saat deploy ke Railway dengan `composer install --no-dev`, package Scribe (dev dependency) dihapus, tapi config file-nya (`config/scribe.php`) masih mencoba load class Scribe yang sudah tidak ada.

### Penyebab:
```bash
# Railway build command
composer install --optimize-autoloader --no-dev && php artisan storage:link
                                        ^^^^^^^^
                                        Removes dev dependencies including Scribe

# Kemudian artisan storage:link trigger package:discover
# Yang load semua config files termasuk scribe.php
# Yang reference class yang sudah di-remove
```

### Solusi:

#### 1. **Update railway.json** ✅
```json
{
  "build": {
    "buildCommand": "composer install --optimize-autoloader --no-dev --no-scripts"
                                                                      ^^^^^^^^^^^
                                                                      Skip post-install scripts
  },
  "deploy": {
    "startCommand": "php artisan storage:link && php artisan migrate --force && ..."
                    ^^^^^^^^^^^^^^^^^^^^^^^
                    Run storage:link at deploy time instead
  }
}
```

#### 2. **Update AppServiceProvider.php** ✅
```php
public function register(): void
{
    // Disable Scribe config in production if class doesn't exist
    if (!class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
        $this->app->make('config')->set('scribe', []);
    }
}
```

#### 3. **Update config/scribe.php** ✅
```php
// Line 132 - Make it safe for when class doesn't exist
'in' => class_exists('Knuckles\Scribe\Config\AuthIn') 
    ? AuthIn::BEARER->value 
    : 'bearer',
```

---

## Error: "Could not scan for classes inside..."

### Masalah:
Warning messages seperti:
```
Could not scan for classes inside "/app/vendor/theseer/tokenizer/src/" 
which does not appear to be a file nor a folder
```

### Penyebab:
Composer mencoba scan classes yang sudah di-remove oleh `--no-dev` flag.

### Solusi:
⚠️ **Ini hanya warning, tidak mematikan deployment!**

- Caused by Laravel's autoload dumping mechanism
- Safe to ignore
- Tidak affect aplikasi runtime

Jika ingin remove warning:
```json
// railway.json
"buildCommand": "composer install --no-dev --no-scripts && composer dump-autoload --optimize"
```

---

## Verification

### Check Build Logs di Railway:

**❌ FAILED (Before Fix):**
```
Script @php artisan package:discover --ansi handling 
the post-autoload-dump event returned with error code 1
```

**✅ SUCCESS (After Fix):**
```
Generating optimized autoload files
Build completed successfully
Starting deployment...
```

### Check Deployed App:

```bash
# Check if app is running
curl https://your-app.railway.app/api

# Should return JSON:
{
  "message": "Lurah/RW Complaint Management System API",
  "version": "1.0.0",
  ...
}
```

---

## Prevention untuk Future

### Jangan load dev configs di production:

```php
// config/app.php or AppServiceProvider
if (app()->environment('production')) {
    // Unset dev-only configs
    config(['scribe' => null]);
}
```

### Alternative: Conditional Config Loading

```php
// config/scribe.php - Add at top
if (!class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
    return [];
}
```

---

## Complete Railway Setup Checklist

Setelah fix ini, setup Railway dengan:

### Environment Variables:
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database
FIREBASE_CREDENTIALS_BASE64=your-base64-here
```

### Services:

**1. Web Service**
- Start Command: `php artisan storage:link && php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT`

**2. Worker Service** 
- Start Command: `php artisan queue:work --verbose --tries=3 --timeout=90`
- Same environment variables as Web Service

---

## Test After Deployment

```bash
# 1. Check web service
curl https://your-app.railway.app/api

# 2. Check worker logs
railway logs --service mypengaduan-worker

# Should see: "Processing jobs..."

# 3. Test notification
# Create complaint via API → Check worker logs → Should process
```

---

## Summary

✅ **Fixed:**
- Scribe config error in production
- Build command updated to use `--no-scripts`
- Storage link moved to deploy phase
- AppServiceProvider handles missing Scribe gracefully

✅ **Result:**
- Clean deployment
- No errors
- Queue worker ready
- API accessible

**Status: READY FOR RAILWAY DEPLOYMENT 🚀**
