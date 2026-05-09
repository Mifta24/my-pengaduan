<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Infrastruktur',
                'description' => 'Laporan terkait jalan, jembatan, drainase, got, dan infrastruktur umum lingkungan',
                'icon'        => 'hammer',
                'color'       => 'blue',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kebersihan',
                'description' => 'Laporan terkait sampah, kebersihan lingkungan, sanitasi, dan pembuangan limbah',
                'icon'        => 'trash-2',
                'color'       => 'green',
                'is_active'   => true,
            ],
            [
                'name'        => 'Keamanan',
                'description' => 'Laporan terkait keamanan lingkungan, pencurian, gangguan ketertiban, dan tindak kejahatan',
                'icon'        => 'shield',
                'color'       => 'red',
                'is_active'   => true,
            ],
            [
                'name'        => 'Fasilitas Umum',
                'description' => 'Laporan terkait taman, lampu jalan, pos ronda, MCK umum, dan fasilitas umum lainnya',
                'icon'        => 'building-2',
                'color'       => 'purple',
                'is_active'   => true,
            ],
            [
                'name'        => 'Layanan Publik',
                'description' => 'Laporan terkait pelayanan administrasi RT, surat menyurat, dan pelayanan warga',
                'icon'        => 'users',
                'color'       => 'orange',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sosial & Kemasyarakatan',
                'description' => 'Laporan terkait permasalahan sosial, kerukunan warga, dan kegiatan kemasyarakatan',
                'icon'        => 'heart-handshake',
                'color'       => 'pink',
                'is_active'   => true,
            ],
            [
                'name'        => 'Lainnya',
                'description' => 'Laporan lainnya yang tidak masuk kategori di atas',
                'icon'        => 'more-horizontal',
                'color'       => 'gray',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
