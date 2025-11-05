# 🔧 QUICK FIXES - ADMIN PANEL & API

## Latest Fix: Database Column Issue (Oct 27, 2025) ✅

### Fix: ticket_number Column Error
**Affected Endpoints:**
- `GET /api/admin/dashboard`
- `GET /api/admin/complaints` (search)

**Error:**
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "ticket_number" does not exist
```

**Files Modified:**
1. `app/Http/Controllers/Api/Admin/DashboardController.php`
   - Removed `ticket_number` from select query (Line ~51)
   - Fixed PostgreSQL syntax: `MONTH()` → `EXTRACT(MONTH FROM ...)` (Line ~67)

2. `app/Http/Controllers/Api/Admin/ComplaintController.php`
   - Removed `ticket_number` from search query (Line ~37-40)

**Root Cause:**
Tabel `complaints` tidak memiliki kolom `ticket_number`. Controller mencoba SELECT kolom yang tidak ada.

**Solution:**
```php
// BEFORE ❌
->get(['id', 'ticket_number', 'title', ...])

// AFTER ✅
->get(['id', 'title', 'status', 'user_id', ...])
```

**Status:** ✅ Fixed
**Test:** `curl GET http://localhost/api/admin/dashboard -H "Authorization: Bearer TOKEN"`

---

## Priority: HIGH | Estimated Time: 1 hour

### 1. Fix Auth Helper Warning ✅
**File:** `app/Http/Controllers/Admin/AnnouncementController.php`
**Line:** 105

**Before:**
```php
$validated['author_id'] = auth()->id();
```

**After:**
```php
$validated['author_id'] = Auth::id();
```

---

### 2. Fix Grid + Hidden CSS Conflict ✅
**File:** `resources/views/admin/announcements/create.blade.php`
**Line:** 329

**Before:**
```html
<div id="attachments-preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden"></div>
```

**After:**
```html
<div id="attachments-preview" class="mt-4 hidden"></div>
```

**JavaScript Update:**
```javascript
function renderAttachmentPreview() {
    const container = document.getElementById('attachments-preview');
    container.innerHTML = '';

    if (attachmentFiles.length === 0) {
        container.classList.add('hidden');
        container.classList.remove('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'gap-4');
        return;
    }

    container.classList.remove('hidden');
    container.classList.add('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'gap-4');
    
    // ... rest of code
}
```

**Apply same fix to:**
- `resources/views/admin/complaints/show.blade.php` (line 473)

---

### 3. Fix Conditional CSS Classes ✅

#### File: `resources/views/admin/announcements/index.blade.php`

**Before (Lines 192-193):**
```blade
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
    @if($announcement->is_active) bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
    @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
```

**After:**
```blade
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $announcement->is_active 
    ? 'bg-green-50 text-green-700 ring-green-600/20' 
    : 'bg-red-50 text-red-700 ring-red-600/10' }}">
```

**Before (Lines 198-201):**
```blade
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
    @if($announcement->priority === 'low') bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20
    @elseif($announcement->priority === 'medium') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
    @elseif($announcement->priority === 'high') bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20
    @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
```

**After:**
```blade
@php
$priorityClasses = [
    'low' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    'medium' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'high' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
    'urgent' => 'bg-red-50 text-red-700 ring-red-600/10',
];
@endphp
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityClasses[$announcement->priority] ?? $priorityClasses['low'] }}">
```

#### File: `resources/views/admin/announcements/show.blade.php` (Lines 349-351)
**Apply same fix as above**

#### File: `resources/views/admin/complaints/index.blade.php`

**Status Badge (Lines 133-136):**
```blade
@php
$statusClasses = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-blue-100 text-blue-800',
    'resolved' => 'bg-green-100 text-green-800',
    'rejected' => 'bg-red-100 text-red-800',
];
@endphp
<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$complaint->status] ?? 'bg-gray-100 text-gray-800' }}">
```

**Status Badge (Lines 211-214):**
```blade
@php
$statusClasses = [
    'pending' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
    'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
    'resolved' => 'bg-green-50 text-green-700 ring-green-600/20',
    'rejected' => 'bg-red-50 text-red-700 ring-red-600/10',
];
@endphp
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$complaint->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
```

#### File: `resources/views/admin/complaints/show.blade.php`

**Status Badge (Lines 273-276):**
```blade
@php
$statusClasses = [
    'pending' => 'bg-yellow-50 text-yellow-800',
    'in_progress' => 'bg-blue-50 text-blue-700',
    'resolved' => 'bg-green-50 text-green-700',
    'rejected' => 'bg-red-50 text-red-700',
];
@endphp
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$complaint->status] ?? 'bg-gray-50 text-gray-700' }}">
```

**Priority Badge (Lines 286-289):**
```blade
@php
$priorityClasses = [
    'low' => 'bg-gray-50 text-gray-700',
    'medium' => 'bg-yellow-50 text-yellow-700',
    'high' => 'bg-orange-50 text-orange-700',
    'urgent' => 'bg-red-50 text-red-700',
];
@endphp
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityClasses[$complaint->priority] ?? 'bg-gray-50 text-gray-700' }}">
```

#### File: `resources/views/admin/categories/index.blade.php` (Lines 101-102)
```blade
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $category->is_active 
    ? 'bg-green-50 text-green-700 ring-green-600/20' 
    : 'bg-red-50 text-red-700 ring-red-600/10' }}">
```

---

## Testing Checklist After Fixes

- [ ] Create announcement - verify file upload works
- [ ] Edit announcement - verify attachments preview shows correctly
- [ ] Complaints index - verify status badges show correct colors
- [ ] Complaints show - verify all badges display properly
- [ ] Categories index - verify active/inactive badges
- [ ] No console errors
- [ ] No linter warnings

---

## Additional Quick Improvements

### Add Loading States
```javascript
// Add to form submissions
document.querySelector('form').addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">...</svg> Processing...';
});
```

### Add Confirmation Dialogs
```javascript
// Add to delete buttons
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
        if (!confirm(this.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
```

### Add Toast Notifications
```javascript
// Add to layouts/admin.blade.php
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif
```

---

## Files to Edit Summary

1. ✅ `app/Http/Controllers/Admin/AnnouncementController.php` - Line 105
2. ✅ `resources/views/admin/announcements/create.blade.php` - Line 329 + JS
3. ✅ `resources/views/admin/announcements/index.blade.php` - Lines 192-193, 198-201
4. ✅ `resources/views/admin/announcements/show.blade.php` - Lines 349-351
5. ✅ `resources/views/admin/complaints/index.blade.php` - Lines 133-136, 211-214
6. ✅ `resources/views/admin/complaints/show.blade.php` - Lines 273-276, 286-289, 473
7. ✅ `resources/views/admin/categories/index.blade.php` - Lines 101-102

**Total Files:** 7
**Total Lines:** ~20 changes
**Estimated Time:** 30-60 minutes

---

## Execution Order

1. **Start with Backend** (5 min)
   - Fix AnnouncementController.php

2. **Fix CSS Structure** (20 min)
   - Fix all conditional class issues
   - Use @php blocks for class arrays

3. **Fix JavaScript** (15 min)
   - Update grid/hidden dynamic classes
   - Test file upload preview

4. **Testing** (20 min)
   - Test each fixed component
   - Verify no regressions

---

**Status:** Ready to Execute
**Created:** 2025-10-17
