<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely ensure required roles exist without changing primary keys
        DB::table('roles')->insertOrIgnore([
            [
                'role_name' => 'Kepala Sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Waka Kurikulum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Pegawai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Kepala Sekolah role only
        DB::table('roles')->where('role_name', 'Kepala Sekolah')->delete();
    }
};
