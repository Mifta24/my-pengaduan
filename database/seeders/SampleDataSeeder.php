<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mypengaduan.com')->firstOrFail();

        // ── Warga (Users) ────────────────────────────────────────────────────────
        $wargaData = [
            [
                'name'                    => 'Budi Santoso',
                'email'                   => 'budi.santoso@gmail.com',
                'password'                => Hash::make('password'),
                'role'                    => 'user',
                'nik'                     => '3201051205850002',
                'phone'                   => '082211223344',
                'address'                 => 'Gang Annur 2 No. 5, RT 05 RW 01',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => true,
                'verified_at'             => now()->subMonths(3),
                'is_active'               => true,
                'email_verified_at'       => now()->subMonths(3),
                'notification_preferences' => [
                    'email_notifications'  => true,
                    'sms_notifications'    => false,
                    'push_notifications'   => true,
                    'complaint_updates'    => true,
                    'system_announcements' => true,
                    'marketing_emails'     => false,
                ],
            ],
            [
                'name'                    => 'Siti Nurhaliza',
                'email'                   => 'siti.nurhaliza@gmail.com',
                'password'                => Hash::make('password'),
                'role'                    => 'user',
                'nik'                     => '3201055107900003',
                'phone'                   => '085533445566',
                'address'                 => 'Gang Annur 2 No. 12, RT 05 RW 01',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => true,
                'verified_at'             => now()->subMonths(2),
                'is_active'               => true,
                'email_verified_at'       => now()->subMonths(2),
                'notification_preferences' => [
                    'email_notifications'  => true,
                    'sms_notifications'    => false,
                    'push_notifications'   => false,
                    'complaint_updates'    => true,
                    'system_announcements' => true,
                    'marketing_emails'     => false,
                ],
            ],
            [
                'name'                    => 'Ahmad Fauzi',
                'email'                   => 'ahmad.fauzi@yahoo.com',
                'password'                => Hash::make('password'),
                'role'                    => 'user',
                'nik'                     => '3201051503920004',
                'phone'                   => '081399887766',
                'address'                 => 'Gang Annur 2 No. 18, RT 05 RW 01',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => false,
                'verified_at'             => null,
                'is_active'               => true,
                'email_verified_at'       => now()->subWeeks(2),
                'notification_preferences' => [
                    'email_notifications'  => false,
                    'sms_notifications'    => false,
                    'push_notifications'   => true,
                    'complaint_updates'    => true,
                    'system_announcements' => false,
                    'marketing_emails'     => false,
                ],
            ],
            [
                'name'                    => 'Rina Wulandari',
                'email'                   => 'rina.wulandari@gmail.com',
                'password'                => Hash::make('password'),
                'role'                    => 'user',
                'nik'                     => '3201054508880005',
                'phone'                   => '087722334455',
                'address'                 => 'Gang Annur 2 No. 7, RT 05 RW 01',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => true,
                'verified_at'             => now()->subMonth(),
                'is_active'               => true,
                'email_verified_at'       => now()->subMonth(),
                'notification_preferences' => [
                    'email_notifications'  => true,
                    'sms_notifications'    => false,
                    'push_notifications'   => true,
                    'complaint_updates'    => true,
                    'system_announcements' => true,
                    'marketing_emails'     => true,
                ],
            ],
            [
                'name'                    => 'Darmawan Susilo',
                'email'                   => 'darmawan.susilo@gmail.com',
                'password'                => Hash::make('password'),
                'role'                    => 'user',
                'nik'                     => '3201051107750006',
                'phone'                   => '089655443322',
                'address'                 => 'Gang Annur 2 No. 3, RT 05 RW 01',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => true,
                'verified_at'             => now()->subMonths(4),
                'is_active'               => false, // akun nonaktif
                'email_verified_at'       => now()->subMonths(4),
                'notification_preferences' => [
                    'email_notifications'  => false,
                    'sms_notifications'    => false,
                    'push_notifications'   => false,
                    'complaint_updates'    => false,
                    'system_announcements' => false,
                    'marketing_emails'     => false,
                ],
            ],
        ];

        $warga = [];
        foreach ($wargaData as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->syncRoles(['user']);
            $warga[] = $user;
        }

        [$budi, $siti, $ahmad, $rina, $darmawan] = $warga;

        // ── Categories ───────────────────────────────────────────────────────────
        $infrastruktur = Category::where('name', 'Infrastruktur')->first();
        $kebersihan    = Category::where('name', 'Kebersihan')->first();
        $keamanan      = Category::where('name', 'Keamanan')->first();
        $fasilitasUmum = Category::where('name', 'Fasilitas Umum')->first();
        $layananPublik = Category::where('name', 'Layanan Publik')->first();
        $sosial        = Category::where('name', 'Sosial & Kemasyarakatan')->first();
        $lainnya       = Category::where('name', 'Lainnya')->first();

        // ── Complaints ───────────────────────────────────────────────────────────
        $complaints = [
            // PENDING
            [
                'user_id'     => $budi->id,
                'title'       => 'Lampu Jalan Gang Annur 2 Mati',
                'description' => 'Lampu jalan di depan rumah no. 15 sudah padam sejak 5 hari lalu. Kondisi jalan sangat gelap pada malam hari dan sangat berbahaya bagi warga yang melintas. Mohon segera ditindaklanjuti.',
                'category_id' => $fasilitasUmum->id,
                'location'    => 'Gang Annur 2, depan no. 15, RT 05 RW 03',
                'status'      => 'pending',
                'priority'    => 'high',
                'report_date' => now()->subDays(5),
            ],
            [
                'user_id'     => $siti->id,
                'title'       => 'Drainase Tersumbat Depan Gang',
                'description' => 'Saluran air (got) di depan Gang Annur 2 tersumbat sampah dan lumpur. Saat hujan, air meluap ke jalan dan menggenangi halaman rumah warga. Sudah berlangsung lebih dari 1 minggu.',
                'category_id' => $infrastruktur->id,
                'location'    => 'Depan gang masuk Gang Annur 2, RT 05',
                'status'      => 'pending',
                'priority'    => 'medium',
                'report_date' => now()->subDays(7),
            ],
            [
                'user_id'     => $rina->id,
                'title'       => 'Pengemis Berkeliaran di Lingkungan RT',
                'description' => 'Dalam seminggu terakhir terdapat beberapa orang asing (pengemis) yang berkeliaran di lingkungan Gang Annur 2 hingga larut malam. Warga merasa kurang nyaman dan khawatir akan keamanan. Mohon pengurus RT menindaklanjuti.',
                'category_id' => $keamanan->id,
                'location'    => 'Seluruh area Gang Annur 2, RT 05 RW 03',
                'status'      => 'pending',
                'priority'    => 'urgent',
                'report_date' => now()->subDays(2),
            ],
            // IN PROGRESS
            [
                'user_id'     => $budi->id,
                'title'       => 'Sampah Menumpuk di TPS Sementara',
                'description' => 'Tempat pembuangan sampah sementara di ujung gang sudah melebihi kapasitas. Sampah menumpuk dan menimbulkan bau tidak sedap serta lalat. Warga sekitar sangat terganggu. Mohon segera dikoordinasikan jadwal pengangkutan.',
                'category_id' => $kebersihan->id,
                'location'    => 'Ujung Gang Annur 2, RT 05 RW 03',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'report_date' => now()->subDays(10),
            ],
            [
                'user_id'     => $ahmad->id,
                'title'       => 'Jalan Berlubang di Depan Masjid',
                'description' => 'Jalan depan masjid Gang Annur 2 rusak dengan lubang cukup besar berdiameter sekitar 50cm dan kedalaman 20cm. Sudah ada 2 kasus motor terjatuh. Sangat berbahaya terutama saat malam hari.',
                'category_id' => $infrastruktur->id,
                'location'    => 'Depan Masjid Al-Annur, Gang Annur 2, RT 05',
                'status'      => 'in_progress',
                'priority'    => 'urgent',
                'report_date' => now()->subDays(14),
                'estimated_resolution' => now()->addDays(7),
            ],
            // WAITING USER CONFIRMATION
            [
                'user_id'     => $siti->id,
                'title'       => 'Taman Bermain Anak Rusak',
                'description' => 'Ayunan dan perosotan di taman bermain RT 05 sudah rusak dan berkarat. Beberapa baut penguat sudah copot sehingga berbahaya untuk anak-anak. Mohon segera diperbaiki atau diganti.',
                'category_id' => $fasilitasUmum->id,
                'location'    => 'Taman RT 05, Gang Annur 2 RW 03',
                'status'      => 'in_progress',
                'priority'    => 'medium',
                'report_date' => now()->subDays(20),
                'estimated_resolution' => now()->subDays(3),
                'admin_resolved_at'   => now()->subDays(3),
            ],
            // RESOLVED
            [
                'user_id'     => $rina->id,
                'title'       => 'Penerangan Pos Ronda Mati',
                'description' => 'Lampu pos ronda RT 05 tidak menyala sejak 3 hari lalu. Kegiatan ronda malam terganggu karena pos menjadi gelap dan tidak nyaman untuk piket ronda.',
                'category_id' => $fasilitasUmum->id,
                'location'    => 'Pos Ronda RT 05, Gang Annur 2',
                'status'      => 'resolved',
                'priority'    => 'low',
                'report_date' => now()->subDays(30),
                'admin_resolved_at'  => now()->subDays(25),
                'user_resolved_at'   => now()->subDays(24),
                'resolved_at'        => now()->subDays(24),
                'resolved_by'        => 'user',
            ],
            [
                'user_id'     => $budi->id,
                'title'       => 'Pengurusan Surat Keterangan Domisili Lambat',
                'description' => 'Saya sudah mengajukan surat keterangan domisili 2 minggu lalu untuk keperluan daftar sekolah anak, namun belum ada kabar. Mohon dipercepat prosesnya.',
                'category_id' => $layananPublik->id,
                'location'    => 'Pos RT 05, Gang Annur 2',
                'status'      => 'resolved',
                'priority'    => 'medium',
                'report_date' => now()->subDays(18),
                'admin_resolved_at'  => now()->subDays(14),
                'user_resolved_at'   => now()->subDays(13),
                'resolved_at'        => now()->subDays(13),
                'resolved_by'        => 'admin',
            ],
            // REJECTED
            [
                'user_id'     => $ahmad->id,
                'title'       => 'Minta Dana Renovasi Rumah Pribadi',
                'description' => 'Rumah saya bocor parah saat hujan dan membutuhkan renovasi atap. Mengajukan permohonan dana bantuan dari kas RT untuk perbaikan rumah.',
                'category_id' => $lainnya->id,
                'location'    => 'Gang Annur 2 No. 18, RT 05 RW 03',
                'status'      => 'rejected',
                'priority'    => 'low',
                'report_date' => now()->subDays(25),
            ],
        ];

        $createdComplaints = [];
        foreach ($complaints as $data) {
            $createdComplaints[] = Complaint::create($data);
        }

        // ── Responses ─────────────────────────────────────────────────────────
        // in_progress: Sampah Menumpuk
        Response::create([
            'complaint_id' => $createdComplaints[3]->id,
            'user_id'      => $admin->id,
            'content'      => 'Laporan sudah kami terima. Kami telah berkoordinasi dengan Dinas Kebersihan untuk menambah frekuensi pengangkutan sampah di wilayah RT 05. Pengangkutan tambahan dijadwalkan besok pagi pukul 07.00 WIB.',
        ]);

        // in_progress: Jalan Berlubang
        Response::create([
            'complaint_id' => $createdComplaints[4]->id,
            'user_id'      => $admin->id,
            'content'      => 'Terima kasih laporannya. Kondisi jalan sudah kami laporkan ke Kelurahan dan Dinas PU setempat. Proses pengajuan anggaran perbaikan sedang berlangsung. Sementara ini sudah kami pasang tanda peringatan di sekitar lubang. Estimasi perbaikan 7-10 hari ke depan.',
        ]);

        // waiting_user_confirmation: Taman Bermain
        Response::create([
            'complaint_id' => $createdComplaints[5]->id,
            'user_id'      => $admin->id,
            'content'      => 'Perbaikan fasilitas taman bermain (ayunan dan perosotan) telah selesai dilakukan oleh tim pemeliharaan RT pada tanggal ' . now()->subDays(3)->format('d/m/Y') . '. Baut penguat sudah dipasang ulang dan bagian yang berkarat sudah dicat ulang. Mohon konfirmasi apakah perbaikan sudah sesuai.',
        ]);

        // resolved: Pos Ronda
        Response::create([
            'complaint_id' => $createdComplaints[6]->id,
            'user_id'      => $admin->id,
            'content'      => 'Lampu pos ronda sudah kami ganti dengan yang baru. Lampu LED 20 watt dipasang oleh tim listrik RT kemarin sore. Pos ronda kini sudah terang kembali. Terima kasih atas laporannya.',
        ]);

        // resolved: Surat Domisili
        Response::create([
            'complaint_id' => $createdComplaints[7]->id,
            'user_id'      => $admin->id,
            'content'      => 'Surat keterangan domisili atas nama Bapak Budi Santoso sudah selesai diproses dan dapat diambil di Pos RT setiap hari pukul 09.00 - 17.00 WIB. Mohon maaf atas keterlambatan sebelumnya.',
        ]);

        // rejected: Dana Renovasi
        Response::create([
            'complaint_id' => $createdComplaints[8]->id,
            'user_id'      => $admin->id,
            'content'      => 'Permohonan ini tidak dapat kami proses melalui sistem pengaduan RT. Dana kas RT diperuntukkan untuk kebutuhan fasilitas umum lingkungan, bukan renovasi rumah pribadi warga. Untuk bantuan renovasi rumah, disarankan menghubungi program RTLH (Rumah Tidak Layak Huni) di Kelurahan.',
        ]);

        // ── Announcements ────────────────────────────────────────────────────────
        $announcements = [
            [
                'title'           => 'Jadwal Gotong Royong Mingguan RT 05',
                'slug'            => 'jadwal-gotong-royong-mingguan-rt-05',
                'summary'         => 'Seluruh warga RT 05 diundang untuk hadir dalam kegiatan gotong royong setiap hari Minggu pagi pukul 07.00 WIB.',
                'content'         => "Assalamu'alaikum Wr. Wb.\n\nKepada seluruh warga RT 05 Gang Annur 2 yang kami hormati,\n\nDalam rangka menjaga kebersihan dan keindahan lingkungan kita bersama, pengurus RT 05 mengundang seluruh warga untuk berpartisipasi dalam kegiatan **Gotong Royong Mingguan** yang dilaksanakan setiap hari **Minggu pukul 07.00 - 09.00 WIB**.\n\nKegiatan meliputi:\n- Pembersihan selokan dan got\n- Penyapuan jalan lingkungan\n- Penataan tanaman dan taman RT\n- Perbaikan fasilitas kecil yang rusak\n\nMohon setiap kepala keluarga mengirimkan minimal 1 perwakilan. Bagi warga yang berhalangan hadir, dapat menggantikan dengan kontribusi konsumsi atau peralatan kebersihan.\n\nAtas partisipasi dan kerjasamanya, kami ucapkan terima kasih.\n\nSalam,\nPengurus RT 05 RW 03",
                'priority'        => 'medium',
                'target_audience' => ['all'],
                'is_active'       => true,
                'is_sticky'       => true,
                'allow_comments'  => true,
                'published_at'    => now()->subDays(7),
                'views_count'     => 42,
                'author_id'       => $admin->id,
            ],
            [
                'title'           => 'Rapat Warga Bulanan — Mei 2026',
                'slug'            => 'rapat-warga-bulanan-mei-2026',
                'summary'         => 'Rapat bulanan warga RT 05 akan diselenggarakan pada Rabu, 14 Mei 2026 pukul 19.30 WIB di Pos RT.',
                'content'         => "Kepada Yth.\nSeluruh Kepala Keluarga RT 05 RW 03\nGang Annur 2\n\nDengan hormat,\n\nBersama ini kami mengundang Bapak/Ibu untuk hadir dalam **Rapat Warga Bulanan** dengan ketentuan sebagai berikut:\n\n**Hari/Tanggal:** Rabu, 14 Mei 2026\n**Waktu:** Pukul 19.30 WIB s/d selesai\n**Tempat:** Pos RT 05, Gang Annur 2\n\n**Agenda Rapat:**\n1. Pembukaan\n2. Laporan kas RT bulan April 2026\n3. Pembahasan program kerja bulan Mei\n4. Evaluasi pengaduan warga bulan lalu\n5. Persiapan peringatan HUT RI ke-81\n6. Lain-lain\n\nDiharapkan seluruh kepala keluarga hadir tepat waktu. Kehadiran Bapak/Ibu sangat kami butuhkan demi kemajuan lingkungan kita bersama.\n\nHormat kami,\nKetua RT 05 RW 03",
                'priority'        => 'high',
                'target_audience' => ['all'],
                'is_active'       => true,
                'is_sticky'       => true,
                'allow_comments'  => false,
                'published_at'    => now()->subDays(3),
                'views_count'     => 61,
                'author_id'       => $admin->id,
            ],
            [
                'title'           => 'PENTING: Pemadaman Listrik Terjadwal PLN',
                'slug'            => 'penting-pemadaman-listrik-terjadwal-pln',
                'summary'         => 'PLN akan melakukan pemadaman listrik terjadwal di wilayah RT 05 pada Sabtu, 10 Mei 2026 pukul 08.00 - 17.00 WIB.',
                'content'         => "⚠️ **PEMBERITAHUAN PENTING**\n\nBerdasarkan informasi resmi dari PLN UP3 Bogor, akan dilakukan **pemadaman listrik terjadwal** dalam rangka pemeliharaan jaringan distribusi:\n\n**Tanggal:** Sabtu, 10 Mei 2026\n**Waktu:** Pukul 08.00 – 17.00 WIB\n**Wilayah:** Gang Annur 2 RT 05 RW 03 dan sekitarnya\n\n**Imbauan kepada warga:**\n- Simpan data pekerjaan penting sebelum listrik padam\n- Matikan peralatan elektronik sensitif (AC, kulkas) terlebih dahulu\n- Siapkan penerangan darurat (senter, lilin)\n- Cabut charger dan perangkat elektronik dari stop kontak\n- Gunakan air secukupnya (pompa air tidak dapat beroperasi)\n\nMohon maaf atas ketidaknyamanan ini. Informasi lebih lanjut dapat menghubungi call center PLN 123.\n\nSalam,\nPengurus RT 05",
                'priority'        => 'urgent',
                'target_audience' => ['all'],
                'is_active'       => true,
                'is_sticky'       => true,
                'allow_comments'  => true,
                'published_at'    => now()->subDay(),
                'views_count'     => 118,
                'author_id'       => $admin->id,
            ],
            [
                'title'           => 'Pendaftaran Lomba HUT RI ke-81 RT 05',
                'slug'            => 'pendaftaran-lomba-hut-ri-ke-81-rt-05',
                'summary'         => 'Daftarkan diri Anda dan keluarga untuk mengikuti berbagai lomba seru dalam rangka peringatan HUT RI ke-81 di RT 05.',
                'content'         => "🇮🇩 **PENDAFTARAN LOMBA 17 AGUSTUS — HUT RI KE-81**\n\nDalam rangka memperingati Hari Ulang Tahun Republik Indonesia ke-81, RT 05 RW 03 Gang Annur 2 akan menyelenggarakan berbagai kegiatan dan perlombaan seru untuk seluruh warga!\n\n**Lomba yang tersedia:**\n\n🧒 *Kategori Anak-anak (5-12 tahun):*\n- Balap karung\n- Makan kerupuk\n- Memasukkan pensil ke dalam botol\n- Tarik tambang anak\n\n👨 *Kategori Dewasa:*\n- Tarik tambang\n- Balap kelereng\n- Joget balon berpasangan\n\n**Cara Mendaftar:**\nHubungi Sekretaris RT (Pak Darmo) atau daftar langsung di Pos RT setiap hari pukul 16.00 - 20.00 WIB.\n\n**Hadiah menarik untuk setiap juara!**\n\nAyo warga RT 05, tunjukkan semangat kemerdekaan kita! 🎉",
                'priority'        => 'low',
                'target_audience' => ['all'],
                'is_active'       => true,
                'is_sticky'       => false,
                'allow_comments'  => true,
                'published_at'    => now()->subDays(14),
                'views_count'     => 73,
                'author_id'       => $admin->id,
            ],
            [
                'title'           => 'Iuran RT Bulan Mei 2026 — Harap Segera Dibayarkan',
                'slug'            => 'iuran-rt-bulan-mei-2026',
                'summary'         => 'Pengurus RT mengingatkan kembali kepada seluruh warga untuk segera membayar iuran RT bulan Mei 2026.',
                'content'         => "Kepada Yth.\nSeluruh Warga RT 05 RW 03\n\nDengan hormat,\n\nPengurus RT 05 mengingatkan kembali kepada seluruh warga untuk segera melunasi **iuran RT bulan Mei 2026**.\n\n**Rincian Iuran:**\n- Iuran kebersihan: Rp 15.000/bulan\n- Iuran keamanan (ronda): Rp 10.000/bulan\n- Iuran penerangan jalan: Rp 5.000/bulan\n- **Total: Rp 30.000/KK/bulan**\n\n**Cara Pembayaran:**\n- Langsung ke Bendahara RT (Bu Siti, rumah no. 8)\n- Transfer ke rekening RT: BRI 1234-5678-9012-3456 a.n. Kas RT 05\n\n**Batas waktu pembayaran:** 31 Mei 2026\n\nPembayaran iuran RT digunakan untuk pemeliharaan fasilitas umum, keamanan lingkungan, dan kegiatan sosial bersama. Ketersediaan dana RT sangat bergantung pada kepatuhan pembayaran iuran.\n\nAtas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.\n\nHormat kami,\nBendahara RT 05 RW 03",
                'priority'        => 'medium',
                'target_audience' => ['all'],
                'is_active'       => true,
                'is_sticky'       => false,
                'allow_comments'  => false,
                'published_at'    => now()->subDays(5),
                'views_count'     => 55,
                'author_id'       => $admin->id,
            ],
        ];

        $createdAnnouncements = [];
        foreach ($announcements as $data) {
            $createdAnnouncements[] = Announcement::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ── Comments on Announcements ─────────────────────────────────────────
        // Gotong royong
        $commentGR1 = Comment::create([
            'user_id'         => $budi->id,
            'announcement_id' => $createdAnnouncements[0]->id,
            'content'         => 'Siap hadir Pak RT! Kegiatan ini sangat bermanfaat untuk menjaga lingkungan kita tetap bersih dan indah. 👍',
            'is_approved'     => true,
        ]);
        Comment::create([
            'user_id'         => $admin->id,
            'announcement_id' => $createdAnnouncements[0]->id,
            'content'         => 'Terima kasih Pak Budi, semangat! Sampai Minggu pagi ya.',
            'is_approved'     => true,
            'parent_id'       => $commentGR1->id,
        ]);
        Comment::create([
            'user_id'         => $siti->id,
            'announcement_id' => $createdAnnouncements[0]->id,
            'content'         => 'Saya akan ikut membawa sapu dan sekop. Semangat warga RT 05! 💪',
            'is_approved'     => true,
        ]);
        Comment::create([
            'user_id'         => $rina->id,
            'announcement_id' => $createdAnnouncements[0]->id,
            'content'         => 'Apakah disediakan konsumsi Pak RT? Supaya lebih semangat hehe 😄',
            'is_approved'     => true,
        ]);
        Comment::create([
            'user_id'         => $admin->id,
            'announcement_id' => $createdAnnouncements[0]->id,
            'content'         => 'Insya Allah ada snack dan minuman ringan Bu Rina. Mohon kehadirannya ya!',
            'is_approved'     => true,
            'parent_id'       => Comment::where('user_id', $rina->id)->latest()->first()->id,
        ]);

        // Pemadaman listrik
        Comment::create([
            'user_id'         => $siti->id,
            'announcement_id' => $createdAnnouncements[2]->id,
            'content'         => 'Pak RT, apakah generator RT bisa dinyalakan untuk masjid selama pemadaman?',
            'is_approved'     => true,
        ]);
        Comment::create([
            'user_id'         => $admin->id,
            'announcement_id' => $createdAnnouncements[2]->id,
            'content'         => 'Insya Allah akan kami usahakan Bu Siti. Kami akan koordinasikan dengan pengurus masjid untuk genset.',
            'is_approved'     => true,
            'parent_id'       => Comment::where('user_id', $siti->id)->latest()->first()->id,
        ]);
        Comment::create([
            'user_id'         => $budi->id,
            'announcement_id' => $createdAnnouncements[2]->id,
            'content'         => 'Terima kasih informasinya Pak RT. Sangat membantu untuk persiapan kami.',
            'is_approved'     => true,
        ]);

        // Lomba 17 Agustus
        Comment::create([
            'user_id'         => $rina->id,
            'announcement_id' => $createdAnnouncements[3]->id,
            'content'         => 'Waaah seru banget! Anak saya pasti mau ikut lomba balap karung. Daftar ke mana ya Pak RT?',
            'is_approved'     => true,
        ]);
        Comment::create([
            'user_id'         => $admin->id,
            'announcement_id' => $createdAnnouncements[3]->id,
            'content'         => 'Daftar langsung ke Pos RT Bu Rina, setiap sore pukul 16.00-20.00 WIB. Ditunggu ya!',
            'is_approved'     => true,
            'parent_id'       => Comment::where('user_id', $rina->id)->latest()->first()->id,
        ]);

        // ── Bookmarks ─────────────────────────────────────────────────────────
        $budi->bookmarkedAnnouncements()->syncWithoutDetaching([
            $createdAnnouncements[2]->id, // pemadaman listrik
            $createdAnnouncements[1]->id, // rapat bulanan
        ]);
        $siti->bookmarkedAnnouncements()->syncWithoutDetaching([
            $createdAnnouncements[0]->id, // gotong royong
            $createdAnnouncements[2]->id, // pemadaman listrik
            $createdAnnouncements[4]->id, // iuran RT
        ]);
        $rina->bookmarkedAnnouncements()->syncWithoutDetaching([
            $createdAnnouncements[3]->id, // lomba HUT RI
            $createdAnnouncements[2]->id, // pemadaman listrik
        ]);
        $ahmad->bookmarkedAnnouncements()->syncWithoutDetaching([
            $createdAnnouncements[1]->id, // rapat bulanan
        ]);
    }
}
