# Daftar Tabel Database (Dari Migration Aktif)

Dokumen ini merangkum semua tabel dari migration aktif untuk membantu pembuatan class diagram dan ERD.

## Ringkasan

Total tabel: 26

## Daftar Tabel

### A. Tabel Inti Aplikasi

1. users
2. categories
3. complaints
4. announcements
5. responses
6. comments
7. attachments
8. announcement_bookmarks
9. user_devices
10. notification_settings
11. fcm_notifications
12. app_settings

### B. Tabel Auth, Session, Token

13. password_reset_tokens
14. sessions
15. personal_access_tokens

### C. Tabel Role & Permission (Spatie)

16. roles
17. permissions
18. role_has_permissions
19. model_has_roles
20. model_has_permissions

### D. Tabel Queue, Cache, System

21. jobs
22. job_batches
23. failed_jobs
24. cache
25. cache_locks
26. activity_log

Catatan: nama tabel activity_log mengikuti config('activitylog.table_name'). Jika config package belum dipublish, biasanya default-nya activity_log.

## Relasi Foreign Key

1. complaints.user_id -> users.id
2. complaints.category_id -> categories.id
3. announcements.author_id -> users.id
4. responses.complaint_id -> complaints.id
5. responses.user_id -> users.id
6. comments.user_id -> users.id
7. comments.parent_id -> comments.id (self reference)
8. user_devices.user_id -> users.id
9. notification_settings.user_id -> users.id (juga unique)
10. fcm_notifications.user_id -> users.id
11. announcement_bookmarks.user_id -> users.id
12. announcement_bookmarks.announcement_id -> announcements.id
13. role_has_permissions.role_id -> roles.id
14. role_has_permissions.permission_id -> permissions.id
15. model_has_roles.role_id -> roles.id
16. model_has_permissions.permission_id -> permissions.id

## Relasi Polymorphic

1. attachments: attachable_id + attachable_type
2. comments: commentable_id + commentable_type
3. personal_access_tokens: tokenable_id + tokenable_type
4. activity_log: subject_id + subject_type
5. activity_log: causer_id + causer_type

## Catatan Penting Untuk ERD

1. sessions.user_id adalah foreignId nullable + index, tetapi tidak dibuat constraint FK di migration.
2. model_has_roles dan model_has_permissions memakai model_type + model_id (polymorphic authorization mapping).
3. role_has_permissions, model_has_roles, model_has_permissions adalah tabel pivot.
4. complaints dan announcements menggunakan soft deletes (deleted_at).

## Detail Kolom Per Tabel

### users

1. id: bigint unsigned (PK)
2. name: varchar
3. email: varchar (unique)
4. nik: varchar(16), nullable
5. ktp_path: varchar, nullable
6. email_verified_at: timestamp, nullable
7. password: varchar
8. role: enum(admin, user), default user
9. address: text, nullable
10. phone: varchar(20), nullable
11. rt_number: varchar(3), nullable
12. rw_number: varchar(3), nullable
13. is_verified: boolean, default false
14. verified_at: timestamp, nullable
15. is_active: boolean, default true
16. remember_token: varchar(100), nullable
17. notification_preferences: json, nullable
18. created_at: timestamp, nullable
19. updated_at: timestamp, nullable

### categories

1. id: bigint unsigned (PK)
2. name: varchar
3. description: text, nullable
4. icon: varchar, nullable
5. color: varchar, default blue
6. is_active: boolean, default true
7. created_at: timestamp, nullable
8. updated_at: timestamp, nullable

### complaints

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. title: varchar
4. description: text
5. category_id: bigint unsigned (FK -> categories.id)
6. photo: varchar, nullable
7. location: varchar
8. status: enum(pending, in_progress, resolved, rejected), default pending
9. priority: enum(low, medium, high, urgent), default medium
10. response: text, nullable
11. admin_response: text, nullable
12. estimated_resolution: date, nullable
13. report_date: date
14. created_at: timestamp, nullable
15. updated_at: timestamp, nullable
16. deleted_at: timestamp, nullable (soft delete)

### announcements

1. id: bigint unsigned (PK)
2. title: varchar
3. slug: varchar (unique)
4. summary: text, nullable
5. content: text
6. priority: enum(urgent, high, medium, low), default medium
7. target_audience: json, nullable
8. attachments: json, nullable
9. is_active: boolean, default true
10. is_sticky: boolean, default false
11. allow_comments: boolean, default true
12. published_at: timestamp, nullable
13. views_count: unsigned integer, default 0
14. author_id: bigint unsigned (FK -> users.id)
15. created_at: timestamp, nullable
16. updated_at: timestamp, nullable
17. deleted_at: timestamp, nullable (soft delete)

### responses

1. id: bigint unsigned (PK)
2. complaint_id: bigint unsigned (FK -> complaints.id)
3. user_id: bigint unsigned (FK -> users.id)
4. content: text
5. photo: varchar, nullable
6. created_at: timestamp, nullable
7. updated_at: timestamp, nullable

### comments

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. commentable_id: bigint unsigned
4. commentable_type: varchar
5. content: text
6. is_approved: boolean, default true
7. parent_id: bigint unsigned, nullable (FK -> comments.id)
8. deleted_at: timestamp, nullable (soft delete)
9. created_at: timestamp, nullable
10. updated_at: timestamp, nullable

### attachments

1. id: bigint unsigned (PK)
2. attachable_id: bigint unsigned
3. attachable_type: varchar
4. file_name: varchar
5. file_path: varchar
6. file_size: integer
7. mime_type: varchar
8. attachment_type: varchar, default complaint
9. created_at: timestamp, nullable
10. updated_at: timestamp, nullable

### announcement_bookmarks

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. announcement_id: bigint unsigned (FK -> announcements.id)
4. created_at: timestamp, nullable
5. updated_at: timestamp, nullable
6. unique: (user_id, announcement_id)

### user_devices

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. device_token: varchar(500), unique
4. device_type: enum(android, ios), default android
5. device_model: varchar, nullable
6. os_version: varchar, nullable
7. app_version: varchar, nullable
8. is_active: boolean, default true
9. last_used_at: timestamp, nullable
10. created_at: timestamp, nullable
11. updated_at: timestamp, nullable
12. index: (user_id, is_active)

### notification_settings

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. complaint_created: boolean, default true
4. complaint_status_changed: boolean, default true
5. announcement_created: boolean, default true
6. admin_response: boolean, default true
7. comment_added: boolean, default true
8. push_enabled: boolean, default true
9. created_at: timestamp, nullable
10. updated_at: timestamp, nullable
11. unique: user_id

### fcm_notifications

1. id: bigint unsigned (PK)
2. user_id: bigint unsigned (FK -> users.id)
3. type: varchar
4. title: varchar
5. body: text
6. data: json, nullable
7. is_read: boolean, default false
8. read_at: timestamp, nullable
9. created_at: timestamp, nullable
10. updated_at: timestamp, nullable
11. index: (user_id, is_read)
12. index: (user_id, created_at)

### app_settings

1. id: bigint unsigned (PK)
2. key: varchar, unique
3. value: text, nullable
4. created_at: timestamp, nullable
5. updated_at: timestamp, nullable

### password_reset_tokens

1. email: varchar (PK)
2. token: varchar
3. created_at: timestamp, nullable

### sessions

1. id: varchar (PK)
2. user_id: bigint unsigned, nullable (indexed, tanpa FK constraint)
3. ip_address: varchar(45), nullable
4. user_agent: text, nullable
5. payload: longText
6. last_activity: integer (indexed)

### personal_access_tokens

1. id: bigint unsigned (PK)
2. tokenable_id: bigint unsigned
3. tokenable_type: varchar
4. name: text
5. token: varchar(64), unique
6. abilities: text, nullable
7. last_used_at: timestamp, nullable
8. expires_at: timestamp, nullable (indexed)
9. created_at: timestamp, nullable
10. updated_at: timestamp, nullable

### roles

1. id: bigint unsigned (PK)
2. name: varchar
3. guard_name: varchar
4. created_at: timestamp, nullable
5. updated_at: timestamp, nullable
6. unique: (name, guard_name)

### permissions

1. id: bigint unsigned (PK)
2. name: varchar
3. guard_name: varchar
4. created_at: timestamp, nullable
5. updated_at: timestamp, nullable
6. unique: (name, guard_name)

### role_has_permissions

1. permission_id: bigint unsigned (FK -> permissions.id)
2. role_id: bigint unsigned (FK -> roles.id)
3. primary key: (permission_id, role_id)

### model_has_roles

1. role_id: bigint unsigned (FK -> roles.id)
2. model_type: varchar
3. model_id: bigint unsigned
4. primary key: (role_id, model_id, model_type)
5. index: (model_id, model_type)

### model_has_permissions

1. permission_id: bigint unsigned (FK -> permissions.id)
2. model_type: varchar
3. model_id: bigint unsigned
4. primary key: (permission_id, model_id, model_type)
5. index: (model_id, model_type)

### jobs

1. id: bigint unsigned (PK)
2. queue: varchar (indexed)
3. payload: longText
4. attempts: tinyint unsigned
5. reserved_at: unsigned integer, nullable
6. available_at: unsigned integer
7. created_at: unsigned integer

### job_batches

1. id: varchar (PK)
2. name: varchar
3. total_jobs: integer
4. pending_jobs: integer
5. failed_jobs: integer
6. failed_job_ids: longText
7. options: mediumText, nullable
8. cancelled_at: integer, nullable
9. created_at: integer
10. finished_at: integer, nullable

### failed_jobs

1. id: bigint unsigned (PK)
2. uuid: varchar, unique
3. connection: text
4. queue: text
5. payload: longText
6. exception: longText
7. failed_at: timestamp, default current timestamp

### cache

1. key: varchar (PK)
2. value: mediumText
3. expiration: integer

### cache_locks

1. key: varchar (PK)
2. owner: varchar
3. expiration: integer

### activity_log

1. id: bigint unsigned (PK)
2. log_name: varchar, nullable
3. description: text
4. subject_type: varchar, nullable
5. subject_id: bigint unsigned, nullable
6. event: varchar, nullable
7. causer_type: varchar, nullable
8. causer_id: bigint unsigned, nullable
9. properties: json, nullable
10. batch_uuid: uuid, nullable
11. created_at: timestamp, nullable
12. updated_at: timestamp, nullable
13. index: log_name

## Perbedaan Singkat (Sesuai Kebutuhan)

1. Class Diagram
Fokus: struktur program (OOP / sistem aplikasi)
Isi: class, attribute, method (fungsi)

2. ERD (Entity Relationship Diagram)
Fokus: struktur database
Isi: entity (tabel), attribute (kolom), relasi (foreign key)

Catatan: dokumen ini sekarang difokuskan untuk ERD/database saja (tabel, kolom, dan relasi), tanpa kode diagram.

## Daftar Method Model (Untuk Class Diagram)

Bagian ini hanya untuk kebutuhan Class Diagram (struktur class, attribute, method).

### User

1. getAvatarUrlAttribute()
2. getKtpUrlAttribute()
3. getActivitylogOptions()
4. complaints()
5. announcements()
6. responses()
7. comments()
8. devices()
9. notificationSettings()
10. fcmNotifications()
11. bookmarkedAnnouncements()
12. getActiveDeviceTokens()
13. isAdmin()
14. isUser()

### Complaint

1. getPhotoUrlAttribute()
2. user()
3. category()
4. responses()
5. attachments()
6. complaintAttachments()
7. resolutionAttachments()
8. getStatusBadgeColor()

### Announcement

1. getRouteKeyName()
2. author()
3. creator()
4. comments()
5. allComments()
6. bookmarkedBy()
7. getCommentsCountAttribute()
8. scopePublished($query)
9. scopeActive($query)
10. scopePriority($query, $priority)
11. scopeSticky($query)
12. getStatusAttribute()
13. getAttachmentUrl($path)

### Category

1. complaints()
2. scopeActive($query)
3. getComplaintsCountAttribute()
4. getIconClassAttribute()

### Response

1. getPhotoUrlAttribute()
2. complaint()
3. user()
4. attachments()

### Comment

1. commentable()
2. user()
3. parent()
4. replies()
5. scopeApproved($query)
6. scopeParent($query)

### Attachment

1. attachable()
2. getFileSizeHumanAttribute()
3. isImage()
4. isPdf()
5. getFileUrlAttribute()

### NotificationSetting

1. user()

### FcmNotification

1. user()
2. markAsRead()
3. scopeUnread($query)
4. scopeRead($query)

### UserDevice

1. user()
2. scopeActive($query)
3. scopeAndroid($query)
4. scopeIos($query)

### AppSetting

1. getValue(string $key, ?string $default = null)
2. setValue(string $key, ?string $value)

Catatan penting:
1. Untuk Class Diagram, biasanya method `scope...`, accessor `get...Attribute`, dan relasi (`belongsTo`, `hasMany`, `morph...`) tetap ditampilkan karena ini bagian perilaku model.
2. Method internal framework seperti `casts()` tidak wajib ditampilkan jika kamu mau diagram yang lebih ringkas.
