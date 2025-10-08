# Website Pengaduan Warga Digital (ruang lingkup rt) 

## 🧩 **Tahap 1 — Rencana Web Admin Laravel**

### 🎯 **Tujuan**

Membuat web admin untuk:

* mengelola laporan pengaduan warga,
* menindaklanjuti laporan,
* mengelola data pengguna,
* membuat pengumuman,
* dan menampilkan laporan statistik.

---

## ⚙️ **1. Fitur Utama Web Admin**

### 🧍‍♂️ **Autentikasi & Role**

* **Login Admin / Ketua RT**
* Role-based Access (Admin RW, Ketua RT, Staff RT)
* Middleware `auth` & `role` (bisa pakai Laravel Spatie Permission)

---

### 📝 **Manajemen Pengaduan**

CRUD lengkap untuk data pengaduan:

* **List Pengaduan** → tabel berisi: nama pelapor, kategori, status, tanggal laporan
* **Detail Pengaduan** → menampilkan deskripsi, foto bukti, lokasi, dan komentar
* **Ubah Status** → *menunggu*, *diproses*, *selesai*
* **Tambahkan Tanggapan** atau upload bukti penyelesaian
* **Hapus laporan** jika duplikat/spam

---

### 📢 **Manajemen Pengumuman**

* CRUD pengumuman untuk aplikasi warga
* Field: *judul, isi, tanggal, lampiran opsional*
* Ditampilkan juga di API agar muncul di Flutter app

---

### 👥 **Manajemen Warga**

* Lihat daftar warga terdaftar (nama, NIK, alamat, email)
* Nonaktifkan / aktifkan akun
* Lihat riwayat pengaduan setiap warga

---

### 📊 **Dashboard Statistik**

* Total laporan masuk
* Jumlah laporan selesai
* Jumlah laporan sedang diproses
* Jumlah warga aktif
* Grafik (Chart.js atau Laravel Charts)

---

### 📂 **Export Data**

* Export laporan ke **PDF atau Excel** untuk laporan bulanan
* Bisa filter berdasarkan status & tanggal

---

### 🔔 **Notifikasi (Opsional)**

* Admin bisa kirim notifikasi ke warga (via FCM atau Supabase Realtime)
* Contoh: *"Laporan Anda telah ditindaklanjuti oleh Ketua RT"*

---

## 🧱 **2. Struktur Database (Supabase / PostgreSQL)**

Berikut rancangan tabel inti:

### 📋 `users`

| Kolom      | Tipe                          | Keterangan    |
| ---------- | ----------------------------- | ------------- |
| id         | bigint                        | Primary key   |
| name       | varchar                       | Nama pengguna |
| email      | varchar                       | Email unik    |
| password   | varchar                       | Hash password |
| role       | enum('warga','rt','admin_rw') | Role pengguna |
| alamat     | text                          | Alamat warga  |
| created_at | timestamp                     |               |
| updated_at | timestamp                     |               |

---

### 📋 `complaints`

| Kolom           | Tipe                                  | Keterangan                                  |
| --------------- | ------------------------------------- | ------------------------------------------- |
| id              | bigint                                | Primary key                                 |
| user_id         | bigint (FK users)                     | Pelapor                                     |
| judul           | varchar                               | Judul laporan                               |
| deskripsi       | text                                  | Isi laporan                                 |
| kategori        | varchar                               | Jenis pengaduan (kebersihan, keamanan, dll) |
| foto            | varchar                               | Path bukti foto                             |
| lokasi          | varchar                               | Lokasi kejadian                             |
| status          | enum('menunggu','diproses','selesai') | Status laporan                              |
| tanggapan       | text                                  | Respon dari admin                           |
| tanggal_laporan | date                                  | Tanggal dibuat                              |
| created_at      | timestamp                             |                                             |
| updated_at      | timestamp                             |                                             |

---

### 📋 `announcements`

| Kolom      | Tipe              | Keterangan         |
| ---------- | ----------------- | ------------------ |
| id         | bigint            | Primary key        |
| judul      | varchar           | Judul pengumuman   |
| isi        | text              | Isi pengumuman     |
| tanggal    | date              | Tanggal publikasi  |
| created_by | bigint (FK users) | Pembuat pengumuman |
| created_at | timestamp         |                    |
| updated_at | timestamp         |                    |

---

### 📋 `responses` (opsional untuk komentar/tindak lanjut)

| Kolom        | Tipe                   | Keterangan         |
| ------------ | ---------------------- | ------------------ |
| id           | bigint                 | Primary key        |
| complaint_id | bigint (FK complaints) | ID pengaduan       |
| user_id      | bigint (FK users)      | Admin penindak     |
| isi          | text                   | Isi tanggapan      |
| foto         | varchar                | Bukti penyelesaian |
| created_at   | timestamp              |                    |

---

## 🧠 **3. Teknologi & Tools Laravel**

| Fitur             | Package / Tools yang Dipilih          |
| ----------------- | ------------------------------------- |
| Role & Permission | `spatie/laravel-permission`           |
| UI / Template     | **Tailwind CSS + Alpine.js**          |
| PDF Export        | `barryvdh/laravel-dompdf`             |
| Excel Export      | `maatwebsite/excel`                   |
| Auth              | **Laravel Breeze** (with Tailwind)    |
| API               | `laravel/sanctum` untuk Flutter       |
| Image Processing  | `intervention/image`                  |
| Audit Log         | `spatie/laravel-activitylog`          |
| Charts            | **Chart.js** (modern & responsive)    |
| Icons             | **Heroicons** atau **Lucide**         |

---

## 🚀 **4. Alur Kerja Sistem Web**

1. Admin login → masuk ke dashboard
2. Melihat laporan masuk → ubah status / tambahkan tanggapan
3. Membuat pengumuman baru → tampil di aplikasi mobile
4. Mengekspor laporan → PDF/Excel
5. (Opsional) Mengirim notifikasi ke warga

