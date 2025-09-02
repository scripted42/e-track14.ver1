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
        Schema::table('students', function (Blueprint $table) {
            // Add NISN if it doesn't exist
            if (!Schema::hasColumn('students', 'nisn')) {
                $table->string('nisn', 20)->nullable()->after('id');
            }
            
            // Add photo if it doesn't exist
            if (!Schema::hasColumn('students', 'photo')) {
                $table->string('photo')->nullable()->after('name');
            }
            
            // Add address if it doesn't exist
            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable()->after('class_name');
            }
            
            // Add status if it doesn't exist
            if (!Schema::hasColumn('students', 'status')) {
                $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif')->after('card_qr_code');
            }
        });
        
        // Update existing students with default NISN values if NISN is null
        DB::table('students')->whereNull('nisn')->update(['nisn' => DB::raw('CONCAT("NISN", LPAD(id, 6, "0"))')]);
        
        // Make NISN unique if it's not already
        if (!Schema::hasColumn('students', 'nisn')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('nisn', 20)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nisn', 'photo', 'address', 'status']);
        });
    }
};
