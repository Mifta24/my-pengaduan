<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // roles, permissions, admin user
            CategorySeeder::class,       // kategori pengaduan
            SampleDataSeeder::class,     // warga, keluhan, pengumuman, komentar
        ]);
    }
}
