<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Announcement;
use App\Models\Response;
use App\Models\Category;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@Lurah.com')->first();
        $user = User::where('email', 'warga@test.com')->first();

        // Get categories
        $infrastruktur = Category::where('name', 'Infrastruktur')->first();
        $kebersihan = Category::where('name', 'Kebersihan')->first();

        // Create sample complaints
        $complaints = [
            [
                'user_id' => $user->id,
                'title' => 'Lampu Jalan Rusak',
                'description' => 'Lampu jalan di depan rumah no. 15 sudah mati sejak 3 hari lalu. Mohon segera diperbaiki karena membuat jalan gelap dan tidak aman.',
                'category_id' => $infrastruktur->id,
                'location' => 'Jl. Mawar No. 15, Lurah 01/RW 01',
                'status' => 'pending',
                'report_date' => now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Sampah Menumpuk',
                'description' => 'Tempat pembuangan sampah di ujung gang sudah penuh dan menumpuk. Bau tidak sedap mulai tercium.',
                'category_id' => $kebersihan->id,
                'location' => 'Gang Melati, Lurah 01/RW 01',
                'status' => 'processing',
                'response' => 'Laporan sudah diterima. Tim kebersihan akan datang besok pagi.',
                'report_date' => now()->subDays(1),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Jalan Berlubang',
                'description' => 'Jalan di depan Lurah rusak dan berlubang besar. Berbahaya untuk kendaraan yang lewat.',
                'category_id' => $infrastruktur->id,
                'location' => 'Jl. Kenanga, Lurah 01/RW 01',
                'status' => 'completed',
                'response' => 'Jalan sudah diperbaiki oleh tim infrastruktur. Terima kasih atas laporannya.',
                'report_date' => now()->subWeek(),
            ]
        ];

        foreach ($complaints as $complaintData) {
            Complaint::create($complaintData);
        }

        // Create sample announcements
        $announcements = [
            [
                'title' => 'Gotong Royong Mingguan',
                'slug' => 'gotong-royong-mingguan',
                'summary' => 'Mengundang seluruh warga Lurah 01 untuk mengikuti gotong royong setiap hari Minggu pagi.',
                'content' => 'Mengundang seluruh warga Lurah 01 untuk mengikuti gotong royong setiap hari Minggu pagi mulai pukul 07.00 WIB. Mari bersama-sama menjaga kebersihan lingkungan kita. Acara akan dimulai dengan berkumpul di balai Lurah kemudian melakukan kerja bakti bersama.',
                'priority' => 'high',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => true,
                'allow_comments' => true,
                'published_at' => now()->subDays(1),
                'views_count' => 15,
                'author_id' => $admin->id,
            ],
            [
                'title' => 'Rapat Lurah Bulanan',
                'slug' => 'rapat-Lurah-bulanan',
                'summary' => 'Rapat Lurah akan dilaksanakan pada hari Rabu, 15 Oktober 2025 pukul 19.30 WIB.',
                'content' => 'Rapat Lurah akan dilaksanakan pada hari Rabu, 15 Oktober 2025 pukul 19.30 WIB di Balai Lurah. Agenda: pembahasan iuran Lurah dan program kerja bulan depan. Diharapkan seluruh warga dapat hadir tepat waktu.',
                'priority' => 'medium',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => false,
                'allow_comments' => true,
                'published_at' => now()->subHours(12),
                'views_count' => 23,
                'author_id' => $admin->id,
            ],
            [
                'title' => 'Peringatan Hari Kemerdekaan',
                'slug' => 'peringatan-hari-kemerdekaan',
                'summary' => 'Lurah 01 akan mengadakan lomba untuk anak-anak dan dewasa dalam rangka HUT RI.',
                'content' => 'Dalam rangka memperingati HUT RI ke-80, Lurah 01 akan mengadakan lomba untuk anak-anak dan dewasa. Lomba meliputi balap karung, makan kerupuk, dan panjat pinang. Daftarkan diri Anda di Ketua Lurah. Hadiah menarik menanti para pemenang!',
                'priority' => 'low',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => false,
                'allow_comments' => true,
                'published_at' => now()->subDays(30),
                'views_count' => 47,
                'author_id' => $admin->id,
            ],
            [
                'title' => 'Pengumuman Penting: Pemadaman Listrik',
                'slug' => 'pengumuman-penting-pemadaman-listrik',
                'summary' => 'PLN akan melakukan pemadaman listrik bergilir di wilayah Lurah 01.',
                'content' => 'Berdasarkan informasi dari PLN, akan dilakukan pemadaman listrik bergilir di wilayah Lurah 01 pada hari Sabtu, 12 Oktober 2025 mulai pukul 08.00 - 16.00 WIB. Mohon maaf atas ketidaknyamanan ini. Harap persiapkan kebutuhan selama pemadaman.',
                'priority' => 'urgent',
                'target_audience' => ['all'],
                'is_active' => true,
                'is_sticky' => true,
                'allow_comments' => true,
                'published_at' => now()->subHours(6),
                'views_count' => 89,
                'author_id' => $admin->id,
            ]
        ];

        foreach ($announcements as $announcementData) {
            Announcement::create($announcementData);
        }

        // Create sample responses
        $complaint = Complaint::where('status', 'completed')->first();
        if ($complaint) {
            Response::create([
                'complaint_id' => $complaint->id,
                'user_id' => $admin->id,
                'content' => 'Perbaikan jalan telah selesai dilakukan. Tim infrastruktur telah menambal lubang dan menghaluskan permukaan jalan.',
            ]);
        }
    }
}
