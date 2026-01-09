# 🎨 MyPengaduan - Design Guidelines

Panduan lengkap untuk membuat desain UI/UX aplikasi MyPengaduan.

---

## 📋 Table of Contents
- [Design Philosophy](#design-philosophy)
- [Color Palette](#color-palette)
- [Typography](#typography)
- [Spacing & Layout](#spacing--layout)
- [Components](#components)
- [Screen Designs](#screen-designs)
- [User Flow](#user-flow)
- [Icons & Illustrations](#icons--illustrations)
- [Design Tools](#design-tools)

---

## 🎯 Design Philosophy

### Prinsip Desain
1. **Simple & Clear** - Interface yang mudah dipahami semua kalangan
2. **Accessible** - Dapat digunakan oleh semua usia (18-65+)
3. **Trustworthy** - Desain yang memberi rasa aman dan terpercaya
4. **Efficient** - Proses pengaduan cepat dan mudah
5. **Responsive** - Tampil baik di semua ukuran layar

### Target Users
- **Masyarakat Umum** - Warga yang ingin menyampaikan pengaduan
- **Admin/RT** - Petugas yang mengelola pengaduan
- **Usia:** 18-65+ tahun
- **Tech Literacy:** Basic hingga intermediate

---

## 🎨 Color Palette

### Primary Colors
```
Primary (Blue)
- Primary 900: #0D47A1  - Dark Blue
- Primary 700: #1976D2  - Main Blue
- Primary 500: #2196F3  - Light Blue
- Primary 300: #64B5F6  - Lighter Blue
- Primary 100: #BBDEFB  - Very Light Blue

Usage: Main actions, headers, active states
```

### Secondary Colors
```
Secondary (Green)
- Secondary 700: #388E3C  - Dark Green
- Secondary 500: #4CAF50  - Main Green
- Secondary 300: #81C784  - Light Green

Usage: Success states, resolved complaints
```

### Status Colors
```
Status Colors:
- Success:  #4CAF50  - Green (Resolved)
- Warning:  #FF9800  - Orange (In Progress)
- Danger:   #F44336  - Red (Rejected)
- Pending:  #FFC107  - Amber (Pending)
- Info:     #2196F3  - Blue (Information)
```

### Neutral Colors
```
Neutral/Gray Scale:
- Gray 900: #212121  - Text Primary
- Gray 700: #616161  - Text Secondary
- Gray 500: #9E9E9E  - Disabled Text
- Gray 300: #E0E0E0  - Borders
- Gray 100: #F5F5F5  - Background
- White:    #FFFFFF  - Surface
```

### Usage Examples
```
Background:         Gray 100 (#F5F5F5)
Surface (Cards):    White (#FFFFFF)
Text Primary:       Gray 900 (#212121)
Text Secondary:     Gray 700 (#616161)
Dividers:           Gray 300 (#E0E0E0)
Primary Action:     Primary 700 (#1976D2)
Danger Action:      Danger (#F44336)
```

---

## ✍️ Typography

### Font Family
```
Primary Font: Roboto (Android) / San Francisco (iOS)
Alternative: Inter, Open Sans

Usage:
- Headings: Roboto Medium / Bold
- Body: Roboto Regular
- Captions: Roboto Regular (smaller)
```

### Font Sizes & Weights

```
H1 - Page Title
Size: 24sp / 1.5rem
Weight: Bold (700)
Line Height: 32sp
Usage: Screen titles

H2 - Section Title
Size: 20sp / 1.25rem
Weight: Bold (700)
Line Height: 28sp
Usage: Section headers

H3 - Card Title
Size: 18sp / 1.125rem
Weight: Medium (500)
Line Height: 24sp
Usage: Card headers, item titles

Body 1 - Primary Text
Size: 16sp / 1rem
Weight: Regular (400)
Line Height: 24sp
Usage: Main content, descriptions

Body 2 - Secondary Text
Size: 14sp / 0.875rem
Weight: Regular (400)
Line Height: 20sp
Usage: Supporting text

Caption
Size: 12sp / 0.75rem
Weight: Regular (400)
Line Height: 16sp
Usage: Timestamps, labels, hints

Button Text
Size: 14sp / 0.875rem
Weight: Medium (500)
Letter Spacing: 0.5sp
Usage: All buttons
```

### Text Colors
```
Primary Text:    Gray 900 (#212121)
Secondary Text:  Gray 700 (#616161)
Disabled Text:   Gray 500 (#9E9E9E)
Link Text:       Primary 700 (#1976D2)
Error Text:      Danger (#F44336)
Success Text:    Success (#4CAF50)
```

---

## 📐 Spacing & Layout

### Spacing Scale (8pt Grid System)
```
4px  (0.25rem) - xs   - Tight spacing
8px  (0.5rem)  - sm   - Small spacing
12px (0.75rem) - md   - Default spacing
16px (1rem)    - lg   - Medium spacing
24px (1.5rem)  - xl   - Large spacing
32px (2rem)    - 2xl  - Extra large spacing
48px (3rem)    - 3xl  - Section spacing
64px (4rem)    - 4xl  - Page spacing
```

### Layout Margins & Padding
```
Screen Padding:     16px (lg) - 24px (xl)
Card Padding:       16px (lg)
List Item Padding:  12px (md) vertical, 16px (lg) horizontal
Button Padding:     12px (md) vertical, 24px (xl) horizontal
Section Spacing:    24px (xl) - 32px (2xl)
```

### Component Spacing
```
Between Labels & Inputs:     8px (sm)
Between Form Fields:         16px (lg)
Between Sections:            24px (xl)
Between Cards:               12px (md)
Bottom Navigation Height:    56px
App Bar Height:              56px
```

### Border Radius
```
Small (Buttons, Tags):       4px
Medium (Cards, Inputs):      8px
Large (Bottom Sheets):       16px
Circle (Avatar):             50%
```

---

## 🧩 Components

### 1. Buttons

#### Primary Button
```
Background: Primary 700 (#1976D2)
Text: White
Padding: 12px vertical, 24px horizontal
Border Radius: 4px
Min Height: 48px
Font: 14sp, Medium (500)
State: Pressed (Primary 900), Disabled (Gray 300)
```

#### Secondary Button
```
Background: White
Text: Primary 700
Border: 1px solid Primary 700
Padding: 12px vertical, 24px horizontal
Border Radius: 4px
Min Height: 48px
```

#### Text Button
```
Background: Transparent
Text: Primary 700
Padding: 8px horizontal
Min Height: 36px
```

#### Floating Action Button (FAB)
```
Background: Primary 700
Icon: White
Size: 56x56px
Border Radius: Circle
Shadow: Elevation 6
Position: Bottom right (16px margin)
```

### 2. Input Fields

#### Text Input
```
Border: 1px solid Gray 300
Border Radius: 8px
Padding: 12px
Min Height: 48px
Background: White

States:
- Default: Border Gray 300
- Focus: Border Primary 700, Shadow
- Error: Border Danger, Helper text in red
- Disabled: Background Gray 100
```

#### Text Area
```
Same as Text Input
Min Height: 120px
Max Lines: 5
```

#### Dropdown/Select
```
Same as Text Input
Icon: Chevron down (right aligned)
Dropdown: White background, Shadow elevation 2
```

### 3. Cards

#### Standard Card
```
Background: White
Border Radius: 8px
Shadow: Elevation 2
Padding: 16px
Margin: 12px between cards
```

#### Complaint Card
```
Background: White
Border Radius: 8px
Shadow: Elevation 1
Padding: 16px

Content:
- Status Badge (top right)
- Title (H3, Bold)
- Category (Body 2, Secondary)
- Date (Caption, Secondary)
- Preview Text (Body 2, 2 lines max)
```

#### Notification Card
```
Background: White (read) / Primary 100 (unread)
Border Left: 4px solid Primary 700 (unread only)
Border Radius: 8px
Padding: 12px
Margin: 8px between items

Content:
- Icon (left)
- Title (Body 1, Bold if unread)
- Message (Body 2)
- Timestamp (Caption)
- Dot indicator (unread only)
```

### 4. Status Badges

```
Pending:
- Background: #FFF3E0 (Amber 50)
- Text: #F57C00 (Amber 700)
- Icon: Clock

In Progress:
- Background: #E3F2FD (Blue 50)
- Text: #1976D2 (Blue 700)
- Icon: Progress circle

Resolved:
- Background: #E8F5E9 (Green 50)
- Text: #388E3C (Green 700)
- Icon: Check circle

Rejected:
- Background: #FFEBEE (Red 50)
- Text: #D32F2F (Red 700)
- Icon: Cancel

Design:
- Padding: 4px 8px
- Border Radius: 12px (pill shape)
- Font: 12sp, Medium
```

### 5. Bottom Navigation

```
Height: 56px
Background: White
Shadow: Elevation 8

Items: 4-5 items
- Icon: 24x24px
- Label: 12sp
- Active: Primary 700
- Inactive: Gray 500
- Indicator: Background Primary 100 (optional)
```

### 6. App Bar / Top Bar

```
Height: 56px
Background: Primary 700
Title: White, 20sp, Medium
Icons: White, 24x24px
Shadow: Elevation 4
```

### 7. List Items

```
Min Height: 72px
Padding: 12px vertical, 16px horizontal
Divider: 1px solid Gray 300

Content:
- Leading Icon/Avatar (40x40px)
- Title (Body 1)
- Subtitle (Body 2, Secondary)
- Trailing Icon/Text
```

### 8. Dialogs

```
Background: White
Border Radius: 16px
Max Width: 280dp (mobile)
Padding: 24px
Shadow: Elevation 24

Title: H2
Content: Body 1
Actions: Text buttons (right aligned)
```

### 9. Bottom Sheets

```
Background: White
Border Radius: 16px (top corners only)
Padding: 16px
Handle Bar: 32px width, 4px height, Gray 300

Content: Flexible based on use case
```

---

## 📱 Screen Designs

### 1. Splash Screen

```
Layout:
┌─────────────────────┐
│                     │
│                     │
│     [App Logo]      │
│                     │
│    MyPengaduan      │
│                     │
│   [Loading...]      │
│                     │
│                     │
└─────────────────────┘

Elements:
- Center aligned
- App logo/icon (120x120px)
- App name (H1)
- Loading indicator
- Background: Primary 700 or White
```

### 2. Login Screen

```
Layout:
┌─────────────────────┐
│      [Logo]         │
│                     │
│  Selamat Datang     │
│  Silakan login      │
│                     │
│  [Email Input]      │
│  [Password Input]   │
│                     │
│  [ Lupa Password? ] │
│                     │
│  [   LOGIN BTN   ]  │
│                     │
│  Belum punya akun?  │
│  [    Daftar    ]   │
│                     │
└─────────────────────┘

Elements:
- Logo (80x80px)
- Title (H1)
- Subtitle (Body 1)
- Email input (icon: email)
- Password input (icon: lock, show/hide)
- Forgot password link (right aligned)
- Login button (full width, primary)
- Register link (center aligned)
```

### 3. Register Screen

```
Layout:
┌─────────────────────┐
│  < Daftar Akun      │
│                     │
│  [Nama Lengkap]     │
│  [Email]            │
│  [No. Telepon]      │
│  [Alamat]           │
│  [Password]         │
│  [Konfirmasi Pass]  │
│                     │
│  [   DAFTAR   ]     │
│                     │
│  Sudah punya akun?  │
│  [    Login    ]    │
│                     │
└─────────────────────┘

Elements:
- App bar with back button
- Form inputs (all required)
- Register button (primary)
- Login link
```

### 4. Home Screen

```
Layout:
┌─────────────────────┐
│ ☰  MyPengaduan  🔔  │
├─────────────────────┤
│  Halo, [Name]       │
│  [Search Box]       │
│                     │
│  📊 Statistik       │
│  ┌────┬────┬────┐   │
│  │ 5  │ 3  │ 2  │   │
│  │Tot │Pros│Slsi│   │
│  └────┴────┴────┘   │
│                     │
│  📝 Pengaduan Saya  │
│  ┌─────────────┐    │
│  │ [Complaint]│     │
│  │ Card       │     │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ [Complaint]│     │
│  │ Card       │     │
│  └─────────────┘    │
│                     │
├─────────────────────┤
│ [Home][Notif][Prof] │
└─────────────────────┘

Elements:
- App bar (drawer icon, title, notification icon with badge)
- Welcome section
- Search bar
- Statistics cards (3 columns)
- Complaint list (scrollable)
- FAB (+ create complaint)
- Bottom navigation
```

### 5. Complaint List Screen

```
Layout:
┌─────────────────────┐
│ <  Pengaduan Saya   │
├─────────────────────┤
│ [Filter] [Sort] [⋮] │
│                     │
│  ┌─────────────┐    │
│  │ [Badge]     │    │
│  │ Judul       │    │
│  │ Kategori • Date│  │
│  │ Preview...  │    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ [Complaint] │    │
│  │ Card        │    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ [Complaint] │    │
│  │ Card        │    │
│  └─────────────┘    │
│                     │
│        [+]          │
└─────────────────────┘

Elements:
- App bar with back button
- Filter/Sort buttons
- Complaint cards (scrollable)
- Empty state (jika kosong)
- FAB (create complaint)
```

### 6. Complaint Detail Screen

```
Layout:
┌─────────────────────┐
│ <  Detail Pengaduan │
├─────────────────────┤
│  [Status Badge]     │
│                     │
│  Judul Pengaduan    │
│  Kategori           │
│  📅 12 Jan 2026     │
│  📍 Lokasi          │
│                     │
│  Deskripsi:         │
│  Lorem ipsum dolor  │
│  sit amet...        │
│                     │
│  📷 Lampiran:       │
│  [img] [img] [img]  │
│                     │
│  💬 Tanggapan:      │
│  ┌─────────────┐    │
│  │ Admin       │    │
│  │ Response... │    │
│  │ 12 Jan 10:00│    │
│  └─────────────┘    │
│                     │
│  📊 Riwayat Status  │
│  • Dibuat - 12 Jan  │
│  • Diproses - 13 Jan│
│  • Selesai - 14 Jan │
│                     │
└─────────────────────┘

Elements:
- App bar (back, share, menu)
- Status badge (prominent)
- Details section
- Image gallery (horizontal scroll)
- Responses section
- Status timeline
- Action buttons (if pending)
```

### 7. Create Complaint Screen

```
Layout:
┌─────────────────────┐
│ <  Buat Pengaduan   │
├─────────────────────┤
│  [Kategori ▼]       │
│                     │
│  [Judul]            │
│                     │
│  [Deskripsi]        │
│  [Text Area]        │
│  [             ]    │
│                     │
│  [Lokasi]           │
│                     │
│  [Tanggal Kejadian] │
│                     │
│  📷 Upload Foto:    │
│  [+] [img] [img]    │
│                     │
│  Prioritas:         │
│  ○ Rendah           │
│  ○ Sedang           │
│  ● Tinggi           │
│                     │
│  [   KIRIM   ]      │
│                     │
└─────────────────────┘

Elements:
- App bar with back button
- Form inputs (all with labels)
- Category dropdown
- Text area for description
- Image picker (multiple)
- Priority radio buttons
- Submit button (primary)
```

### 8. Notification List Screen

```
Layout:
┌─────────────────────┐
│ <  Notifikasi    ✓  │
├─────────────────────┤
│ Filter: [Semua ▼]   │
│                     │
│  ┌─────────────┐    │
│  │● [Icon]     │    │
│  │  Title      │    │
│  │  Message... │    │
│  │  2 jam lalu │    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │  [Icon]     │    │
│  │  Title      │    │
│  │  Message... │    │
│  │  1 hari lalu│    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │  [Icon]     │    │
│  │  Message... │    │
│  │  3 hari lalu│    │
│  └─────────────┘    │
│                     │
└─────────────────────┘

Elements:
- App bar (back, mark all read)
- Filter dropdown
- Notification cards (unread highlighted)
- Swipe actions (delete, mark read)
- Empty state
```

### 9. Profile Screen

```
Layout:
┌─────────────────────┐
│ <  Profil           │
├─────────────────────┤
│     [Avatar]        │
│     Nama User       │
│     email@mail.com  │
│                     │
│  ┌─────────────┐    │
│  │ 📝 Edit Profil│   │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ 🔔 Pengaturan  │  │
│  │    Notifikasi │   │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ 🔐 Ganti    │    │
│  │    Password │    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ ℹ️ Tentang   │    │
│  │    Aplikasi │    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ 🚪 Keluar   │    │
│  └─────────────┘    │
│                     │
└─────────────────────┘

Elements:
- App bar
- Avatar (120x120px)
- User info
- Menu list items
- Logout button (danger color)
```

### 10. Announcement Screen

```
Layout:
┌─────────────────────┐
│ <  Pengumuman       │
├─────────────────────┤
│  🔴 PENTING         │
│  ┌─────────────┐    │
│  │ [Urgent]    │    │
│  │ Announcement│    │
│  └─────────────┘    │
│                     │
│  Terbaru            │
│  ┌─────────────┐    │
│  │ [Regular]   │    │
│  │ Announcement│    │
│  └─────────────┘    │
│  ┌─────────────┐    │
│  │ [Regular]   │    │
│  │ Announcement│    │
│  └─────────────┘    │
│                     │
└─────────────────────┘

Elements:
- App bar
- Urgent section (highlighted)
- Announcement cards
- Date & category labels
```

---

## 🔄 User Flow

### Flow 1: Login & Registration

```
Splash Screen
    ↓
Login Screen
    ├─→ Register Screen → Email Verification → Login
    └─→ Forgot Password → Reset Password → Login
        ↓
    Home Screen
```

### Flow 2: Create Complaint

```
Home Screen
    ↓
Click FAB (+)
    ↓
Create Complaint Form
    ├─→ Select Category
    ├─→ Fill Details
    ├─→ Upload Images
    └─→ Submit
        ↓
Success Dialog
    ↓
Complaint Detail Screen
```

### Flow 3: View Notifications

```
Home Screen (Notification Badge)
    ↓
Notification List
    ├─→ Filter (All/Unread/Read)
    └─→ Click Notification
        ↓
    Complaint Detail / Announcement Detail
```

### Flow 4: Track Complaint

```
Home Screen
    ↓
Complaint List
    ↓
Select Complaint
    ↓
Complaint Detail
    ├─→ View Status Timeline
    ├─→ View Responses
    └─→ View Attachments
```

### Flow 5: Profile Management

```
Home Screen
    ↓
Profile Tab
    ├─→ Edit Profile → Save
    ├─→ Change Password → Verify → Save
    ├─→ Notification Settings → Toggle → Save
    └─→ Logout → Confirm → Login Screen
```

---

## 🎨 Icons & Illustrations

### Icon Set
Gunakan **Material Icons** atau **Material Symbols**:
- Free & Open Source
- Consistent design language
- Available in Flutter/Android

### Common Icons
```
Navigation:
- home, notifications, person (profile)
- menu, arrow_back, close
- add (FAB), edit, delete

Status:
- check_circle (resolved)
- cancel (rejected)
- pending, hourglass_empty
- info, warning, error

Actions:
- send, upload, download
- share, favorite, bookmark
- search, filter, sort
- visibility, visibility_off

Content:
- description (document)
- location_on (location)
- calendar_today (date)
- attach_file (attachment)
- image, camera

Categories:
- construction (infrastruktur)
- lightbulb (listrik)
- local_hospital (kesehatan)
- security (keamanan)
- water_drop (air)
- delete (sampah)
```

### Illustrations

**Empty States:**
```
No Complaints:
- Illustration of relaxed person
- Text: "Belum ada pengaduan"
- CTA: "Buat Pengaduan Pertama"

No Notifications:
- Illustration of mailbox
- Text: "Tidak ada notifikasi"

No Internet:
- Illustration of disconnected cable
- Text: "Tidak ada koneksi internet"
- CTA: "Coba Lagi"
```

**Sources untuk Illustrations:**
- undraw.co (free)
- storyset.com (free)
- humaaans.com (free)
- illustrationkit.com

---

## 🛠️ Design Tools

### Recommended Tools

#### 1. Figma (Recommended) ⭐
```
URL: figma.com
Price: Free (with limitations)

Features:
- Cloud-based (collaborative)
- Component system
- Auto-layout
- Prototyping
- Developer handoff
- Plugin ecosystem

Plugins Recommended:
- Material Design
- Iconify
- Unsplash (images)
- Lorem Ipsum
- Remove BG
```

#### 2. Adobe XD
```
URL: adobe.com/xd
Price: Free starter plan

Features:
- Design & prototype
- Auto-animate
- Repeat grid
- Component states
```

#### 3. Sketch (macOS only)
```
URL: sketch.com
Price: Paid

Features:
- Vector editing
- Symbols & overrides
- Plugin ecosystem
```

### Design Resources

#### Material Design
```
URL: material.io/design
- Official Material Design guidelines
- Component specifications
- Color tools
- Icon library
```

#### Android Design Guidelines
```
URL: developer.android.com/design
- Platform-specific guidelines
- Best practices
- Pattern library
```

#### Flutter Widget Catalog
```
URL: docs.flutter.dev/ui/widgets
- Flutter UI components
- Interactive examples
```

---

## 📏 Design Specifications

### Mobile Screens
```
Android:
- Base: 360x640dp (Compact)
- Medium: 411x731dp (Most common)
- Large: 428x926dp (Modern phones)

Breakpoints:
- Compact: < 600dp width
- Medium: 600-840dp width
- Expanded: > 840dp width
```

### Safe Areas
```
Status Bar: 24dp height
Navigation Bar: 48-56dp height
Screen Padding: 16-24dp
```

### Touch Targets
```
Minimum: 48x48dp (Material Design spec)
Recommended: 48-56dp for primary actions
Spacing: 8dp minimum between touch targets
```

---

## ✅ Design Checklist

### Before Development
- [ ] All screens designed
- [ ] Component library created
- [ ] Color palette defined
- [ ] Typography system set
- [ ] Icons selected/created
- [ ] Responsive layouts checked
- [ ] Dark mode considered (optional)
- [ ] Accessibility checked
- [ ] User flow validated
- [ ] Developer handoff prepared

### Deliverables
- [ ] Figma/XD file (organized)
- [ ] Design system document
- [ ] Color values (hex codes)
- [ ] Font specifications
- [ ] Spacing values
- [ ] Icon files (SVG/PNG)
- [ ] Image assets (2x, 3x)
- [ ] Prototype/demo
- [ ] Design annotations

---

## 📱 Example Screen Templates

### Login Screen Template
```
Figma Dimensions: 375x812 (iPhone X)
Android: 360x640dp

Elements:
1. Logo: Center, top margin 80dp
2. Title: Below logo, 24dp margin
3. Inputs: Start 120dp from top, 16dp spacing
4. Button: 24dp below last input
5. Register link: 16dp below button

Colors:
- Background: White
- Primary button: #1976D2
- Text: #212121
- Secondary text: #616161
```

### Home Screen Template
```
Dimensions: 375x812

Layout:
- App bar: 56dp height
- Content: Scroll view with padding 16dp
- Statistics: 3 cards, equal width, 8dp gap
- Complaint cards: Full width, 12dp vertical gap
- FAB: Bottom right, 16dp margin
- Bottom nav: 56dp height

Colors:
- App bar: #1976D2 (Primary)
- Background: #F5F5F5 (Gray 100)
- Cards: #FFFFFF
- FAB: #1976D2
```

---

## 🎯 Priority Screens

### Phase 1 (MVP)
1. ✅ Splash Screen
2. ✅ Login Screen
3. ✅ Register Screen
4. ✅ Home Screen
5. ✅ Create Complaint Screen
6. ✅ Complaint Detail Screen
7. ✅ Notification List Screen
8. ✅ Profile Screen

### Phase 2 (Enhanced)
9. Complaint List Screen (dengan filter)
10. Edit Profile Screen
11. Change Password Screen
12. Notification Settings Screen
13. Announcement List Screen
14. Announcement Detail Screen

### Phase 3 (Polish)
15. Search Screen
16. Filter Bottom Sheet
17. Image Viewer Screen
18. About App Screen
19. Help/FAQ Screen
20. Feedback Screen

---

## 📚 Design References

### Similar Apps
- Lapor! (Indonesia)
- MySejahtera (Malaysia)
- SG Clean (Singapore)
- FixMyStreet (UK)

### Inspiration
- Dribbble.com (search: "complaint app", "civic app")
- Behance.net
- Mobbin.com (mobile app designs)

---

## 🎨 Figma Starter Template

### Structure
```
Figma File Structure:

📁 MyPengaduan Design
├─ 📄 Cover (project info)
├─ 🎨 Design System
│  ├─ Colors
│  ├─ Typography
│  ├─ Components
│  │  ├─ Buttons
│  │  ├─ Input Fields
│  │  ├─ Cards
│  │  ├─ Badges
│  │  └─ Navigation
│  └─ Icons
├─ 📱 Screens - Login & Auth
├─ 📱 Screens - Home & Main
├─ 📱 Screens - Complaints
├─ 📱 Screens - Notifications
├─ 📱 Screens - Profile
├─ 🔄 User Flows
└─ 📦 Assets & Export
```

### Components to Create
```
Components (reusable):
1. Button (Primary, Secondary, Text)
2. Input Field (Default, Error, Disabled)
3. Card (Standard, Complaint, Notification)
4. Status Badge (4 states)
5. Avatar (with/without image)
6. App Bar
7. Bottom Navigation
8. List Item
9. Dialog
10. Bottom Sheet
```

---

## 🚀 Getting Started

### For Designers

**Step 1: Setup Figma**
1. Create Figma account
2. Create new project: "MyPengaduan"
3. Set artboard: 375x812 (mobile)

**Step 2: Build Design System**
1. Create color styles (Primary, Secondary, Status, Neutral)
2. Create text styles (H1-H3, Body, Caption, Button)
3. Create component library (buttons, inputs, cards)

**Step 3: Design Screens**
1. Start with Login & Home (high priority)
2. Create variants for different states
3. Add interactions/prototyping
4. Test user flow

**Step 4: Developer Handoff**
1. Organize layers properly
2. Name layers clearly
3. Add annotations for interactions
4. Export assets (icons, images)
5. Share with developer (view access)

---

## 📞 Support

### Design Questions?
- Check Material Design docs first
- Review similar apps for inspiration
- Test designs with real users
- Iterate based on feedback

### Need Help?
- Material Design: material.io
- Flutter UI: docs.flutter.dev/ui
- Figma Help: help.figma.com

---

**Happy Designing! 🎨**

**Version:** 1.0.0  
**Last Updated:** January 6, 2026  
**Target Platform:** Flutter (Android/iOS)
