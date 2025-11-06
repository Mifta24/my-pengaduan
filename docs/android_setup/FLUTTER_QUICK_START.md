# 🚀 Flutter Quick Start - MyPengaduan

Panduan cepat untuk memulai development aplikasi MyPengaduan dengan Flutter.

## ✅ Requirements Check

Berdasarkan `flutter doctor` Anda:

```
✅ Flutter 3.35.1 (Stable Channel)
✅ Dart 3.9.0
✅ Android SDK 35.0.0
✅ Android Studio 2024.1
✅ VS Code 1.105.1 + Flutter Extension
✅ Windows 11
⚠️  Android licenses - Perlu di-accept
```

## 🔧 Setup Awal

### 1. Accept Android Licenses

```powershell
flutter doctor --android-licenses
```

Tekan `y` untuk accept semua licenses.

### 2. Create Project

```powershell
# Create project
flutter create mypengaduan_app

# Masuk ke folder project
cd mypengaduan_app

# Buka di VS Code
code .
```

### 3. Update pubspec.yaml

Copy paste dependencies ini ke `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter

  # UI
  cupertino_icons: ^1.0.8
  google_fonts: ^6.2.1
  
  # State Management
  provider: ^6.1.2
  
  # HTTP & API
  dio: ^5.7.0
  
  # Storage
  shared_preferences: ^2.3.2
  flutter_secure_storage: ^9.2.2
  
  # Firebase
  firebase_core: ^3.8.1
  firebase_messaging: ^15.1.5
  
  # Notifications
  flutter_local_notifications: ^18.0.1
  
  # Image
  image_picker: ^1.1.2
  cached_network_image: ^3.4.1
  
  # Utils
  intl: ^0.19.0
  permission_handler: ^11.3.1
```

Kemudian run:

```powershell
flutter pub get
```

---

## 🔥 Setup Firebase

### Step 1: Install FlutterFire CLI

```powershell
# Install FlutterFire CLI
dart pub global activate flutterfire_cli
```

### Step 2: Configure Firebase

```powershell
# Login ke Firebase (akan buka browser)
firebase login

# Configure project
flutterfire configure
```

Pilih:
1. Select Firebase project (atau create new)
2. Select platforms: **Android** dan **iOS** (jika perlu)
3. Wait hingga selesai

Ini akan generate:
- `lib/firebase_options.dart`
- Update `android/app/build.gradle`
- Download `google-services.json`

---

## 📁 Project Structure

Create folders & files berikut:

```
lib/
├── main.dart
├── firebase_options.dart (auto-generated)
├── config/
│   └── app_config.dart
├── models/
│   ├── user_model.dart
│   ├── auth_response.dart
│   ├── notification_model.dart
│   ├── complaint_model.dart
│   └── api_response.dart
├── services/
│   ├── auth_service.dart
│   ├── fcm_service.dart
│   ├── notification_service.dart
│   └── complaint_service.dart
├── providers/
│   ├── auth_provider.dart
│   ├── notification_provider.dart
│   └── complaint_provider.dart
└── screens/
    ├── auth/
    │   ├── login_screen.dart
    │   └── register_screen.dart
    ├── home/
    │   └── home_screen.dart
    ├── complaints/
    │   ├── complaint_list_screen.dart
    │   └── complaint_detail_screen.dart
    └── notifications/
        └── notification_list_screen.dart
```

---

## 🎯 Implementation Priority

### Phase 1: Authentication (Day 1)

**Files needed:**
1. `lib/config/app_config.dart`
2. `lib/models/user_model.dart`
3. `lib/models/auth_response.dart`
4. `lib/services/auth_service.dart`
5. `lib/providers/auth_provider.dart`
6. `lib/screens/auth/login_screen.dart`
7. `lib/main.dart`

**Steps:**
```powershell
# 1. Ganti BASE_URL di app_config.dart
# Ubah ke IP komputer Anda (bukan localhost!)
# Contoh: http://192.168.1.100:8000/api/

# 2. Test API pakai Postman dulu
# Pastikan endpoint /auth/login works

# 3. Run app
flutter run
```

### Phase 2: Firebase FCM (Day 2)

**Files needed:**
1. `lib/services/fcm_service.dart`
2. Update `lib/main.dart`

**Steps:**
```powershell
# 1. Pastikan Firebase sudah configured
flutterfire configure

# 2. Test FCM token generation
# Check console log untuk FCM token

# 3. Register token ke backend
# POST /device-tokens
```

### Phase 3: Notifications (Day 3)

**Files needed:**
1. `lib/models/notification_model.dart`
2. `lib/models/api_response.dart`
3. `lib/services/notification_service.dart`
4. `lib/providers/notification_provider.dart`
5. `lib/screens/notifications/notification_list_screen.dart`

### Phase 4: Complaints (Day 4-5)

**Files needed:**
1. `lib/models/complaint_model.dart`
2. `lib/services/complaint_service.dart`
3. `lib/providers/complaint_provider.dart`
4. `lib/screens/complaints/complaint_list_screen.dart`
5. `lib/screens/complaints/complaint_detail_screen.dart`

---

## 🔑 Important Code Snippets

### 1. App Config

**lib/config/app_config.dart**:
```dart
class AppConfig {
  // ⚠️ GANTI IP INI KE IP KOMPUTER ANDA!
  static const String baseUrl = 'http://192.168.1.100:8000/api/';
  
  static const String appName = 'MyPengaduan';
  static const String appVersion = '1.0.0';
  static const int defaultPerPage = 15;
  
  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String fcmTokenKey = 'fcm_token';
}
```

**Cara cek IP komputer:**

```powershell
# Windows
ipconfig

# Cari "IPv4 Address" di bagian WiFi/Ethernet
# Contoh: 192.168.1.100
```

### 2. Main.dart Setup

**lib/main.dart**:
```dart
import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:provider/provider.dart';
import 'firebase_options.dart';
import 'providers/auth_provider.dart';
import 'services/fcm_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Firebase
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  
  // Initialize FCM
  final fcmService = FCMService();
  await fcmService.initialize();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
      ],
      child: MaterialApp(
        title: 'MyPengaduan',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          useMaterial3: true,
        ),
        home: const LoginScreen(), // Start dengan login
      ),
    );
  }
}
```

### 3. Testing API Connection

**Test di Dart console:**

```dart
import 'package:dio/dio.dart';

void testConnection() async {
  final dio = Dio();
  try {
    final response = await dio.get('http://192.168.1.100:8000/api/');
    print('Connection successful: ${response.data}');
  } catch (e) {
    print('Connection failed: $e');
  }
}
```

---

## 🐛 Common Issues & Solutions

### 1. Error: Connection Refused

**Problem:** App tidak bisa connect ke backend

**Solution:**
```powershell
# 1. Pastikan Laravel server running
cd C:\laragon\www\mypengaduan
php artisan serve --host=0.0.0.0 --port=8000

# 2. Check firewall (allow port 8000)

# 3. Pastikan HP/Emulator dalam network yang sama dengan laptop
```

### 2. Error: Android License Not Accepted

**Problem:** `Some Android licenses not accepted`

**Solution:**
```powershell
flutter doctor --android-licenses
# Tekan 'y' untuk semua
```

### 3. Error: Firebase not configured

**Problem:** Firebase errors

**Solution:**
```powershell
# Re-configure Firebase
flutterfire configure

# Pilih project yang benar
# Pilih platform (Android)
```

### 4. Error: Cleartext HTTP not permitted (Android 9+)

**Problem:** HTTP requests blocked di Android

**Solution:**

**android/app/src/main/AndroidManifest.xml**:
```xml
<application
    android:usesCleartextTraffic="true"
    ...>
```

### 5. FCM Token null

**Problem:** FCM token tidak generate

**Solution:**
```dart
// Check permission
final messaging = FirebaseMessaging.instance;
final settings = await messaging.requestPermission();
print('Permission: ${settings.authorizationStatus}');

// Get token manually
final token = await messaging.getToken();
print('FCM Token: $token');
```

---

## 📱 Run Commands

### Development

```powershell
# Run di emulator/device
flutter run

# Run dengan hot reload
# (Save file = auto reload)

# Run dengan BASE_URL custom
flutter run --dart-define=BASE_URL=http://192.168.1.100:8000/api/
```

### Build

```powershell
# Build debug APK
flutter build apk --debug

# Build release APK
flutter build apk --release

# Build App Bundle (untuk Play Store)
flutter build appbundle --release
```

### Clean Build

```powershell
# Jika ada masalah, clean build
flutter clean
flutter pub get
flutter run
```

---

## 🧪 Testing Flow

### 1. Test Backend API (Postman)

```
✓ POST /auth/login - Get token
✓ POST /device-tokens - Register FCM token
✓ GET /notifications - Get notifications
✓ GET /complaints - Get complaints
```

### 2. Test Flutter App

```
✓ Login berhasil
✓ Token tersimpan
✓ FCM token terkirim ke backend
✓ Notification list muncul
✓ Mark as read works
✓ Logout works
```

### 3. Test Push Notification

**Cara test:**

1. Login di app
2. Minimize app (jangan close)
3. Trigger notifikasi dari backend/Postman
4. Check notification muncul

---

## 📊 Development Workflow

### Daily Workflow:

```powershell
# 1. Start Laravel server
cd C:\laragon\www\mypengaduan
php artisan serve --host=0.0.0.0 --port=8000

# 2. New terminal - Start Flutter
cd path/to/mypengaduan_app
flutter run

# 3. Code & save (hot reload otomatis)

# 4. Test di emulator/device
```

### Git Workflow:

```powershell
# Create .gitignore
# Exclude:
# - build/
# - .dart_tool/
# - .flutter-plugins
# - .flutter-plugins-dependencies

git init
git add .
git commit -m "Initial commit"
```

---

## 📚 Resources

### Documentation
- [FLUTTER_INTEGRATION_GUIDE.md](./FLUTTER_INTEGRATION_GUIDE.md) - Complete guide
- [API_TESTING_GUIDE.md](./API_TESTING_GUIDE.md) - API documentation
- [NOTIFICATION_TYPES_ANDROID.md](./NOTIFICATION_TYPES_ANDROID.md) - Notification types

### Learning Resources
- [Flutter Docs](https://docs.flutter.dev/)
- [Dio Package](https://pub.dev/packages/dio)
- [Provider Pattern](https://pub.dev/packages/provider)
- [Firebase Messaging](https://pub.dev/packages/firebase_messaging)

### Tools
- **VS Code Extensions:**
  - Flutter
  - Dart
  - Flutter Widget Snippets
  - Awesome Flutter Snippets

---

## ✨ Next Steps

1. **Accept Android licenses**
   ```powershell
   flutter doctor --android-licenses
   ```

2. **Setup Firebase**
   ```powershell
   flutterfire configure
   ```

3. **Create project structure**
   - Copy file structure dari guide

4. **Implement authentication**
   - Login screen
   - Token management

5. **Test API connection**
   - Postman testing
   - Flutter testing

6. **Implement FCM**
   - Token registration
   - Notification handling

7. **Build features**
   - Notifications
   - Complaints
   - Profile

---

## 🎯 Target Timeline

- **Day 1:** Setup project + Authentication
- **Day 2:** Firebase FCM setup
- **Day 3:** Notification list
- **Day 4-5:** Complaint CRUD
- **Day 6-7:** Polish UI/UX
- **Day 8+:** Testing & bug fixes

---

## 🆘 Need Help?

1. Check `FLUTTER_INTEGRATION_GUIDE.md` untuk code lengkap
2. Test API di Postman dulu
3. Check console log untuk errors
4. Google error message + "flutter"

---

## 🎉 You're Ready!

Backend sudah siap 100%:
- ✅ API tested & working
- ✅ FCM integrated
- ✅ ISO 8601 timestamps
- ✅ Complete documentation

Sekarang tinggal build Flutter app! 🚀

**Happy Coding!** 💙

---

**Last Updated:** November 6, 2025  
**Flutter Version:** 3.35.1  
**Dart Version:** 3.9.0  
**Backend:** Laravel 10.x
