<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Infrastruktur',
                'description' => 'Laporan terkait jalan, jembatan, drainase, dan infrastruktur umum',
                'icon' => 'hammer',
                'color' => 'blue',
                'is_active' => true,
            ],
            [
                'name' => 'Kebersihan',
                'description' => 'Laporan terkait sampah, kebersihan lingkungan, dan sanitasi',
                'icon' => 'trash-2',
                'color' => 'green',
                'is_active' => true,
            ],
            [
                'name' => 'Keamanan',
                'description' => 'Laporan terkait keamanan, pencurian, dan ketertiban',
                'icon' => 'shield',
                'color' => 'red',
                'is_active' => true,
            ],
            [
                'name' => 'Fasilitas Umum',
                'description' => 'Laporan terkait taman, lampu jalan, dan fasilitas umum lainnya',
                'icon' => 'building-2',
                'color' => 'purple',
                'is_active' => true,
            ],
            [
                'name' => 'Layanan Publik',
                'description' => 'Laporan terkait pelayanan RT dan administrasi',
                'icon' => 'users',
                'color' => 'orange',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Laporan lainnya yang tidak masuk kategori di atas',
                'icon' => 'more-horizontal',
                'color' => 'gray',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
