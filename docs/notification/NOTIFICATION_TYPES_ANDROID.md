# 🔔 Notification Types & Data Structure

Dokumentasi lengkap tentang tipe-tipe notifikasi dan struktur data yang dikirim melalui FCM.

## 📋 Notification Types

### 1. `complaint_created`
**Trigger**: Ketika user membuat pengaduan baru

**Data Structure**:
```json
{
    "type": "complaint_created",
    "title": "Pengaduan Baru Dibuat",
    "body": "Pengaduan Anda telah berhasil dibuat dan akan segera diproses",
    "data": {
        "complaint_id": 123,
        "complaint_title": "Jalan Rusak di Jl. Merdeka",
        "status": "pending"
    }
}
```

**Android Action**: Navigate to Complaint Detail

---

### 2. `complaint_status_changed`
**Trigger**: Ketika status pengaduan berubah

**Data Structure**:
```json
{
    "type": "complaint_status_changed",
    "title": "Status Pengaduan Diperbarui",
    "body": "Status pengaduan Anda berubah menjadi: Sedang Diproses",
    "data": {
        "complaint_id": 123,
        "complaint_title": "Jalan Rusak di Jl. Merdeka",
        "old_status": "pending",
        "new_status": "in_progress",
        "changed_by": "Admin Name"
    }
}
```

**Possible Status Values**:
- `pending` - Menunggu
- `in_progress` - Sedang Diproses
- `resolved` - Selesai
- `rejected` - Ditolak

**Android Action**: 
- Show status update dialog
- Navigate to Complaint Detail
- Update local complaint status

---

### 3. `admin_response`
**Trigger**: Ketika admin memberikan tanggapan pada pengaduan

**Data Structure**:
```json
{
    "type": "admin_response",
    "title": "Tanggapan Baru dari Admin",
    "body": "Admin telah memberikan tanggapan pada pengaduan Anda",
    "data": {
        "complaint_id": 123,
        "complaint_title": "Jalan Rusak di Jl. Merdeka",
        "response_id": 45,
        "response_preview": "Terima kasih atas laporannya. Tim kami akan...",
        "admin_name": "Admin Name"
    }
}
```

**Android Action**: 
- Navigate to Complaint Detail
- Scroll to responses section
- Highlight new response

---

### 4. `complaint_resolved`
**Trigger**: Ketika pengaduan ditandai selesai

**Data Structure**:
```json
{
    "type": "complaint_resolved",
    "title": "Pengaduan Diselesaikan",
    "body": "Pengaduan Anda telah diselesaikan",
    "data": {
        "complaint_id": 123,
        "complaint_title": "Jalan Rusak di Jl. Merdeka",
        "resolved_at": "2025-01-15T10:30:45.123456Z",
        "resolution_note": "Jalan telah diperbaiki"
    }
}
```

**Android Action**: 
- Show completion dialog
- Request user rating/feedback
- Navigate to Complaint Detail

---

### 5. `announcement_created`
**Trigger**: Ketika admin membuat pengumuman baru

**Data Structure**:
```json
{
    "type": "announcement_created",
    "title": "Pengumuman Baru",
    "body": "Pemberitahuan Pemeliharaan Sistem",
    "data": {
        "announcement_id": 67,
        "announcement_title": "Pemberitahuan Pemeliharaan Sistem",
        "is_urgent": true,
        "category": "maintenance"
    }
}
```

**Android Action**: 
- Navigate to Announcement Detail
- Show urgent badge if `is_urgent: true`

---

### 6. `comment_added`
**Trigger**: Ketika ada komentar baru pada pengumuman yang user ikuti

**Data Structure**:
```json
{
    "type": "comment_added",
    "title": "Komentar Baru",
    "body": "Ada komentar baru pada pengumuman yang Anda ikuti",
    "data": {
        "announcement_id": 67,
        "announcement_title": "Pemberitahuan Pemeliharaan Sistem",
        "comment_id": 89,
        "comment_preview": "Terima kasih atas informasinya...",
        "commenter_name": "John Doe"
    }
}
```

**Android Action**: 
- Navigate to Announcement Detail
- Scroll to comments section

---

## 🏗️ FCM Message Structure

### Full FCM Payload Example

```json
{
    "notification": {
        "title": "Status Pengaduan Diperbarui",
        "body": "Status pengaduan Anda berubah menjadi: Sedang Diproses"
    },
    "data": {
        "type": "complaint_status_changed",
        "complaint_id": "123",
        "complaint_title": "Jalan Rusak di Jl. Merdeka",
        "old_status": "pending",
        "new_status": "in_progress",
        "timestamp": "2025-01-15T10:30:45.123456Z",
        "click_action": "FLUTTER_NOTIFICATION_CLICK"
    },
    "android": {
        "priority": "high",
        "notification": {
            "channel_id": "complaints_channel",
            "sound": "default",
            "color": "#2196F3",
            "icon": "ic_notification"
        }
    }
}
```

---

## 📱 Android Implementation

### 1. Handle FCM Message

```kotlin
class MyFirebaseMessagingService : FirebaseMessagingService() {
    
    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)
        
        val type = remoteMessage.data["type"]
        val title = remoteMessage.notification?.title
        val body = remoteMessage.notification?.body
        
        when (type) {
            "complaint_status_changed" -> handleComplaintStatusChanged(remoteMessage.data)
            "admin_response" -> handleAdminResponse(remoteMessage.data)
            "complaint_resolved" -> handleComplaintResolved(remoteMessage.data)
            "announcement_created" -> handleAnnouncementCreated(remoteMessage.data)
            "comment_added" -> handleCommentAdded(remoteMessage.data)
            else -> showGenericNotification(title, body, remoteMessage.data)
        }
    }
    
    private fun handleComplaintStatusChanged(data: Map<String, String>) {
        val complaintId = data["complaint_id"]?.toIntOrNull()
        val newStatus = data["new_status"]
        val title = data["complaint_title"]
        
        // Create intent to ComplaintDetailActivity
        val intent = Intent(this, ComplaintDetailActivity::class.java).apply {
            putExtra("complaint_id", complaintId)
            putExtra("highlight_status", true)
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        
        val pendingIntent = PendingIntent.getActivity(
            this, complaintId ?: 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        
        val notificationBuilder = NotificationCompat.Builder(this, "complaints_channel")
            .setSmallIcon(R.drawable.ic_notification)
            .setContentTitle("Status Pengaduan Diperbarui")
            .setContentText("$title - Status: ${getStatusText(newStatus)}")
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setColor(getStatusColor(newStatus))
        
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(complaintId ?: 0, notificationBuilder.build())
    }
    
    private fun handleAdminResponse(data: Map<String, String>) {
        val complaintId = data["complaint_id"]?.toIntOrNull()
        val responsePreview = data["response_preview"]
        val adminName = data["admin_name"]
        
        val intent = Intent(this, ComplaintDetailActivity::class.java).apply {
            putExtra("complaint_id", complaintId)
            putExtra("scroll_to_responses", true)
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        
        val pendingIntent = PendingIntent.getActivity(
            this, complaintId ?: 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        
        val notificationBuilder = NotificationCompat.Builder(this, "complaints_channel")
            .setSmallIcon(R.drawable.ic_notification)
            .setContentTitle("Tanggapan Baru dari $adminName")
            .setContentText(responsePreview)
            .setStyle(NotificationCompat.BigTextStyle().bigText(responsePreview))
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
        
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(complaintId ?: 0, notificationBuilder.build())
    }
    
    private fun handleComplaintResolved(data: Map<String, String>) {
        val complaintId = data["complaint_id"]?.toIntOrNull()
        val title = data["complaint_title"]
        val resolutionNote = data["resolution_note"]
        
        // Show rating dialog when user opens notification
        val intent = Intent(this, ComplaintDetailActivity::class.java).apply {
            putExtra("complaint_id", complaintId)
            putExtra("show_rating_dialog", true)
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        
        val pendingIntent = PendingIntent.getActivity(
            this, complaintId ?: 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        
        val notificationBuilder = NotificationCompat.Builder(this, "complaints_channel")
            .setSmallIcon(R.drawable.ic_check)
            .setContentTitle("Pengaduan Diselesaikan")
            .setContentText(title)
            .setStyle(NotificationCompat.BigTextStyle().bigText(resolutionNote))
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setColor(ContextCompat.getColor(this, R.color.green))
        
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(complaintId ?: 0, notificationBuilder.build())
    }
    
    private fun handleAnnouncementCreated(data: Map<String, String>) {
        val announcementId = data["announcement_id"]?.toIntOrNull()
        val title = data["announcement_title"]
        val isUrgent = data["is_urgent"]?.toBoolean() ?: false
        
        val intent = Intent(this, AnnouncementDetailActivity::class.java).apply {
            putExtra("announcement_id", announcementId)
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        
        val pendingIntent = PendingIntent.getActivity(
            this, announcementId ?: 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        
        val notificationBuilder = NotificationCompat.Builder(this, "announcements_channel")
            .setSmallIcon(R.drawable.ic_announcement)
            .setContentTitle(if (isUrgent) "⚠️ Pengumuman Penting" else "Pengumuman Baru")
            .setContentText(title)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(if (isUrgent) NotificationCompat.PRIORITY_MAX else NotificationCompat.PRIORITY_HIGH)
            .setColor(if (isUrgent) ContextCompat.getColor(this, R.color.red) else ContextCompat.getColor(this, R.color.blue))
        
        if (isUrgent) {
            notificationBuilder.setVibrate(longArrayOf(0, 500, 200, 500))
        }
        
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(announcementId ?: 0, notificationBuilder.build())
    }
    
    private fun getStatusText(status: String?): String {
        return when (status) {
            "pending" -> "Menunggu"
            "in_progress" -> "Sedang Diproses"
            "resolved" -> "Selesai"
            "rejected" -> "Ditolak"
            else -> status ?: "Unknown"
        }
    }
    
    private fun getStatusColor(status: String?): Int {
        return when (status) {
            "pending" -> ContextCompat.getColor(this, R.color.yellow)
            "in_progress" -> ContextCompat.getColor(this, R.color.blue)
            "resolved" -> ContextCompat.getColor(this, R.color.green)
            "rejected" -> ContextCompat.getColor(this, R.color.red)
            else -> ContextCompat.getColor(this, R.color.gray)
        }
    }
}
```

### 2. Create Notification Channels

```kotlin
class NotificationChannelManager(private val context: Context) {
    
    fun createNotificationChannels() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val notificationManager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            
            // Complaints Channel
            val complaintsChannel = NotificationChannel(
                "complaints_channel",
                "Pengaduan",
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Notifikasi terkait pengaduan Anda"
                enableLights(true)
                lightColor = Color.BLUE
                enableVibration(true)
            }
            
            // Announcements Channel
            val announcementsChannel = NotificationChannel(
                "announcements_channel",
                "Pengumuman",
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Pengumuman dari admin"
                enableLights(true)
                lightColor = Color.GREEN
            }
            
            // Urgent Announcements Channel
            val urgentChannel = NotificationChannel(
                "urgent_channel",
                "Pengumuman Penting",
                NotificationManager.IMPORTANCE_MAX
            ).apply {
                description = "Pengumuman penting yang memerlukan perhatian segera"
                enableLights(true)
                lightColor = Color.RED
                enableVibration(true)
                vibrationPattern = longArrayOf(0, 500, 200, 500)
            }
            
            notificationManager.createNotificationChannels(
                listOf(complaintsChannel, announcementsChannel, urgentChannel)
            )
        }
    }
}
```

### 3. Initialize in Application Class

```kotlin
class MyApplication : Application() {
    
    override fun onCreate() {
        super.onCreate()
        
        // Create notification channels
        NotificationChannelManager(this).createNotificationChannels()
        
        // Get FCM token
        FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
            if (task.isSuccessful) {
                val token = task.result
                // Send to backend
                sendTokenToServer(token)
            }
        }
    }
    
    private fun sendTokenToServer(token: String) {
        // Send token to backend via API
        // This should be done after user login
    }
}
```

---

## 🎨 UI/UX Recommendations

### Notification Badge
```kotlin
// Update notification badge count
fun updateNotificationBadge(unreadCount: Int) {
    val badgeDrawable = bottomNavigationView.getOrCreateBadge(R.id.nav_notifications)
    if (unreadCount > 0) {
        badgeDrawable.isVisible = true
        badgeDrawable.number = unreadCount
        badgeDrawable.backgroundColor = ContextCompat.getColor(this, R.color.red)
    } else {
        badgeDrawable.isVisible = false
    }
}
```

### In-App Notification
```kotlin
// Show in-app notification using Snackbar
fun showInAppNotification(title: String, body: String, action: () -> Unit) {
    Snackbar.make(binding.root, body, Snackbar.LENGTH_LONG)
        .setAction("Lihat") { action() }
        .setActionTextColor(ContextCompat.getColor(this, R.color.blue))
        .show()
}
```

---

## 📊 Analytics & Tracking

Recommended events to track:
- `notification_received` - When FCM message received
- `notification_opened` - When user taps notification
- `notification_dismissed` - When user dismisses notification
- `notification_action_taken` - When user takes action from notification

```kotlin
// Example using Firebase Analytics
fun logNotificationReceived(type: String) {
    val bundle = Bundle().apply {
        putString("notification_type", type)
        putLong("timestamp", System.currentTimeMillis())
    }
    FirebaseAnalytics.getInstance(this).logEvent("notification_received", bundle)
}
```

---

## 🔒 Permissions (Android 13+)

```kotlin
// Request notification permission for Android 13+
if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
    if (ContextCompat.checkSelfPermission(
            this, 
            Manifest.permission.POST_NOTIFICATIONS
        ) != PackageManager.PERMISSION_GRANTED
    ) {
        ActivityCompat.requestPermissions(
            this,
            arrayOf(Manifest.permission.POST_NOTIFICATIONS),
            NOTIFICATION_PERMISSION_CODE
        )
    }
}
```

---

## 📝 Testing Checklist

- [ ] Receive notification when app is foreground
- [ ] Receive notification when app is background
- [ ] Receive notification when app is killed
- [ ] Notification opens correct screen
- [ ] Notification badge updates correctly
- [ ] Different notification types show different icons/colors
- [ ] Urgent notifications use appropriate priority
- [ ] Sound and vibration work correctly
- [ ] Notification channels properly configured
- [ ] Permission request works on Android 13+

---

**Happy Coding! 🚀**
