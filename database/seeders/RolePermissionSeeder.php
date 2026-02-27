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

        // Create roles - Admin RT dan Warga
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
                'address' => 'Pos RT 05, Gang Annur 2'
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
                'address' => 'Gang Annur 2 RT 05'
            ]
        );
        $warga1->assignRole('user');

        $warga2 = User::firstOrCreate(
            ['email' => 'warga2@test.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Gang Annur 2 RT 05'
            ]
        );
        $warga2->assignRole('user');

        $warga3 = User::firstOrCreate(
            ['email' => 'warga3@test.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Gang Annur 2 RT 05'
            ]
        );
        $warga3->assignRole('user');
    }
}
