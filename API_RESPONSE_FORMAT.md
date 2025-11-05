# 📊 API Response Format - Standardization Guide

## Overview
Semua API endpoint sekarang menggunakan format response yang konsisten dan terstruktur dengan helper trait `ApiResponse`.

## Format Response

### 1. Success Response dengan Pagination
```json
{
    "success": true,
    "message": "List kategori berhasil dimuat",
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 7,
        "last_page": 1,
        "next_page": null,
        "prev_page": null
    },
    "data": [
        {
            "id": 1,
            "name": "Infrastruktur",
            "description": "Laporan terkait jalan, jembatan, drainase, dan infrastruktur umum",
            "icon": "hammer",
            "color": "blue",
            "is_active": true,
            "complaints_count": 4
        }
    ]
}
```

### 2. Success Response tanpa Pagination
```json
{
    "success": true,
    "message": "Detail kategori berhasil dimuat",
    "data": {
        "id": 1,
        "name": "Infrastruktur",
        "description": "Laporan terkait jalan, jembatan, drainase",
        "icon": "hammer",
        "color": "blue",
        "is_active": true,
        "complaints_count": 4
    }
}
```

### 3. Success Response untuk Create (201)
```json
{
    "success": true,
    "message": "Kategori berhasil dibuat",
    "data": {
        "id": 8,
        "name": "Kebersihan",
        "slug": "kebersihan",
        "icon": "trash",
        "color": "green",
        "is_active": true
    }
}
```

### 4. Success Response untuk Delete
```json
{
    "success": true,
    "message": "Kategori berhasil dihapus"
}
```

### 5. Validation Error Response (422)
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "description": [
            "The description field must be a string."
        ]
    }
}
```

### 6. Not Found Error Response (404)
```json
{
    "success": false,
    "message": "Kategori tidak ditemukan"
}
```

### 7. Server Error Response (500)
```json
{
    "success": false,
    "message": "Terjadi kesalahan pada server",
    "errors": {
        "message": "SQLSTATE[42703]: Undefined column...",
        "file": "/path/to/file.php",
        "line": 123
    }
}
```

## ApiResponse Trait

### Location
`app/Traits/ApiResponse.php`

### Available Methods

#### 1. successWithPagination()
Untuk response dengan pagination (Laravel Paginator).

```php
return $this->successWithPagination($categories, 'List kategori berhasil dimuat');
```

**Parameters:**
- `$data` (LengthAwarePaginator) - Data dengan pagination
- `$message` (string) - Pesan sukses (default: "Data berhasil dimuat")
- `$statusCode` (int) - HTTP status code (default: 200)

#### 2. success()
Untuk response sukses tanpa pagination.

```php
return $this->success($category, 'Detail kategori berhasil dimuat');
```

**Parameters:**
- `$data` (mixed) - Data response
- `$message` (string) - Pesan sukses (default: "Operasi berhasil")
- `$statusCode` (int) - HTTP status code (default: 200)

#### 3. created()
Untuk response create resource (201).

```php
return $this->created($category, 'Kategori berhasil dibuat');
```

**Parameters:**
- `$data` (mixed) - Data resource yang dibuat
- `$message` (string) - Pesan sukses (default: "Data berhasil dibuat")

#### 4. deleted()
Untuk response delete resource.

```php
return $this->deleted('Kategori berhasil dihapus');
```

**Parameters:**
- `$message` (string) - Pesan sukses (default: "Data berhasil dihapus")

#### 5. error()
Untuk response error umum.

```php
return $this->error('Tidak dapat menghapus kategori yang memiliki pengaduan');
```

**Parameters:**
- `$message` (string) - Pesan error
- `$errors` (mixed) - Detail error (optional)
- `$statusCode` (int) - HTTP status code (default: 400)

#### 6. validationError()
Untuk validation error (422).

```php
if ($validator->fails()) {
    return $this->validationError($validator->errors());
}
```

**Parameters:**
- `$errors` (mixed) - Validation errors
- `$message` (string) - Pesan error (default: "Validasi gagal")

#### 7. notFound()
Untuk resource not found (404).

```php
return $this->notFound('Kategori tidak ditemukan');
```

**Parameters:**
- `$message` (string) - Pesan error (default: "Data tidak ditemukan")

#### 8. unauthorized()
Untuk unauthorized access (403).

```php
return $this->unauthorized('Tidak memiliki akses');
```

**Parameters:**
- `$message` (string) - Pesan error (default: "Tidak memiliki akses")

#### 9. serverError()
Untuk server error (500).

```php
return $this->serverError('Gagal memuat data', $e);
```

**Parameters:**
- `$message` (string) - Pesan error (default: "Terjadi kesalahan pada server")
- `$error` (Exception|mixed) - Exception atau error detail (optional)

## Implementation Example

### CategoryController (Updated)

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $query = Category::withCount('complaints');
            
            // Filters...
            
            if ($request->boolean('all')) {
                $categories = $query->get();
                return $this->success($categories, 'List kategori berhasil dimuat');
            } else {
                $perPage = $request->get('per_page', 15);
                $categories = $query->paginate($perPage);
                return $this->successWithPagination($categories, 'List kategori berhasil dimuat');
            }
        } catch (\Exception $e) {
            return $this->serverError('Gagal memuat list kategori', $e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [...]);
            
            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }
            
            $category = Category::create($data);
            
            return $this->created($category, 'Kategori berhasil dibuat');
        } catch (\Exception $e) {
            return $this->serverError('Gagal membuat kategori', $e);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->success($category, 'Detail kategori berhasil dimuat');
        } catch (\Exception $e) {
            return $this->notFound('Kategori tidak ditemukan');
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            if ($category->complaints()->count() > 0) {
                return $this->error('Tidak dapat menghapus kategori yang memiliki pengaduan');
            }
            
            $category->delete();
            
            return $this->deleted('Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return $this->notFound('Kategori tidak ditemukan');
        }
    }
}
```

## Migration Checklist

### ✅ Controllers Updated
- [x] `App\Http\Controllers\Api\Admin\CategoryController` - DONE

### 🔄 Controllers to Update
- [ ] `App\Http\Controllers\Api\Admin\DashboardController`
- [ ] `App\Http\Controllers\Api\Admin\ComplaintController`
- [ ] `App\Http\Controllers\Api\Admin\UserController`
- [ ] `App\Http\Controllers\Api\Admin\AnnouncementController`
- [ ] `App\Http\Controllers\Api\AuthController`
- [ ] `App\Http\Controllers\Api\ComplaintController`
- [ ] `App\Http\Controllers\Api\AnnouncementController`
- [ ] `App\Http\Controllers\Api\DeviceTokenController`
- [ ] `App\Http\Controllers\Api\NotificationController`

## Benefits

### 1. Consistency
- Semua endpoint menggunakan format response yang sama
- Mudah di-consume oleh frontend/mobile app
- Predictable error handling

### 2. Maintainability
- Centralized response logic
- Mudah mengubah format response di satu tempat
- Reduce code duplication

### 3. Developer Experience
- Clear error messages dalam Bahasa Indonesia
- Structured validation errors
- Debug information di development mode

### 4. Client-Side Handling
Frontend/Mobile dapat handle response dengan pattern:

```javascript
// Success handling
if (response.success) {
    // Dengan pagination
    if (response.meta) {
        const data = response.data;
        const currentPage = response.meta.current_page;
        const total = response.meta.total;
    } else {
        // Tanpa pagination
        const data = response.data;
    }
    
    showSuccessMessage(response.message);
}

// Error handling
else {
    if (response.errors) {
        // Validation errors
        showValidationErrors(response.errors);
    } else {
        // General error
        showErrorMessage(response.message);
    }
}
```

## Best Practices

### 1. Gunakan Method yang Tepat
```php
// ✅ Good
return $this->created($user, 'User berhasil dibuat');

// ❌ Bad
return $this->success($user, 'User berhasil dibuat', 201);
```

### 2. Consistent Messages dalam Bahasa Indonesia
```php
// ✅ Good
return $this->success($data, 'Data berhasil dimuat');
return $this->error('Tidak dapat menghapus data');

// ❌ Bad
return $this->success($data, 'Data successfully loaded');
return $this->error('Cannot delete data');
```

### 3. Handle Exceptions Properly
```php
// ✅ Good
try {
    $model = Model::findOrFail($id);
    return $this->success($model);
} catch (\Exception $e) {
    return $this->notFound('Data tidak ditemukan');
}

// ❌ Bad
try {
    $model = Model::findOrFail($id);
    return $this->success($model);
} catch (\Exception $e) {
    return $this->serverError('Error', $e); // Too generic
}
```

### 4. Validate Before Processing
```php
// ✅ Good
if ($validator->fails()) {
    return $this->validationError($validator->errors());
}

// Process data...

// ❌ Bad
if ($validator->fails()) {
    return response()->json(['error' => $validator->errors()], 400);
}
```

## Testing Response Format

### Postman Tests
```javascript
pm.test("Response has correct structure", function () {
    var jsonData = pm.response.json();
    
    pm.expect(jsonData).to.have.property('success');
    pm.expect(jsonData).to.have.property('message');
    
    if (jsonData.success) {
        pm.expect(jsonData).to.have.property('data');
    }
});

pm.test("Pagination structure is correct", function () {
    var jsonData = pm.response.json();
    
    if (jsonData.meta) {
        pm.expect(jsonData.meta).to.have.property('current_page');
        pm.expect(jsonData.meta).to.have.property('per_page');
        pm.expect(jsonData.meta).to.have.property('total');
        pm.expect(jsonData.meta).to.have.property('last_page');
    }
});
```

---

**Created**: October 30, 2025  
**Status**: ✅ CategoryController Updated  
**Next**: Update remaining controllers to use ApiResponse trait
