<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert roles
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_name' => 'Kepala Sekolah', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_name' => 'Waka Kurikulum', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'role_name' => 'Guru', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'role_name' => 'Pegawai', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'role_name' => 'Siswa', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default users
        DB::table('users')->insert([
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

        // Insert default settings
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

        // Insert sample students
        DB::table('students')->insert([
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
    }
}