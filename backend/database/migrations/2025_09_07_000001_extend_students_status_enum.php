<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('students', 'status')) {
            // Ubah enum menjadi set nilai yang lebih lengkap
            // Catatan: Perubahan enum tergantung DBMS. Pada MySQL, gunakan MODIFY.
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `students` MODIFY `status` ENUM('Aktif','Non-Aktif','Lulus','Pindah','Drop Out','Tidak Naik Kelas') NOT NULL DEFAULT 'Aktif'");
            } else {
                // Fallback untuk sqlite/pgsql: ubah ke string biasa agar fleksibel
                Schema::table('students', function (Blueprint $table) {
                    $table->string('status', 50)->default('Aktif')->change();
                });
            }
        }

        // Tambah kolom tanggal kelulusan jika belum ada
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'graduation_date')) {
                $table->date('graduation_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'previous_class')) {
                $table->string('previous_class', 100)->nullable()->after('class_name');
            }
            if (!Schema::hasColumn('students', 'transfer_date')) {
                $table->date('transfer_date')->nullable()->after('graduation_date');
            }
            if (!Schema::hasColumn('students', 'dropout_date')) {
                $table->date('dropout_date')->nullable()->after('transfer_date');
            }
            if (!Schema::hasColumn('students', 'academic_year')) {
                $table->string('academic_year', 20)->nullable()->after('dropout_date');
            }
        });
    }

    public function down(): void
    {
        // Tidak mengembalikan enum ke kondisi awal untuk menghindari kehilangan data
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'graduation_date')) {
                $table->dropColumn('graduation_date');
            }
            if (Schema::hasColumn('students', 'previous_class')) {
                $table->dropColumn('previous_class');
            }
            if (Schema::hasColumn('students', 'transfer_date')) {
                $table->dropColumn('transfer_date');
            }
            if (Schema::hasColumn('students', 'dropout_date')) {
                $table->dropColumn('dropout_date');
            }
            if (Schema::hasColumn('students', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
        });
    }
};


