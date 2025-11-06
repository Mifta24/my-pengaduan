# 🗑️ DELETE CONFIRMATION MODAL - Documentation

## 📋 Overview

Modal konfirmasi delete yang cantik dan modern untuk menggantikan `confirm()` dialog JavaScript standar.

**Features:**
- ✅ Modern UI dengan animasi smooth
- ✅ Icon warning yang jelas
- ✅ Responsive design
- ✅ Keyboard support (ESC to close)
- ✅ Auto-attach dengan data attributes
- ✅ Custom messages
- ✅ Accessible (ARIA labels)
- ✅ Focus management

---

## 🎨 Modal Design

### Visual Elements:
- **Icon:** Red warning triangle
- **Title:** "Konfirmasi Penghapusan"
- **Message:** Customizable warning text
- **Buttons:** 
  - Primary (Red): "Ya, Hapus" dengan icon trash
  - Secondary (Gray): "Batal"

### Colors:
- Background overlay: Gray 500 with 75% opacity
- Icon background: Red 100
- Icon: Red 600
- Delete button: Red 600 → Red 700 on hover
- Cancel button: White → Gray 50 on hover

---

## 🚀 Usage Methods

### Method 1: Data Attribute (Recommended - Auto)

Paling mudah! Tinggal tambahkan attribute `data-confirm-delete` ke button atau form.

#### On Delete Button:
```blade
<form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" 
            data-confirm-delete="Apakah Anda yakin ingin menghapus pengumuman '{{ $announcement->title }}'?"
            class="text-red-600 hover:text-red-900">
        Hapus
    </button>
</form>
```

#### On Form:
```blade
<form action="{{ route('admin.announcements.destroy', $announcement) }}" 
      method="POST"
      data-confirm-delete="Pengumuman ini akan dihapus permanen!">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600">Hapus</button>
</form>
```

#### With Link (Using data-form):
```blade
<form id="delete-form-{{ $item->id }}" 
      action="{{ route('admin.announcements.destroy', $item) }}" 
      method="POST" 
      style="display: none;">
    @csrf
    @method('DELETE')
</form>

<button data-confirm-delete="Hapus {{ $item->title }}?" 
        data-form="delete-form-{{ $item->id }}"
        class="text-red-600">
    <svg>...</svg> Hapus
</button>
```

---

### Method 2: JavaScript Function (Manual)

Untuk kontrol lebih detail atau kondisi khusus.

#### Basic Usage:
```javascript
showDeleteModal('Apakah Anda yakin?', formElement);
```

#### With Custom Message:
```javascript
function deleteItem(itemId, itemName) {
    const form = document.getElementById('delete-form-' + itemId);
    showDeleteModal(`Hapus ${itemName}? Data tidak dapat dikembalikan!`, form);
}
```

#### With Callback Function:
```javascript
showDeleteModal('Hapus semua data?', function() {
    // Custom logic
    fetch('/api/delete-all', { method: 'DELETE' })
        .then(response => response.json())
        .then(data => {
            alert('Berhasil dihapus!');
            location.reload();
        });
});
```

---

## 📝 Implementation Examples

### Example 1: Announcements Index (Card View)

**Before (Old confirm):**
```blade
<form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" 
      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600">Hapus</button>
</form>
```

**After (New modal):**
```blade
<form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" 
            data-confirm-delete="Apakah Anda yakin ingin menghapus pengumuman '{{ $announcement->title }}'? Semua data terkait akan terhapus."
            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus
    </button>
</form>
```

---

### Example 2: Complaints Table (Bulk Actions)

```blade
<!-- Bulk Delete Button -->
<button onclick="bulkDelete()" 
        class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
    Hapus Terpilih
</button>

<script>
function bulkDelete() {
    const selected = document.querySelectorAll('input[name="selected[]"]:checked');
    
    if (selected.length === 0) {
        alert('Pilih minimal 1 item untuk dihapus');
        return;
    }
    
    const count = selected.length;
    showDeleteModal(
        `Anda akan menghapus ${count} komplain. Tindakan ini tidak dapat dibatalkan!`,
        function() {
            // Submit bulk delete form
            document.getElementById('bulk-delete-form').submit();
        }
    );
}
</script>
```

---

### Example 3: Categories with Dependencies Check

```blade
<button onclick="deleteCategory({{ $category->id }}, {{ $category->complaints_count }})" 
        class="text-red-600 hover:text-red-900">
    Hapus
</button>

<form id="delete-category-{{ $category->id }}" 
      action="{{ route('admin.categories.destroy', $category) }}" 
      method="POST" 
      style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteCategory(categoryId, complaintsCount) {
    let message = 'Apakah Anda yakin ingin menghapus kategori ini?';
    
    if (complaintsCount > 0) {
        message = `Kategori ini memiliki ${complaintsCount} komplain. Hapus kategori ini akan mempengaruhi komplain terkait. Lanjutkan?`;
    }
    
    const form = document.getElementById('delete-category-' + categoryId);
    showDeleteModal(message, form);
}
</script>
```

---

### Example 4: User with Role Check

```blade
@if($user->id !== auth()->id())
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit"
                data-confirm-delete="Hapus user '{{ $user->name }}' ({{ $user->email }})? User tidak akan bisa login lagi."
                class="text-red-600 hover:text-red-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </form>
@else
    <span class="text-gray-400" title="Tidak dapat menghapus akun sendiri">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </span>
@endif
```

---

## 🎯 API Reference

### JavaScript Functions

#### `showDeleteModal(message, formOrCallback)`
Show the delete confirmation modal.

**Parameters:**
- `message` (string, optional): Custom message to display
- `formOrCallback` (HTMLFormElement|Function): Form to submit or callback function

**Example:**
```javascript
showDeleteModal('Delete this item?', document.getElementById('myForm'));
showDeleteModal('Delete all?', () => console.log('Deleted!'));
```

#### `closeDeleteModal()`
Close the delete confirmation modal.

**Example:**
```javascript
closeDeleteModal();
```

#### `confirmDelete()`
Confirm and execute the delete action (called internally by modal).

---

### HTML Data Attributes

#### `data-confirm-delete`
Add to button or form to enable automatic confirmation.

**Value:** Custom confirmation message (optional)

**Example:**
```html
<button data-confirm-delete="Custom message">Delete</button>
<form data-confirm-delete="Custom message">...</form>
```

#### `data-form`
Reference to form ID when button is outside the form.

**Value:** Form element ID

**Example:**
```html
<button data-confirm-delete="Delete?" data-form="my-form">Delete</button>
<form id="my-form">...</form>
```

---

## 🎨 Customization

### Change Modal Colors

Edit `resources/views/layouts/admin.blade.php`:

```html
<!-- Change icon background -->
<div class="bg-red-100"> <!-- Change to bg-orange-100, bg-yellow-100, etc -->

<!-- Change icon color -->
<svg class="text-red-600"> <!-- Change to text-orange-600, etc -->

<!-- Change delete button -->
<button class="bg-red-600 hover:bg-red-700"> <!-- Change colors -->
```

### Change Button Text

```javascript
// Find this line in admin.blade.php
innerHTML: 'Ya, Hapus' // Change to your text
```

### Add Animation

Add to modal div:
```html
<div class="... animate-fade-in">
```

CSS:
```css
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fade-in 0.2s ease-out;
}
```

---

## ✅ Migration Checklist

### Files to Update:

#### 1. Announcements
- [ ] `resources/views/admin/announcements/index.blade.php`
- [ ] `resources/views/admin/announcements/show.blade.php`

#### 2. Complaints
- [ ] `resources/views/admin/complaints/index.blade.php`
- [ ] `resources/views/admin/complaints/show.blade.php`

#### 3. Categories
- [ ] `resources/views/admin/categories/index.blade.php`
- [ ] `resources/views/admin/categories/show.blade.php`

#### 4. Users
- [ ] `resources/views/admin/users/index.blade.php`
- [ ] `resources/views/admin/users/show.blade.php`

#### 5. Responses (if any)
- [ ] Check for delete buttons in response views

### Search & Replace:

**Find:**
```blade
onsubmit="return confirm('
onclick="return confirm('
```

**Replace with:**
```blade
data-confirm-delete="
```

---

## 🧪 Testing Checklist

- [ ] Modal opens when delete button clicked
- [ ] Modal closes when "Batal" clicked
- [ ] Modal closes when overlay clicked
- [ ] Modal closes on ESC key press
- [ ] Delete action executes when "Ya, Hapus" clicked
- [ ] Custom messages display correctly
- [ ] Works with different form methods
- [ ] Works with callback functions
- [ ] Multiple delete buttons on same page
- [ ] Keyboard navigation works
- [ ] Mobile responsive
- [ ] No console errors

---

## 🐛 Troubleshooting

### Modal not showing?
1. Check browser console for JavaScript errors
2. Ensure `admin.blade.php` is properly loaded
3. Verify AlpineJS is loaded

### Delete not working?
1. Check form has correct action and method
2. Verify CSRF token is present
3. Check browser console for errors

### Custom message not showing?
1. Check attribute syntax: `data-confirm-delete="message"`
2. Ensure quotes are properly escaped

---

## 📊 Benefits

**Before (Old confirm):**
- ❌ Ugly browser default alert
- ❌ No customization
- ❌ Not consistent across browsers
- ❌ No accessibility features
- ❌ No animations

**After (New modal):**
- ✅ Beautiful, modern UI
- ✅ Fully customizable
- ✅ Consistent across all browsers
- ✅ Accessible (ARIA, keyboard)
- ✅ Smooth animations
- ✅ Professional appearance

---

## 🎓 Best Practices

1. **Always use descriptive messages**
   ```blade
   ❌ data-confirm-delete="Hapus?"
   ✅ data-confirm-delete="Hapus pengumuman '{{ $title }}'? Data tidak dapat dikembalikan."
   ```

2. **Include affected data count**
   ```blade
   ✅ "Kategori ini memiliki 5 komplain. Hapus?"
   ```

3. **Warn about irreversible actions**
   ```blade
   ✅ "Tindakan ini tidak dapat dibatalkan!"
   ```

4. **Prevent self-deletion**
   ```blade
   @if($user->id !== auth()->id())
       <!-- Show delete button -->
   @endif
   ```

5. **Check dependencies before delete**
   ```javascript
   if (hasRelatedData) {
       message += " Data terkait juga akan terpengaruh.";
   }
   ```

---

**Created:** 2025-10-20  
**Version:** 1.0  
**Status:** ✅ Ready for Production
