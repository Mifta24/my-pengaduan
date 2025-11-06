# 📱 Flutter Integration Guide - MyPengaduan API

Panduan lengkap untuk integrasi aplikasi Flutter dengan MyPengaduan Backend API.

## 📋 Table of Contents
- [Overview](#overview)
- [Requirements](#requirements)
- [Project Setup](#project-setup)
- [Dependencies](#dependencies)
- [Configuration](#configuration)
- [Authentication](#authentication)
- [Firebase Cloud Messaging (FCM)](#firebase-cloud-messaging-fcm)
- [API Integration](#api-integration)
- [State Management](#state-management)
- [Error Handling](#error-handling)
- [Best Practices](#best-practices)

---

## 🎯 Overview

MyPengaduan API menggunakan:
- **Framework Backend**: Laravel 10.x dengan PostgreSQL
- **Authentication**: Laravel Sanctum (Bearer Token)
- **Push Notifications**: Firebase Cloud Messaging (FCM)
- **Response Format**: JSON dengan ISO 8601 timestamps
- **Pagination**: Laravel pagination dengan metadata lengkap

---

## ✅ Requirements

Berdasarkan `flutter doctor` Anda:
- ✅ Flutter 3.35.1 (Stable)
- ✅ Dart 3.9.0
- ✅ Android SDK 35.0.0
- ✅ Windows 11
- ✅ VS Code 1.105.1
- ⚠️ Jalankan: `flutter doctor --android-licenses` untuk accept licenses

---

## 🚀 Project Setup

### 1. Create Flutter Project

```bash
flutter create mypengaduan_app
cd mypengaduan_app
```

### 2. Update pubspec.yaml

```yaml
name: mypengaduan_app
description: Aplikasi Pengaduan Masyarakat
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: '>=3.9.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter

  # UI Components
  cupertino_icons: ^1.0.8
  google_fonts: ^6.2.1
  flutter_svg: ^2.0.10+1
  
  # State Management
  provider: ^6.1.2
  get: ^4.6.6
  
  # HTTP & API
  http: ^1.2.2
  dio: ^5.7.0
  
  # Local Storage
  shared_preferences: ^2.3.2
  flutter_secure_storage: ^9.2.2
  
  # Firebase
  firebase_core: ^3.8.1
  firebase_messaging: ^15.1.5
  firebase_analytics: ^11.3.8
  
  # Notifications
  flutter_local_notifications: ^18.0.1
  
  # Image Handling
  image_picker: ^1.1.2
  cached_network_image: ^3.4.1
  
  # Date & Time
  intl: ^0.19.0
  
  # Utils
  url_launcher: ^6.3.1
  permission_handler: ^11.3.1
  connectivity_plus: ^6.1.0
  
  # Loading & Dialogs
  flutter_spinkit: ^5.2.1
  
dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^5.0.0

flutter:
  uses-material-design: true
  
  # assets:
  #   - assets/images/
  #   - assets/icons/
```

### 3. Install Dependencies

```bash
flutter pub get
```

---

## 🔧 Configuration

### 1. Create Config File

**lib/config/app_config.dart**:
```dart
class AppConfig {
  // API Configuration
  static const String baseUrl = String.fromEnvironment(
    'BASE_URL',
    defaultValue: 'http://192.168.1.100:8000/api/',
  );
  
  // App Configuration
  static const String appName = 'MyPengaduan';
  static const String appVersion = '1.0.0';
  
  // Pagination
  static const int defaultPerPage = 15;
  
  // Timeout
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  
  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String fcmTokenKey = 'fcm_token';
}
```

### 2. Environment Configuration

**Run with environment**:
```bash
# Development
flutter run --dart-define=BASE_URL=http://192.168.1.100:8000/api/

# Production
flutter run --dart-define=BASE_URL=https://your-domain.com/api/
```

---

## 🔐 Authentication

### 1. Auth Models

**lib/models/user_model.dart**:
```dart
class User {
  final int id;
  final String name;
  final String email;
  final String phone;
  final String address;
  final List<String> roles;
  final DateTime createdAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.address,
    required this.roles,
    required this.createdAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      address: json['address'],
      roles: List<String>.from(json['roles']),
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'address': address,
      'roles': roles,
      'created_at': createdAt.toIso8601String(),
    };
  }
}
```

**lib/models/auth_response.dart**:
```dart
class AuthResponse {
  final bool success;
  final String message;
  final User user;
  final String token;

  AuthResponse({
    required this.success,
    required this.message,
    required this.user,
    required this.token,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      success: json['success'],
      message: json['message'],
      user: User.fromJson(json['data']['user']),
      token: json['data']['token'],
    );
  }
}
```

### 2. Auth Service

**lib/services/auth_service.dart**:
```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/app_config.dart';
import '../models/user_model.dart';
import '../models/auth_response.dart';

class AuthService {
  final Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  AuthService() : _dio = Dio(BaseOptions(
    baseUrl: AppConfig.baseUrl,
    connectTimeout: AppConfig.connectionTimeout,
    receiveTimeout: AppConfig.receiveTimeout,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  )) {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await getToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
    ));
  }

  // Login
  Future<AuthResponse> login(String email, String password) async {
    try {
      final response = await _dio.post('auth/login', data: {
        'email': email,
        'password': password,
      });
      
      final authResponse = AuthResponse.fromJson(response.data);
      await saveToken(authResponse.token);
      await saveUser(authResponse.user);
      
      return authResponse;
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Register
  Future<AuthResponse> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String passwordConfirmation,
    required String address,
  }) async {
    try {
      final response = await _dio.post('auth/register', data: {
        'name': name,
        'email': email,
        'phone': phone,
        'password': password,
        'password_confirmation': passwordConfirmation,
        'address': address,
      });
      
      final authResponse = AuthResponse.fromJson(response.data);
      await saveToken(authResponse.token);
      await saveUser(authResponse.user);
      
      return authResponse;
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Get Profile
  Future<User> getProfile() async {
    try {
      final response = await _dio.get('auth/profile');
      return User.fromJson(response.data['data']);
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Logout
  Future<void> logout() async {
    try {
      await _dio.post('auth/logout');
      await clearAuth();
    } catch (e) {
      await clearAuth();
      throw _handleError(e);
    }
  }

  // Token Management
  Future<void> saveToken(String token) async {
    await _storage.write(key: AppConfig.tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: AppConfig.tokenKey);
  }

  Future<void> saveUser(User user) async {
    await _storage.write(key: AppConfig.userKey, value: user.toJson().toString());
  }

  Future<void> clearAuth() async {
    await _storage.delete(key: AppConfig.tokenKey);
    await _storage.delete(key: AppConfig.userKey);
  }

  Future<bool> isAuthenticated() async {
    final token = await getToken();
    return token != null;
  }

  // Error Handler
  String _handleError(dynamic error) {
    if (error is DioException) {
      if (error.response != null) {
        final data = error.response!.data;
        return data['message'] ?? 'Terjadi kesalahan';
      }
      return 'Koneksi gagal. Periksa internet Anda.';
    }
    return error.toString();
  }
}
```

### 3. Auth Provider (State Management)

**lib/providers/auth_provider.dart**:
```dart
import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';

class AuthProvider extends ChangeNotifier {
  final AuthService _authService = AuthService();
  
  User? _user;
  bool _isLoading = false;
  String? _error;

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;

  // Login
  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _authService.login(email, password);
      _user = response.user;
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Register
  Future<bool> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String passwordConfirmation,
    required String address,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _authService.register(
        name: name,
        email: email,
        phone: phone,
        password: password,
        passwordConfirmation: passwordConfirmation,
        address: address,
      );
      _user = response.user;
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Load User
  Future<void> loadUser() async {
    try {
      _user = await _authService.getProfile();
      notifyListeners();
    } catch (e) {
      await logout();
    }
  }

  // Logout
  Future<void> logout() async {
    await _authService.logout();
    _user = null;
    notifyListeners();
  }

  // Check Authentication
  Future<bool> checkAuth() async {
    final isAuth = await _authService.isAuthenticated();
    if (isAuth) {
      await loadUser();
    }
    return isAuth;
  }
}
```

---

## 🔔 Firebase Cloud Messaging (FCM)

### 1. Setup Firebase

#### a. Add Firebase to Flutter

```bash
# Install FlutterFire CLI
dart pub global activate flutterfire_cli

# Configure Firebase
flutterfire configure
```

#### b. Initialize Firebase

**lib/main.dart**:
```dart
import 'package:firebase_core/firebase_core.dart';
import 'firebase_options.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Firebase
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  
  runApp(const MyApp());
}
```

### 2. FCM Service

**lib/services/fcm_service.dart**:
```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:dio/dio.dart';
import '../config/app_config.dart';

// Top-level function for background messages
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  print('Background message: ${message.messageId}');
}

class FCMService {
  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();
  final Dio _dio = Dio();

  // Initialize FCM
  Future<void> initialize() async {
    // Request permission
    await _requestPermission();
    
    // Configure local notifications
    await _configureLocalNotifications();
    
    // Get FCM token
    final token = await getToken();
    if (token != null) {
      await _sendTokenToServer(token);
    }
    
    // Handle token refresh
    _firebaseMessaging.onTokenRefresh.listen(_sendTokenToServer);
    
    // Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
    
    // Handle background messages
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
    
    // Handle notification tap
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);
    
    // Check for initial message (app opened from terminated state)
    final initialMessage = await _firebaseMessaging.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage);
    }
  }

  // Request Permission
  Future<void> _requestPermission() async {
    NotificationSettings settings = await _firebaseMessaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );

    print('Permission status: ${settings.authorizationStatus}');
  }

  // Configure Local Notifications
  Future<void> _configureLocalNotifications() async {
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosSettings = DarwinInitializationSettings();
    
    const initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (details) {
        // Handle notification tap from local notification
        print('Notification tapped: ${details.payload}');
      },
    );

    // Create Android notification channel
    const androidChannel = AndroidNotificationChannel(
      'complaints_channel',
      'Pengaduan',
      description: 'Notifikasi terkait pengaduan',
      importance: Importance.high,
    );

    await _localNotifications
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(androidChannel);
  }

  // Get FCM Token
  Future<String?> getToken() async {
    return await _firebaseMessaging.getToken();
  }

  // Send Token to Server
  Future<void> _sendTokenToServer(String token) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final authToken = prefs.getString(AppConfig.tokenKey);
      
      if (authToken == null) return;

      await _dio.post(
        '${AppConfig.baseUrl}device-tokens',
        data: {
          'device_token': token,
          'device_type': 'android', // or 'ios' based on platform
          'device_model': '', // Get from device_info_plus
          'os_version': '',
          'app_version': AppConfig.appVersion,
        },
        options: Options(
          headers: {
            'Authorization': 'Bearer $authToken',
            'Accept': 'application/json',
          },
        ),
      );
      
      // Save token locally
      await prefs.setString(AppConfig.fcmTokenKey, token);
    } catch (e) {
      print('Error sending token to server: $e');
    }
  }

  // Handle Foreground Message
  void _handleForegroundMessage(RemoteMessage message) {
    print('Foreground message: ${message.messageId}');
    
    final notification = message.notification;
    final data = message.data;

    if (notification != null) {
      _showLocalNotification(
        title: notification.title ?? '',
        body: notification.body ?? '',
        payload: data['type'] ?? '',
      );
    }
  }

  // Show Local Notification
  Future<void> _showLocalNotification({
    required String title,
    required String body,
    required String payload,
  }) async {
    const androidDetails = AndroidNotificationDetails(
      'complaints_channel',
      'Pengaduan',
      channelDescription: 'Notifikasi terkait pengaduan',
      importance: Importance.high,
      priority: Priority.high,
    );

    const iosDetails = DarwinNotificationDetails();

    const details = NotificationDetails(
      android: androidDetails,
      iOS: iosDetails,
    );

    await _localNotifications.show(
      DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title,
      body,
      details,
      payload: payload,
    );
  }

  // Handle Notification Tap
  void _handleNotificationTap(RemoteMessage message) {
    print('Notification tapped: ${message.data}');
    
    final type = message.data['type'];
    
    // Navigate based on notification type
    switch (type) {
      case 'complaint_status_changed':
      case 'admin_response':
      case 'complaint_resolved':
        final complaintId = message.data['complaint_id'];
        // Navigate to complaint detail
        // Get.toNamed('/complaint/$complaintId');
        break;
      case 'announcement_created':
        final announcementId = message.data['announcement_id'];
        // Navigate to announcement detail
        // Get.toNamed('/announcement/$announcementId');
        break;
      default:
        // Navigate to notifications page
        // Get.toNamed('/notifications');
        break;
    }
  }
}
```

---

## 🌐 API Integration

### 1. API Response Models

**lib/models/api_response.dart**:
```dart
class ApiResponse<T> {
  final bool success;
  final String message;
  final T? data;
  final Map<String, dynamic>? errors;

  ApiResponse({
    required this.success,
    required this.message,
    this.data,
    this.errors,
  });

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic)? fromJsonT,
  ) {
    return ApiResponse(
      success: json['success'],
      message: json['message'],
      data: json['data'] != null && fromJsonT != null
          ? fromJsonT(json['data'])
          : null,
      errors: json['errors'],
    );
  }
}

class PaginatedResponse<T> {
  final bool success;
  final String message;
  final PaginationMeta meta;
  final List<T> data;
  final int? unreadCount;

  PaginatedResponse({
    required this.success,
    required this.message,
    required this.meta,
    required this.data,
    this.unreadCount,
  });

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic) fromJsonT,
  ) {
    return PaginatedResponse(
      success: json['success'],
      message: json['message'],
      meta: PaginationMeta.fromJson(json['meta']),
      data: (json['data'] as List).map((item) => fromJsonT(item)).toList(),
      unreadCount: json['unread_count'],
    );
  }
}

class PaginationMeta {
  final int currentPage;
  final int? from;
  final int lastPage;
  final int perPage;
  final int? to;
  final int total;

  PaginationMeta({
    required this.currentPage,
    this.from,
    required this.lastPage,
    required this.perPage,
    this.to,
    required this.total,
  });

  factory PaginationMeta.fromJson(Map<String, dynamic> json) {
    return PaginationMeta(
      currentPage: json['current_page'],
      from: json['from'],
      lastPage: json['last_page'],
      perPage: json['per_page'],
      to: json['to'],
      total: json['total'],
    );
  }
}
```

### 2. Notification Models & Service

**lib/models/notification_model.dart**:
```dart
class NotificationModel {
  final int id;
  final String type;
  final String title;
  final String body;
  final Map<String, dynamic>? data;
  final bool isRead;
  final DateTime? readAt;
  final DateTime createdAt;
  final DateTime updatedAt;

  NotificationModel({
    required this.id,
    required this.type,
    required this.title,
    required this.body,
    this.data,
    required this.isRead,
    this.readAt,
    required this.createdAt,
    required this.updatedAt,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    return NotificationModel(
      id: json['id'],
      type: json['type'],
      title: json['title'],
      body: json['body'],
      data: json['data'],
      isRead: json['is_read'],
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}
```

**lib/services/notification_service.dart**:
```dart
import 'package:dio/dio.dart';
import '../config/app_config.dart';
import '../models/api_response.dart';
import '../models/notification_model.dart';

class NotificationService {
  final Dio _dio;

  NotificationService(this._dio);

  // Get Notifications
  Future<PaginatedResponse<NotificationModel>> getNotifications({
    String? status,
    String? type,
    int page = 1,
    int perPage = 15,
  }) async {
    try {
      final response = await _dio.get('notifications', queryParameters: {
        if (status != null) 'status': status,
        if (type != null) 'type': type,
        'page': page,
        'per_page': perPage,
      });

      return PaginatedResponse.fromJson(
        response.data,
        (json) => NotificationModel.fromJson(json),
      );
    } catch (e) {
      rethrow;
    }
  }

  // Mark as Read
  Future<void> markAsRead(int id) async {
    try {
      await _dio.post('notifications/$id/read');
    } catch (e) {
      rethrow;
    }
  }

  // Mark All as Read
  Future<void> markAllAsRead() async {
    try {
      await _dio.post('notifications/read-all');
    } catch (e) {
      rethrow;
    }
  }
}
```

---

## 🎨 UI Examples

### Login Screen

**lib/screens/auth/login_screen.dart**:
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  'Login',
                  style: Theme.of(context).textTheme.headlineMedium,
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 32),
                TextFormField(
                  controller: _emailController,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(
                    labelText: 'Email',
                    prefixIcon: Icon(Icons.email),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Email tidak boleh kosong';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _passwordController,
                  obscureText: _obscurePassword,
                  decoration: InputDecoration(
                    labelText: 'Password',
                    prefixIcon: const Icon(Icons.lock),
                    suffixIcon: IconButton(
                      icon: Icon(
                        _obscurePassword
                            ? Icons.visibility
                            : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() {
                          _obscurePassword = !_obscurePassword;
                        });
                      },
                    ),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Password tidak boleh kosong';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 24),
                Consumer<AuthProvider>(
                  builder: (context, auth, child) {
                    return ElevatedButton(
                      onPressed: auth.isLoading ? null : _handleLogin,
                      child: auth.isLoading
                          ? const CircularProgressIndicator()
                          : const Text('Login'),
                    );
                  },
                ),
                if (Provider.of<AuthProvider>(context).error != null)
                  Padding(
                    padding: const EdgeInsets.only(top: 16),
                    child: Text(
                      Provider.of<AuthProvider>(context).error!,
                      style: const TextStyle(color: Colors.red),
                      textAlign: TextAlign.center,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _handleLogin() async {
    if (_formKey.currentState!.validate()) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      
      final success = await authProvider.login(
        _emailController.text,
        _passwordController.text,
      );

      if (success && mounted) {
        Navigator.pushReplacementNamed(context, '/home');
      }
    }
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
```

### Notification List Screen

**lib/screens/notifications/notification_list_screen.dart**:
```dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/notification_model.dart';
import '../../services/notification_service.dart';

class NotificationListScreen extends StatefulWidget {
  const NotificationListScreen({Key? key}) : super(key: key);

  @override
  State<NotificationListScreen> createState() => _NotificationListScreenState();
}

class _NotificationListScreenState extends State<NotificationListScreen> {
  late NotificationService _notificationService;
  List<NotificationModel> _notifications = [];
  bool _isLoading = false;
  int _currentPage = 1;
  int _unreadCount = 0;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    
    try {
      final response = await _notificationService.getNotifications(
        page: _currentPage,
      );
      
      setState(() {
        _notifications = response.data;
        _unreadCount = response.unreadCount ?? 0;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi'),
        actions: [
          if (_unreadCount > 0)
            TextButton(
              onPressed: _markAllAsRead,
              child: const Text('Tandai Semua Dibaca'),
            ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadNotifications,
              child: ListView.builder(
                itemCount: _notifications.length,
                itemBuilder: (context, index) {
                  final notification = _notifications[index];
                  return _buildNotificationItem(notification);
                },
              ),
            ),
    );
  }

  Widget _buildNotificationItem(NotificationModel notification) {
    return Card(
      color: notification.isRead ? null : Colors.blue.shade50,
      child: ListTile(
        leading: CircleAvatar(
          child: Icon(_getNotificationIcon(notification.type)),
        ),
        title: Text(
          notification.title,
          style: TextStyle(
            fontWeight: notification.isRead ? FontWeight.normal : FontWeight.bold,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(notification.body),
            const SizedBox(height: 4),
            Text(
              _formatTimestamp(notification.createdAt),
              style: Theme.of(context).textTheme.bodySmall,
            ),
          ],
        ),
        onTap: () => _handleNotificationTap(notification),
      ),
    );
  }

  IconData _getNotificationIcon(String type) {
    switch (type) {
      case 'complaint_status_changed':
        return Icons.update;
      case 'admin_response':
        return Icons.message;
      case 'complaint_resolved':
        return Icons.check_circle;
      case 'announcement_created':
        return Icons.announcement;
      default:
        return Icons.notifications;
    }
  }

  String _formatTimestamp(DateTime timestamp) {
    final now = DateTime.now();
    final difference = now.difference(timestamp);

    if (difference.inMinutes < 1) {
      return 'Baru saja';
    } else if (difference.inHours < 1) {
      return '${difference.inMinutes} menit yang lalu';
    } else if (difference.inDays < 1) {
      return '${difference.inHours} jam yang lalu';
    } else if (difference.inDays < 7) {
      return '${difference.inDays} hari yang lalu';
    } else {
      return DateFormat('dd MMM yyyy, HH:mm').format(timestamp);
    }
  }

  Future<void> _handleNotificationTap(NotificationModel notification) async {
    if (!notification.isRead) {
      await _notificationService.markAsRead(notification.id);
      setState(() {
        notification = NotificationModel(
          id: notification.id,
          type: notification.type,
          title: notification.title,
          body: notification.body,
          data: notification.data,
          isRead: true,
          readAt: DateTime.now(),
          createdAt: notification.createdAt,
          updatedAt: notification.updatedAt,
        );
      });
    }

    // Navigate based on type
    // Implementation depends on your routing
  }

  Future<void> _markAllAsRead() async {
    try {
      await _notificationService.markAllAsRead();
      await _loadNotifications();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e')),
      );
    }
  }
}
```

---

## 📝 Main App Setup

**lib/main.dart**:
```dart
import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:provider/provider.dart';
import 'firebase_options.dart';
import 'providers/auth_provider.dart';
import 'services/fcm_service.dart';
import 'screens/auth/login_screen.dart';
import 'screens/home/home_screen.dart';

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
        // Add other providers here
      ],
      child: MaterialApp(
        title: 'MyPengaduan',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          useMaterial3: true,
        ),
        home: const SplashScreen(),
        routes: {
          '/login': (context) => const LoginScreen(),
          '/home': (context) => const HomeScreen(),
          // Add other routes
        },
      ),
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final isAuth = await authProvider.checkAuth();
    
    await Future.delayed(const Duration(seconds: 2));
    
    if (mounted) {
      Navigator.pushReplacementNamed(
        context,
        isAuth ? '/home' : '/login',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
```

---

## ✅ Testing Checklist

- [ ] Accept Android licenses: `flutter doctor --android-licenses`
- [ ] Firebase setup complete
- [ ] Login/Register flow works
- [ ] FCM token registration successful
- [ ] Receive push notifications
- [ ] Notification list displays correctly
- [ ] Mark notification as read works
- [ ] API integration complete
- [ ] Error handling works
- [ ] Offline mode handling

---

## 🚀 Run Commands

```bash
# Run on Android
flutter run

# Run with environment variable
flutter run --dart-define=BASE_URL=http://192.168.1.100:8000/api/

# Build APK
flutter build apk

# Build App Bundle
flutter build appbundle
```

---

**Happy Coding! 🎉**
