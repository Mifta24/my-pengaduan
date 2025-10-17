<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users to be authors
        $adminUsers = User::role(['admin', 'Lurah', 'rw'])->get();

        if ($adminUsers->isEmpty()) {
            $adminUser = User::first(); // Fallback to first user
        } else {
            $adminUser = $adminUsers->first();
        }

        $announcements = [
            [
                'title' => 'Pengumuman Penting: Rapat Lurah Bulanan',
                'slug' => 'pengumuman-penting-rapat-Lurah-bulanan',
                'summary' => 'Rapat rutin bulanan Lurah akan diadakan minggu depan untuk membahas berbagai hal penting.',
                'content' => 'Assalamualaikum Wr. Wb.

Dengan hormat,

Kami mengundang seluruh warga Lurah 001/RW 003 untuk menghadiri rapat bulanan yang akan dilaksanakan pada:

**Hari/Tanggal:** Sabtu, 15 Oktober 2025
**Waktu:** 19.30 - 21.00 WIB
**Tempat:** Balai Lurah 001/RW 003

**Agenda Rapat:**
1. Laporan keuangan Lurah bulan September 2025
2. Program kerja bakti lingkungan
3. Pembahasan iuran bulanan
4. Persiapan peringatan 17 Agustus tahun depan
5. Lain-lain

Kehadiran Bapak/Ibu sangat diharapkan demi kelancaran dan kemajuan lingkungan kita bersama.

Demikian pengumuman ini kami sampaikan. Terima kasih.

Wassalamualaikum Wr. Wb.

**Pengurus Lurah 001/RW 003**',
                'priority' => 'high',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => true,
                'allow_comments' => true,
                'published_at' => now()->subDays(2),
                'views_count' => 45,
                'author_id' => $adminUser->id
            ],
            [
                'title' => 'Info: Jadwal Pengambilan Sampah Terbaru',
                'slug' => 'info-jadwal-pengambilan-sampah-terbaru',
                'summary' => 'Perubahan jadwal pengambilan sampah mulai berlaku minggu ini.',
                'content' => 'Kepada Yth. Warga Lurah 001/RW 003

Bersama ini kami informasikan bahwa mulai tanggal 10 Oktober 2025, jadwal pengambilan sampah mengalami perubahan sebagai berikut:

**Jadwal Baru:**
- **Senin & Kamis:** Sampah organik (sisa makanan, daun-daunan)
- **Selasa & Jumat:** Sampah anorganik (plastik, kertas, kaleng)
- **Sabtu:** Sampah elektronik dan barang bekas

**Ketentuan:**
- Sampah dikeluarkan maksimal jam 06.00 WIB
- Gunakan kantong sampah yang berbeda untuk setiap jenis
- Sampah berbahaya (baterai, lampu) dikumpulkan terpisah

Mohon kerja sama semua warga untuk mematuhi jadwal ini demi kebersihan lingkungan.

Terima kasih.',
                'priority' => 'medium',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => false,
                'allow_comments' => true,
                'published_at' => now()->subDays(1),
                'views_count' => 32,
                'author_id' => $adminUser->id
            ],
            [
                'title' => 'Gotong Royong Bersih-Bersih Lingkungan',
                'slug' => 'gotong-royong-bersih-bersih-lingkungan',
                'summary' => 'Mari bergotong royong membersihkan lingkungan Lurah kita tercinta.',
                'content' => 'Assalamualaikum Wr. Wb.

Dalam rangka menjaga kebersihan dan kenyamanan lingkungan Lurah 001/RW 003, kami mengajak seluruh warga untuk berpartisipasi dalam kegiatan gotong royong:

**Waktu Pelaksanaan:**
- **Hari:** Minggu, 20 Oktober 2025
- **Jam:** 06.00 - 10.00 WIB
- **Titik Kumpul:** Depan Pos Lurah

**Kegiatan yang Akan Dilakukan:**
1. Pembersihan selokan dan saluran air
2. Penyapuan jalan dan gang
3. Pemangkasan rumput liar
4. Pengecatan ulang pagar dan gerbang
5. Penanaman pohon di area kosong

**Yang Perlu Dibawa:**
- Alat kebersihan (sapu, cangkul, gunting rumput)
- Sarung tangan dan masker
- Air minum secukupnya

Setelah kegiatan, akan disediakan makan bersama untuk semua peserta.

Mari bersama-sama menjaga lingkungan kita agar tetap bersih dan asri!

Wassalamualaikum Wr. Wb.',
                'priority' => 'medium',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => false,
                'allow_comments' => true,
                'published_at' => now()->subHours(12),
                'views_count' => 28,
                'author_id' => $adminUser->id
            ],
            [
                'title' => 'Peringatan: Waspada Cuaca Ekstrem',
                'slug' => 'peringatan-waspada-cuaca-ekstrem',
                'summary' => 'BMKG memprediksi cuaca ekstrem dalam beberapa hari ke depan. Harap waspada.',
                'content' => '🚨 **PERINGATAN CUACA EKSTREM** 🚨

Berdasarkan informasi dari BMKG, wilayah kita diprediksi akan mengalami cuaca ekstrem dalam 3-5 hari ke depan dengan karakteristik:

**Prediksi Cuaca:**
- Hujan lebat disertai angin kencang
- Intensitas hujan 50-100 mm/hari
- Angin kencang hingga 40 km/jam
- Potensi petir dan banjir lokal

**Himbauan Kepada Warga:**
1. ⚠️ Hindari aktivitas di luar rumah saat hujan deras
2. 🏠 Pastikan atap rumah dalam kondisi baik
3. 🌳 Pangkas dahan pohon yang rawan patah
4. 🔌 Matikan peralatan elektronik saat petir
5. 📱 Siapkan penerangan darurat (senter, lilin)
6. 💧 Simpan air bersih secukupnya
7. 🚗 Hati-hati saat berkendara

**Kontak Darurat:**
- Pos Lurah: 0812-3456-7890
- Damkar: 113
- PMI: 0274-387878

Tetap waspada dan saling menjaga. Semoga kita semua dalam lindungan Allah SWT.

**Tim Siaga Bencana Lurah 001/RW 003**',
                'priority' => 'urgent',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => true,
                'allow_comments' => true,
                'published_at' => now()->subHours(6),
                'views_count' => 67,
                'author_id' => $adminUser->id
            ],
            [
                'title' => 'Pembukaan Pendaftaran Posyandu Balita',
                'slug' => 'pembukaan-pendaftaran-posyandu-balita',
                'summary' => 'Posyandu bulan November 2025 sudah dibuka. Daftarkan balita Anda segera.',
                'content' => 'Kepada Orang Tua Balita di Lurah 001/RW 003

Kami informasikan bahwa pendaftaran Posyandu bulan November 2025 telah dibuka dengan jadwal:

**Jadwal Posyandu:**
- **Tanggal:** 25 November 2025
- **Waktu:** 08.00 - 12.00 WIB
- **Tempat:** Balai Lurah 001/RW 003

**Layanan yang Tersedia:**
✅ Penimbangan dan pengukuran tinggi badan
✅ Imunisasi lengkap
✅ Pemberian vitamin A
✅ Konsultasi gizi dan kesehatan
✅ Pemberian makanan tambahan
✅ Pemeriksaan kesehatan umum

**Syarat Pendaftaran:**
- Bawa KTP orang tua
- Bawa KIA (Kartu Ibu dan Anak)
- Bawa buku imunisasi anak

**Kuota Terbatas:** 50 balita
**Pendaftaran:** Hubungi Ibu Sari (Lurah) - 0812-1234-5678

Jangan lewatkan kesempatan ini untuk memantau tumbuh kembang buah hati Anda!

**Kader Posyandu Lurah 001/RW 003**',
                'priority' => 'medium',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => false,
                'allow_comments' => true,
                'published_at' => now()->subHours(3),
                'views_count' => 15,
                'author_id' => $adminUser->id
            ]
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
