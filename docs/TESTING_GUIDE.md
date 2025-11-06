# 🧪 Quick Testing Guide - FCM Backend

## ⚡ Quick Test Commands

### 1. Check Database Tables
```bash
php artisan tinker

# Check if tables exist
\App\Models\UserDevice::count();
\App\Models\NotificationSetting::count();
```

### 2. Test Firebase Service
```bash
php artisan tinker

# Load Firebase service
$firebase = app(\App\Services\FirebaseService::class);

# Should not throw error if credentials are set correctly
dd($firebase);
```

### 3. Create Test Device Token
```bash
php artisan tinker

$user = \App\Models\User::first();

$device = \App\Models\UserDevice::create([
    'user_id' => $user->id,
    'device_token' => 'TEST_TOKEN_12345',
    'device_type' => 'android',
    'device_model' => 'Test Device',
    'os_version' => 'Android 13',
    'app_version' => '1.0.0',
    'is_active' => true,
    'last_used_at' => now(),
]);

dd($device);
```

### 4. Test User Relationships
```bash
php artisan tinker

$user = \App\Models\User::first();

# Test devices relationship
$user->devices;

# Test notification settings relationship
$user->notificationSettings;

# Test get active tokens method
$user->getActiveDeviceTokens();
```

### 5. Create Notification Settings
```bash
php artisan tinker

$user = \App\Models\User::first();

$settings = \App\Models\NotificationSetting::create([
    'user_id' => $user->id,
    'complaint_created' => true,
    'complaint_status_changed' => true,
    'announcement_created' => true,
    'admin_response' => true,
    'comment_added' => true,
    'push_enabled' => true,
]);

dd($settings);
```

---

## 🌐 API Testing with cURL

### Setup: Get Auth Token First
```bash
# Login to get token
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Copy the "token" from response
# Use it as YOUR_TOKEN below
```

### Test 1: Register Device Token
```bash
curl -X POST http://localhost/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "TEST_FCM_TOKEN_123456789",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "Android 13",
    "app_version": "1.0.0"
  }'
```

### Test 2: Get User Devices
```bash
curl -X GET http://localhost/api/device-tokens \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test 3: Get Notification Settings
```bash
curl -X GET http://localhost/api/notification-settings \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test 4: Update Notification Settings
```bash
curl -X PUT http://localhost/api/notification-settings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "complaint_created": true,
    "complaint_status_changed": true,
    "announcement_created": false,
    "admin_response": true,
    "comment_added": true,
    "push_enabled": true
  }'
```

### Test 5: Delete Device
```bash
# First, get device ID from Test 2
# Then:
curl -X DELETE http://localhost/api/device-tokens/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🧪 Postman Collection

Import this JSON to Postman:

```json
{
  "info": {
    "name": "MyPengaduan FCM API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Register Device Token",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"device_token\": \"TEST_FCM_TOKEN\",\n  \"device_type\": \"android\",\n  \"device_model\": \"Samsung Galaxy S21\",\n  \"os_version\": \"Android 13\",\n  \"app_version\": \"1.0.0\"\n}"
        },
        "url": {
          "raw": "{{base_url}}/api/device-tokens",
          "host": ["{{base_url}}"],
          "path": ["api", "device-tokens"]
        }
      }
    },
    {
      "name": "Get User Devices",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/device-tokens",
          "host": ["{{base_url}}"],
          "path": ["api", "device-tokens"]
        }
      }
    },
    {
      "name": "Get Notification Settings",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/notification-settings",
          "host": ["{{base_url}}"],
          "path": ["api", "notification-settings"]
        }
      }
    },
    {
      "name": "Update Notification Settings",
      "request": {
        "method": "PUT",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"complaint_created\": true,\n  \"complaint_status_changed\": true,\n  \"announcement_created\": true,\n  \"admin_response\": true,\n  \"comment_added\": true,\n  \"push_enabled\": true\n}"
        },
        "url": {
          "raw": "{{base_url}}/api/notification-settings",
          "host": ["{{base_url}}"],
          "path": ["api", "notification-settings"]
        }
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost"
    },
    {
      "key": "token",
      "value": "YOUR_SANCTUM_TOKEN"
    }
  ]
}
```

---

## ✅ Checklist Before Moving to Day 2

- [ ] **Firebase project created**
- [ ] **google-services.json downloaded** (for Android)
- [ ] **GoogleService-Info.plist downloaded** (for iOS)
- [ ] **firebase-credentials.json downloaded and placed** in `storage/app/firebase/`
- [ ] **.env updated** with FIREBASE_PROJECT_ID
- [ ] **Test Firebase service** (no errors in tinker)
- [ ] **Test API endpoints** (all return 200 OK)
- [ ] **Database tables working** (can create records)
- [ ] **User relationships working** (can access devices & settings)

---

## 🐛 Common Issues & Solutions

### Issue 1: "Class 'Kreait\Firebase\Factory' not found"
**Solution:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue 2: "Firebase credentials file not found"
**Solution:**
1. Check file location: `storage/app/firebase/firebase-credentials.json`
2. Check .env: `FIREBASE_CREDENTIALS=firebase/firebase-credentials.json`
3. File must be valid JSON format
4. Run: `php artisan config:clear`

### Issue 3: "Invalid credentials"
**Solution:**
1. Re-download credentials from Firebase Console
2. Make sure it's the **Service Account** JSON (not google-services.json)
3. Check file permissions (should be readable)

### Issue 4: API returns 401 Unauthorized
**Solution:**
1. Make sure you're logged in: `POST /api/auth/login`
2. Copy the token from response
3. Add header: `Authorization: Bearer YOUR_TOKEN`
4. Token format must be: `Bearer <token>` (with space!)

### Issue 5: "SQLSTATE[23000]: Integrity constraint violation"
**Solution:**
```bash
# Run migrations
php artisan migrate:fresh

# Or if you want to keep data
php artisan migrate:rollback --step=2
php artisan migrate
```

---

## 📞 Need Help?

### Quick Debug Commands:
```bash
# Check Laravel version
php artisan --version

# Check routes
php artisan route:list --path=api

# Check config
php artisan config:show firebase

# Clear all cache
php artisan optimize:clear

# Check database connection
php artisan tinker
DB::connection()->getPdo();
```

### Log Files:
- Laravel logs: `storage/logs/laravel.log`
- FCM errors will be logged here with tag: `[FCM Error]`

---

## 🎯 Success Criteria

You're ready for Day 2 if:
- ✅ All API endpoints return 200 OK
- ✅ Device tokens can be registered
- ✅ Notification settings can be created/updated
- ✅ Firebase service loads without errors
- ✅ Database relationships work correctly
- ✅ No errors in `storage/logs/laravel.log`

**If all green → PROCEED TO DAY 2!** 🚀
