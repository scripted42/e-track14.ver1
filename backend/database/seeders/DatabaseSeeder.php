<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert roles (idempotent)
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'role_name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_name' => 'Kepala Sekolah', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_name' => 'Waka Kurikulum', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'role_name' => 'Guru', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'role_name' => 'Pegawai', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'role_name' => 'Siswa', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default users (idempotent by email)
        DB::table('users')->insertOrIgnore([
            [
                'name' => 'Administrator',
                'email' => 'admin@smpn14.sch.id',
                'password' => Hash::make('admin123'),
                'role_id' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@smpn14.sch.id',
                'password' => Hash::make('kepsek123'),
                'role_id' => 2,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guru Matematika',
                'email' => 'guru@smpn14.sch.id',
                'password' => Hash::make('guru123'),
                'role_id' => 4,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert default settings (only if empty)
        if (DB::table('settings')->count() === 0) {
            DB::table('settings')->insert([
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'radius' => 100,
                'checkin_start' => '07:00:00',
                'checkin_end' => '08:00:00',
                'checkout_start' => '15:00:00',
                'checkout_end' => '17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert sample students (idempotent by card_qr_code)
        DB::table('students')->insertOrIgnore([
            [
                'name' => 'Ahmad Fauzi',
                'class_name' => '7A',
                'card_qr_code' => 'STU001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'class_name' => '7A',
                'card_qr_code' => 'STU002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'class_name' => '8B',
                'card_qr_code' => 'STU003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seed Spatie roles & permissions
        $permissions = [
            'attendance.view_all',
            'attendance.manage',
            'leave.approve',
            'leave.reject',
            'leave.view_all',
            'report.view',
            'report.view_all',
            'student.manage',
            'staff.manage',
            'settings.manage',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $rolePermissions = [
            'Admin' => $permissions,
            'Kepala Sekolah' => ['attendance.view_all','leave.approve','leave.reject','report.view_all','student.manage'],
            // Waka - Divisional
            'Waka Kesiswaan' => ['report.view_all','student.manage'],
            'Waka Kurikulum' => ['report.view_all'],
            'Waka Kehumasan' => ['report.view_all'],
            'Waka Sarpras' => ['report.view_all'],
            // Bendahara
            'Bendahara 1' => ['report.view'],
            'Bendahara 2' => ['report.view'],
            // Tata Usaha & Subordinates
            'Tata Usaha' => ['report.view'],
            'Koordinator TU' => ['report.view'],
            'Staff TU' => ['report.view'],
            'Keamanan' => ['report.view'],
            'Kebersihan' => ['report.view'],
            // Academic roles
            'Guru' => ['report.view'],
            'Pegawai' => ['report.view'],
            // Staff divisions
            'Staff Kesiswaan' => ['report.view'],
            'Staff Kurikulum' => ['report.view'],
            'Staff Kehumasan' => ['report.view'],
            'Staff Sarpras' => ['report.view'],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if (!$role) {
                // roles table adalah tabel lama (memiliki kolom role_name non-nullable), isi keduanya
                \Illuminate\Support\Facades\DB::table('roles')->insert([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'role_name' => $roleName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            }
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}