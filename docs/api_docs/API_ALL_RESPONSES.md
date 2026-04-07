# API Responses Lengkap

Dokumen ini berisi contoh response untuk seluruh endpoint API aktif berdasarkan collection dan format response saat ini.

Total endpoint: 84

## Template Error Umum

### 401

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403

```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### 404

```json
{
  "success": false,
  "message": "Data Not Found"
}
```

### 422

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field": [
      "The field is required."
    ]
  }
}
```

### 500

```json
{
  "success": false,
  "message": "Server error occurred"
}
```

## Admin - Announcements

### GET /api/admin/announcements

Nama request: Get All Announcements

Success (200):

```json
{
  "success": true,
  "message": "Announcements list loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "title": "Pengumuman",
      "is_active": true
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/announcements

Nama request: Create Announcement

Success (201):

```json
{
  "success": true,
  "message": "Announcement created successfully",
  "data": {
    "id": 1,
    "title": "Pengumuman"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/announcements/{{announcement_id}}

Nama request: Delete Announcement

Success (200):

```json
{
  "success": true,
  "message": "Announcement deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/announcements/{{announcement_id}}

Nama request: Get Announcement Detail

Success (200):

```json
{
  "success": true,
  "message": "Announcement details loaded successfully",
  "data": {
    "id": 1,
    "title": "Pengumuman"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/admin/announcements/{{announcement_id}}

Nama request: Update Announcement

Success (200):

```json
{
  "success": true,
  "message": "Announcement updated successfully",
  "data": {
    "id": 1,
    "title": "Pengumuman Updated"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/announcements/{{announcement_id}}/publish

Nama request: Publish

Success (200):

```json
{
  "success": true,
  "message": "Announcement updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/announcements/{{announcement_id}}/toggle-status

Nama request: Toggle Status

Success (200):

```json
{
  "success": true,
  "message": "Announcement updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/announcements/{{announcement_id}}/toggle-sticky

Nama request: Toggle Sticky

Success (200):

```json
{
  "success": true,
  "message": "Announcement updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/announcements/{{announcement_id}}/unpublish

Nama request: Unpublish

Success (200):

```json
{
  "success": true,
  "message": "Announcement updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Admin - Categories

### GET /api/admin/categories

Nama request: Get All Categories

Success (200):

```json
{
  "success": true,
  "message": "List Category loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "name": "Infrastruktur"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/categories

Nama request: Create Category

Success (201):

```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 1,
    "name": "Infrastruktur"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/categories/{{category_id}}

Nama request: Delete Category

Success (200):

```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/categories/{{category_id}}

Nama request: Get Category Detail

Success (200):

```json
{
  "success": true,
  "message": "Detail Category loaded successfully",
  "data": {
    "id": 1,
    "name": "Infrastruktur"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/admin/categories/{{category_id}}

Nama request: Update Category

Success (200):

```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": 1,
    "name": "Infrastruktur Updated"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/categories/{{category_id}}/toggle-status

Nama request: Toggle Status

Success (200):

```json
{
  "success": true,
  "message": "Category successfully updated",
  "data": {
    "id": 1,
    "is_active": false
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/categories/active

Nama request: Get Active Categories

Success (200):

```json
{
  "success": true,
  "message": "List Category loaded successfully",
  "data": [
    {
      "id": 1,
      "name": "Infrastruktur",
      "is_active": true
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/categories/bulk-action

Nama request: Bulk Action

Success (200):

```json
{
  "success": true,
  "message": "Bulk action applied successfully",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Admin - Complaints

### GET /api/admin/complaints

Nama request: Get All Complaints

Success (200):

```json
{
  "success": true,
  "message": "Complaints list loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "title": "Jalan rusak",
      "status": "pending"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/complaints

Nama request: Create Complaint (Admin)

Success (201):

```json
{
  "success": true,
  "message": "Complaint submitted successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/complaints/{{complaint_id}}

Nama request: Delete Complaint (Admin)

Success (200):

```json
{
  "success": true,
  "message": "Complaint deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/complaints/{{complaint_id}}

Nama request: Get Complaint Detail

Success (200):

```json
{
  "success": true,
  "message": "Complaint details loaded successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/admin/complaints/{{complaint_id}}

Nama request: Update Complaint (Admin)

Success (200):

```json
{
  "success": true,
  "message": "Complaint updated successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak updated",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/complaints/{{complaint_id}}/force-delete

Nama request: Force Delete Complaint

Success (200):

```json
{
  "success": true,
  "message": "Complaint deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/complaints/{{complaint_id}}/resolve

Nama request: Mark as Resolved

Success (200):

```json
{
  "success": true,
  "message": "Complaint marked by admin and waiting for user confirmation",
  "data": {
    "id": 1,
    "status": "resolved_by_admin"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/complaints/{{complaint_id}}/response

Nama request: Add Response

Success (201):

```json
{
  "success": true,
  "message": "Response added successfully",
  "data": {
    "id": 1,
    "response": "Sedang diproses"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/complaints/{{complaint_id}}/restore

Nama request: Restore Complaint

Success (200):

```json
{
  "success": true,
  "message": "Complaint restored successfully",
  "data": {
    "id": 1,
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/complaints/{{complaint_id}}/status

Nama request: Update Status

Success (200):

```json
{
  "success": true,
  "message": "Complaint status updated successfully",
  "data": {
    "id": 1,
    "status": "in_progress"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/complaints/attachments/{{attachment_id}}

Nama request: Delete Attachment

Success (200):

```json
{
  "success": true,
  "message": "Complaint deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/complaints/bulk-update

Nama request: Bulk Update

Success (200):

```json
{
  "success": true,
  "message": "Bulk action applied successfully",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/complaints/statistics

Nama request: Get Statistics

Success (200):

```json
{
  "success": true,
  "message": "Statistics loaded successfully",
  "data": {
    "total": 10,
    "pending": 3,
    "in_progress": 2,
    "resolved": 5
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/complaints/trashed

Nama request: Get Trashed Complaints

Success (200):

```json
{
  "success": true,
  "message": "Complaint details loaded successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Admin - Dashboard

### GET /api/admin/dashboard

Nama request: Get Admin Dashboard

Success (200):

```json
{
  "success": true,
  "message": "Dashboard data loaded successfully",
  "data": {
    "summary": {
      "total": 10,
      "pending": 3,
      "resolved": 5
    }
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/dashboard/quick-stats

Nama request: Get Quick Stats (Mobile)

Success (200):

```json
{
  "success": true,
  "message": "Dashboard data loaded successfully",
  "data": {
    "summary": {
      "total": 10,
      "pending": 3,
      "resolved": 5
    }
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Admin - Reports

### GET /api/admin/reports/complaints

Nama request: Get Complaints Report

Success (200):

```json
{
  "success": true,
  "message": "Report loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "name": "Sample"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/reports/overview

Nama request: Get Reports Overview

Success (200):

```json
{
  "success": true,
  "message": "Report overview loaded successfully",
  "data": {
    "complaints": {
      "total": 100
    },
    "users": {
      "total": 50
    }
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/reports/users

Nama request: Get Users Report

Success (200):

```json
{
  "success": true,
  "message": "Report loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "name": "Sample"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Admin - Users

### GET /api/admin/users

Nama request: Get All Users

Success (200):

```json
{
  "success": true,
  "message": "Users list loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "name": "Admin",
      "role": "admin"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/admin/users

Nama request: Create User

Success (201):

```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 2,
    "name": "User Baru",
    "role": "user"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/admin/users/{{user_id}}

Nama request: Delete User

Success (200):

```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/admin/users/{{user_id}}

Nama request: Get User Detail

Success (200):

```json
{
  "success": true,
  "message": "User details loaded successfully",
  "data": {
    "id": 1,
    "name": "Admin",
    "role": "admin"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/admin/users/{{user_id}}

Nama request: Update User

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "Admin Updated",
    "role": "admin"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/change-role

Nama request: Change Role

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/reject-verification

Nama request: Reject Verification

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/reset-password

Nama request: Reset Password

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/unverify-email

Nama request: Unverify Email

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/verify-email

Nama request: Verify Email

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PATCH /api/admin/users/{{user_id}}/verify-user

Nama request: Verify User Identity

Success (200):

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## Authentication

### PUT /api/auth/change-password

Nama request: Change Password

Success (200):

```json
{
  "success": true,
  "message": "Password changed successfully",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/auth/login

Nama request: Login

Success (200):

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|example-token"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/auth/logout

Nama request: Logout

Success (200):

```json
{
  "success": true,
  "message": "Logged out successfully",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/auth/logout-all

Nama request: Logout All Devices

Success (200):

```json
{
  "success": true,
  "message": "Logged out from all devices successfully",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/auth/profile

Nama request: Get Profile

Success (200):

```json
{
  "success": true,
  "message": "Profile loaded successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/auth/profile

Nama request: Update Profile

Success (200):

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Doe Updated",
    "email": "john@example.com"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/auth/register

Nama request: Register

Success (201):

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|example-token"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## System

### GET /api

Nama request: API Info

Success (200):

```json
{
  "success": true,
  "message": "API MyPengaduan",
  "data": {
    "version": "1.0.0",
    "documentation": "/api/docs"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Announcements

### GET /api/announcements

Nama request: Get Announcements

Success (200):

```json
{
  "success": true,
  "message": "Announcements list loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "title": "Pengumuman"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/announcements/{{announcement_id}}

Nama request: Get Announcement Detail

Success (200):

```json
{
  "success": true,
  "message": "Announcement details loaded successfully",
  "data": {
    "id": 1,
    "title": "Pengumuman"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/announcements/{{announcement_id}}/bookmark

Nama request: Toggle Bookmark

Success (200):

```json
{
  "success": true,
  "message": "Bookmark toggled successfully",
  "data": {
    "announcement_id": 1,
    "is_bookmarked": true
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/announcements/{{announcement_id}}/comments

Nama request: Get Comments

Success (200):

```json
{
  "success": true,
  "message": "Comments loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "content": "Komentar contoh"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/announcements/{{announcement_id}}/comments

Nama request: Add Comment

Success (201):

```json
{
  "success": true,
  "message": "Comment added successfully",
  "data": {
    "id": 1,
    "content": "Komentar contoh"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/announcements/{{announcement_id}}/comments/{{comment_id}}

Nama request: Delete Comment

Success (200):

```json
{
  "success": true,
  "message": "Comment deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/announcements/bookmarked

Nama request: Get Bookmarked Announcements

Success (200):

```json
{
  "success": true,
  "message": "Bookmarked announcements loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "title": "Pengumuman"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/announcements/latest

Nama request: Get Latest Announcements

Success (200):

```json
{
  "success": true,
  "message": "Announcements loaded successfully",
  "data": [
    {
      "id": 1,
      "title": "Pengumuman"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/announcements/urgent

Nama request: Get Urgent Announcements

Success (200):

```json
{
  "success": true,
  "message": "Announcements loaded successfully",
  "data": [
    {
      "id": 1,
      "title": "Pengumuman"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Categories

### GET /api/complaints/categories

Nama request: Get Active Categories

Success (200):

```json
{
  "success": true,
  "message": "Categories list loaded successfully",
  "data": [
    {
      "id": 1,
      "name": "Infrastruktur"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Complaints

### GET /api/complaints

Nama request: Get My Complaints

Success (200):

```json
{
  "success": true,
  "message": "Complaints list loaded successfully",
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  },
  "data": [
    {
      "id": 1,
      "title": "Jalan rusak",
      "status": "pending"
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/complaints

Nama request: Create Complaint

Success (201):

```json
{
  "success": true,
  "message": "Complaint submitted successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/complaints/{{complaint_id}}

Nama request: Delete Complaint

Success (200):

```json
{
  "success": true,
  "message": "Complaint deleted successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/complaints/{{complaint_id}}

Nama request: Get Complaint Detail

Success (200):

```json
{
  "success": true,
  "message": "Complaint details loaded successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/complaints/{{complaint_id}}

Nama request: Update Complaint

Success (200):

```json
{
  "success": true,
  "message": "Complaint updated successfully",
  "data": {
    "id": 1,
    "title": "Jalan rusak updated",
    "status": "pending"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/complaints/{{complaint_id}}/confirm-resolution

Nama request: Confirm Resolution

Success (200):

```json
{
  "success": true,
  "message": "Complaint resolution confirmed successfully",
  "data": {
    "id": 1,
    "status": "resolved"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/complaints/{{complaint_id}}/responses

Nama request: Add Complaint Response

Success (201):

```json
{
  "success": true,
  "message": "Response added successfully",
  "data": {
    "id": 1,
    "content": "Tanggapan contoh"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/complaints/{{complaint_id}}/track

Nama request: Track Complaint

Success (200):

```json
{
  "success": true,
  "message": "Complaint tracking loaded successfully",
  "data": {
    "complaint": {
      "id": 1,
      "status": "in_progress"
    },
    "timeline": [
      {
        "status": "pending"
      }
    ]
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/complaints/statistics

Nama request: Get My Statistics

Success (200):

```json
{
  "success": true,
  "message": "Statistics loaded successfully",
  "data": {
    "total": 10,
    "pending": 3,
    "in_progress": 2,
    "resolved": 5
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Dashboard

### GET /api/dashboard

Nama request: Get User Dashboard

Success (200):

```json
{
  "success": true,
  "message": "Dashboard data loaded successfully",
  "data": {
    "summary": {
      "total": 10,
      "pending": 3,
      "resolved": 5
    }
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/user

Nama request: Get Current User

Success (200):

```json
{
  "success": true,
  "message": "User loaded successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Device Tokens

### GET /api/device-tokens

Nama request: Get Device Tokens

Success (200):

```json
{
  "success": true,
  "message": "Devices list loaded successfully",
  "data": [
    {
      "id": 1,
      "device_type": "android",
      "is_active": true
    }
  ]
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/device-tokens

Nama request: Register Device Token

Success (201):

```json
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 1,
    "device_token": "fcm_device_token_example",
    "is_active": true
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### DELETE /api/device-tokens/{{device_token_id}}

Nama request: Delete Device Token

Success (200):

```json
{
  "success": true,
  "message": "Device removed successfully"
}
```

Kemungkinan error: 401, 403, 404, 422, 500

## User - Notifications

### GET /api/notification-settings

Nama request: Get Notification Settings

Success (200):

```json
{
  "success": true,
  "message": "Notification settings loaded successfully",
  "data": {
    "push_enabled": true,
    "complaint_created": true
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### PUT /api/notification-settings

Nama request: Update Notification Settings

Success (200):

```json
{
  "success": true,
  "message": "Notification settings updated successfully",
  "data": {
    "push_enabled": true,
    "complaint_created": true
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### GET /api/notifications

Nama request: Get Notifications

Success (200):

```json
{
  "success": true,
  "message": "Data loaded successfully",
  "data": {
    "id": 1
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/notifications/{{notification_id}}/read

Nama request: Mark Notification as Read

Success (200):

```json
{
  "success": true,
  "message": "Notification marked as read",
  "data": {
    "id": 1,
    "is_read": true
  }
}
```

Kemungkinan error: 401, 403, 404, 422, 500

### POST /api/notifications/read-all

Nama request: Mark All Notifications as Read

Success (200):

```json
{
  "success": true,
  "message": "All notifications marked as read",
  "data": null
}
```

Kemungkinan error: 401, 403, 404, 422, 500


