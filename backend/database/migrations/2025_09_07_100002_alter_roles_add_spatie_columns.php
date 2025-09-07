<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'name')) {
                $table->string('name')->nullable()->after('role_name');
            }
            if (!Schema::hasColumn('roles', 'guard_name')) {
                $table->string('guard_name')->default('web')->after('name');
            }
        });

        // Backfill name from role_name
        DB::table('roles')->update([
            'name' => DB::raw('COALESCE(name, role_name)')
        ]);

        // Add unique index for (name, guard_name)
        Schema::table('roles', function (Blueprint $table) {
            try {
                $table->unique(['name','guard_name']);
            } catch (\Throwable $e) {
                // ignore if exists
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'guard_name')) {
                $table->dropColumn('guard_name');
            }
            if (Schema::hasColumn('roles', 'name')) {
                try { $table->dropUnique('roles_name_guard_name_unique'); } catch (\Throwable $e) {}
                $table->dropColumn('name');
            }
        });
    }
};


