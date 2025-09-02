<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // NIP/NIK
            if (!Schema::hasColumn('users', 'nip_nik')) {
                $table->string('nip_nik')->nullable()->after('name');
            }
            
            // Foto
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('nip_nik');
            }
            
            // Alamat
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('photo');
            }
            
            // Status
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif')->after('address');
            }
            
            // Walikelas (hanya untuk guru)
            if (!Schema::hasColumn('users', 'is_walikelas')) {
                $table->boolean('is_walikelas')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip_nik', 'photo', 'address', 'status', 'is_walikelas']);
        });
    }
};
