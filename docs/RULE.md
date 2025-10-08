# 🧠 Context & Rules — Sistem Pengaduan Warga (Web Admin + User)

## 🎯 Tujuan Sistem
Sistem ini adalah platform berbasis **Laravel** untuk mengelola pengaduan masyarakat di lingkungan RT/RW.  
Web ini digunakan oleh **admin (RT/RW)** dan **user warga** untuk mengelola serta memantau laporan pengaduan secara transparan dan efisien.  
Sistem terintegrasi dengan **API** untuk aplikasi mobile (Flutter) dan **database Supabase** sebagai penyimpanan utama.

---

## 👥 Role dan Hak Akses

### 1. 🧑‍💼 Admin (RT / RW)
**Peran utama:** Mengelola seluruh data pengaduan, warga, dan pengumuman.

**Hak akses:**
- Melihat semua laporan pengaduan dari seluruh warga.  
- Mengubah status pengaduan (menunggu → diproses → selesai).  
- Memberikan catatan atau file tanggapan (`response_note`, `response_file`).  
- Mengelola data warga (`users`), termasuk aktif/nonaktif akun.  
- Membuat, mengedit, dan menghapus **pengumuman (`announcements`)**.  
- Melihat log aktivitas (audit trail).  
- Mengunduh laporan (PDF/Excel) berdasarkan rentang tanggal.  
- Menerima notifikasi jika ada laporan baru dari warga.  

**Batasan:**
- Tidak bisa membuat laporan pengaduan (fitur hanya untuk warga).
- Tidak bisa mengubah data laporan yang sudah berstatus *selesai* tanpa alasan valid (harus melalui audit log).

---

### 2. 👤 User (Warga)
**Peran utama:** Melaporkan masalah di lingkungan dan memantau tindak lanjut laporan.

**Hak akses:**
- Membuat laporan pengaduan (`reports`) dengan deskripsi, lokasi, dan bukti foto.  
- Melihat daftar pengaduan milik sendiri.  
- Memantau status laporan secara real-time (menunggu, diproses, selesai).  
- Mendapat notifikasi saat laporan direspons atau ditindaklanjuti.  
- Melihat pengumuman dari admin.  
- Mengedit atau menghapus laporan **selama masih berstatus "menunggu"**.

**Batasan:**
- Tidak bisa melihat laporan milik warga lain.
- Tidak bisa mengubah laporan yang sudah berstatus *diproses* atau *selesai*.
- Tidak memiliki akses ke dashboard admin, data pengguna lain, atau audit log.

---

## 🧩 Fitur Utama Web

### Untuk Admin:
1. **Dashboard Statistik**
   - Jumlah laporan (menunggu, diproses, selesai)
   - Grafik pengaduan bulanan
2. **Manajemen Pengaduan**
   - Lihat detail laporan
   - Update status dan catatan tindak lanjut
3. **Manajemen Pengumuman**
   - CRUD pengumuman
4. **Manajemen User**
   - Tambah/Edit/Hapus warga
   - Reset password warga
5. **Laporan & Export**
   - Filter berdasarkan tanggal, kategori, atau status
   - Export ke PDF / Excel
6. **Notifikasi**
   - Notifikasi laporan baru dari warga
7. **Audit Log**
   - Merekam semua aktivitas admin

---

### Untuk Warga (User Web):
1. **Buat Pengaduan**
   - Isi deskripsi, kategori, lokasi, upload foto
2. **Riwayat Pengaduan**
   - Lihat daftar laporan sendiri + statusnya
3. **Detail Laporan**
   - Lihat catatan tindak lanjut dari admin
4. **Pengumuman RT/RW**
   - Lihat daftar pengumuman publik
5. **Profil**
   - Edit data diri dan password
6. **Notifikasi**
   - Lihat status pengaduan dan info dari admin

---

## 🔐 Aturan Umum Sistem
- Semua request ke API dilindungi **token autentikasi Laravel Sanctum**.  
- Setiap aksi penting (ubah status, hapus data) otomatis tercatat di **audit_logs**.  
- Akses dibatasi dengan **middleware berdasarkan role (`admin` atau `user`)**.  
- Setiap laporan memiliki `created_by` dan `updated_by` untuk jejak audit.  
- Supabase digunakan untuk menyimpan data secara sinkron dan aman, termasuk file (bukti pengaduan).

---

## 🧠 Prompt Context (untuk AI Coding Assistant)
Saat menggunakan AI untuk generate code, tambahkan konteks berikut:

> “Saya sedang membuat web admin dan user untuk sistem pengaduan warga.  
> Web dibuat dengan Laravel 11, database Supabase, dan Flutter digunakan untuk mobile.  
> Ada dua role utama: admin dan user warga.  
> Admin bisa melihat, memproses, dan menanggapi semua laporan.  
> User hanya bisa membuat dan melihat laporan sendiri.  
> Sistem harus aman, transparan, dan memiliki fitur notifikasi serta pelaporan PDF/Excel.”

---

## 📚 Tujuan Akhir
- Meningkatkan transparansi dan efisiensi dalam penanganan laporan warga.  
- Mempermudah warga melapor dan memantau hasil pengaduan.  
- Memberikan alat bantu modern bagi RT/RW dalam mengelola administrasi lingkungan.

