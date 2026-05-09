<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ─────────────────────────────────────────────────────────
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

        // ── Roles ────────────────────────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        $adminRole->syncPermissions(Permission::all());
        $userRole->syncPermissions(['view-complaints', 'create-complaints', 'edit-complaints']);

        // ── Admin (Ketua RT) ─────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@mypengaduan.com'],
            [
                'name'                    => 'Ketua RT',
                'password'                => Hash::make('password'),
                'role'                    => 'admin',
                'nik'                     => '3201051505700001',
                'phone'                   => '081234567890',
                'address'                 => 'Gang Annur 2 RT 05 / RW 01, Poris Plawad Utara, Cipondoh, Tangerang',
                'rt_number'               => '005',
                'rw_number'               => '001',
                'is_verified'             => true,
                'verified_at'             => now()->subMonths(6),
                'is_active'               => true,
                'email_verified_at'       => now()->subMonths(6),
                'notification_preferences' => [
                    'email_notifications'  => true,
                    'sms_notifications'    => false,
                    'push_notifications'   => true,
                    'complaint_updates'    => true,
                    'system_announcements' => true,
                    'marketing_emails'     => false,
                ],
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
