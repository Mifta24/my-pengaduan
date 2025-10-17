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

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Give limited permissions to user
        $userRole->givePermissionTo([
            'view-complaints',
            'create-complaints',
            'edit-complaints'
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@Lurah.com'],
            [
                'name' => 'Admin Lurah',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'address' => 'Kantor Lurah 01/RW 01'
            ]
        );

        $admin->assignRole('admin');

        // Create test user
        $user = User::firstOrCreate(
            ['email' => 'warga@test.com'],
            [
                'name' => 'Warga Test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Jl. Test No. 123, Lurah 01/RW 01'
            ]
        );

        $user->assignRole('user');
    }
}
