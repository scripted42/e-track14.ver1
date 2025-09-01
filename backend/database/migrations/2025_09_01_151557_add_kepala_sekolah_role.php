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
        // Insert Kepala Sekolah role if it doesn't exist
        DB::table('roles')->insertOrIgnore([
            [
                'id' => 2,
                'role_name' => 'Kepala Sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
        // Update role hierarchy: move existing roles to accommodate Kepala Sekolah
        // Update Guru from id 2 to id 4 (if it exists)
        DB::statement("UPDATE roles SET id = 99 WHERE role_name = 'Guru' AND id = 2");
        DB::statement("UPDATE users SET role_id = 99 WHERE role_id = 2");
        
        // Insert/Update roles with proper hierarchy
        DB::table('roles')->insertOrIgnore([
            [
                'id' => 3,
                'role_name' => 'Waka Kurikulum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'role_name' => 'Guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'role_name' => 'Pegawai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'role_name' => 'Siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
        // Update users from temporary id 99 to new id 4
        DB::statement("UPDATE users SET role_id = 4 WHERE role_id = 99");
        
        // Delete temporary role
        DB::statement("DELETE FROM roles WHERE id = 99");
        
        // Update existing role names to match new structure
        DB::table('roles')->where('role_name', 'Waka Kurikulum')->where('id', '!=', 3)->delete();
        DB::table('roles')->where('role_name', 'Pegawai')->where('id', '!=', 5)->delete();
        DB::table('roles')->where('role_name', 'Siswa')->where('id', '!=', 6)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Kepala Sekolah role and revert changes
        DB::table('roles')->where('role_name', 'Kepala Sekolah')->delete();
        
        // Note: This is a simplified rollback - in production you might want to
        // preserve user role assignments or handle them more carefully
    }
};
