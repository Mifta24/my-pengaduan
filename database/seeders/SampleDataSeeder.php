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
        $admin = User::where('email', 'admin@rt.com')->first();
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
                'location' => 'Jl. Mawar No. 15, RT 01/RW 01',
                'status' => 'pending',
                'report_date' => now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Sampah Menumpuk',
                'description' => 'Tempat pembuangan sampah di ujung gang sudah penuh dan menumpuk. Bau tidak sedap mulai tercium.',
                'category_id' => $kebersihan->id,
                'location' => 'Gang Melati, RT 01/RW 01',
                'status' => 'processing',
                'response' => 'Laporan sudah diterima. Tim kebersihan akan datang besok pagi.',
                'report_date' => now()->subDays(1),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Jalan Berlubang',
                'description' => 'Jalan di depan RT rusak dan berlubang besar. Berbahaya untuk kendaraan yang lewat.',
                'category_id' => $infrastruktur->id,
                'location' => 'Jl. Kenanga, RT 01/RW 01',
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
                'content' => 'Mengundang seluruh warga RT 01 untuk mengikuti gotong royong setiap hari Minggu pagi mulai pukul 07.00 WIB. Mari bersama-sama menjaga kebersihan lingkungan kita.',
                'date' => now()->addDays(2),
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Rapat RT Bulanan',
                'content' => 'Rapat RT akan dilaksanakan pada hari Rabu, 15 Oktober 2025 pukul 19.30 WIB di Balai RT. Agenda: pembahasan iuran RT dan program kerja bulan depan.',
                'date' => now()->addWeek(),
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Peringatan Hari Kemerdekaan',
                'content' => 'Dalam rangka memperingati HUT RI ke-80, RT 01 akan mengadakan lomba untuk anak-anak dan dewasa. Daftarkan diri Anda di Ketua RT.',
                'date' => now()->subDays(30),
                'created_by' => $admin->id,
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
