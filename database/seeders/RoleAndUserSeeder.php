<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $rtRole = Role::firstOrCreate(['name' => 'Lurah']);
        $rwRole = Role::firstOrCreate(['name' => 'rw']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-complaints',
            'manage-categories',
            'manage-announcements',
            'view-reports',
            'create-complaints',
            'view-complaints',
            'comment-announcements'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $rtRole->givePermissionTo(['manage-complaints', 'manage-announcements', 'view-reports', 'comment-announcements']);
        $rwRole->givePermissionTo(['manage-complaints', 'manage-announcements', 'view-reports', 'comment-announcements']);
        $userRole->givePermissionTo(['create-complaints', 'view-complaints', 'comment-announcements']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@mypengaduan.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'Lurah' => '001',
                'rw' => '003'
            ]
        );
        $admin->assignRole('admin');

        // Create Lurah user
        $Lurah = User::firstOrCreate(
            ['email' => 'Lurah@mypengaduan.com'],
            [
                'name' => 'Ketua Lurah 001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'Lurah' => '001',
                'rw' => '003'
            ]
        );
        $Lurah->assignRole('Lurah');

        // Create RW user
        $rw = User::firstOrCreate(
            ['email' => 'rw@mypengaduan.com'],
            [
                'name' => 'Ketua RW 003',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'Lurah' => '001',
                'rw' => '003'
            ]
        );
        $rw->assignRole('rw');

        // Create regular users
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'Lurah' => '001',
                'rw' => '003'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'Lurah' => '001',
                'rw' => '003'
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'Lurah' => '001',
                'rw' => '003'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()
                ])
            );
            $user->assignRole('user');
        }
    }
}
