<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view-complaints',
            'create-complaints',
            'edit-complaints',
            'delete-complaints',
            'manage-users',
            'create-announcements',
            'edit-announcements',
            'delete-announcements',
            'view-dashboard',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles - RT sebagai admin, warga sebagai user
        $rtRole = Role::firstOrCreate(['name' => 'admin']);
        $wargaRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to RT (admin)
        $rtRole->givePermissionTo(Permission::all());

        // Give limited permissions to warga (user)
        $wargaRole->givePermissionTo([
            'view-complaints',
            'create-complaints',
            'edit-complaints'
        ]);

        // Create RT (admin) user
        $rt = User::firstOrCreate(
            ['email' => 'rt@rt.com'],
            [
                'name' => 'Ketua RT',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'address' => 'Kantor RT 01/RW 01'
            ]
        );

        $rt->assignRole('admin');

        // Create warga (user) users
        $warga1 = User::firstOrCreate(
            ['email' => 'warga1@test.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Jl. Mawar No. 15, RT 01/RW 01'
            ]
        );
        $warga1->assignRole('user');

        $warga2 = User::firstOrCreate(
            ['email' => 'warga2@test.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Jl. Melati No. 8, RT 01/RW 01'
            ]
        );
        $warga2->assignRole('user');

        $warga3 = User::firstOrCreate(
            ['email' => 'warga3@test.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Jl. Kenanga No. 23, RT 01/RW 01'
            ]
        );
        $warga3->assignRole('user');
    }
}
