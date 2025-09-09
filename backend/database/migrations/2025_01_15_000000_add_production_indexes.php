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
        // Add indexes for better performance
        Schema::table('attendance', function (Blueprint $table) {
            $table->index(['user_id', 'timestamp'], 'idx_attendance_user_timestamp');
            $table->index(['timestamp'], 'idx_attendance_timestamp');
            $table->index(['status'], 'idx_attendance_status');
        });

        Schema::table('student_attendance', function (Blueprint $table) {
            $table->index(['student_id', 'created_at'], 'idx_student_attendance_student_created');
            $table->index(['created_at'], 'idx_student_attendance_created');
            $table->index(['status'], 'idx_student_attendance_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['email'], 'idx_users_email');
            $table->index(['role_id'], 'idx_users_role_id');
            $table->index(['status'], 'idx_users_status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['class_room_id'], 'idx_students_class_room_id');
            $table->index(['status'], 'idx_students_status');
            $table->index(['nisn'], 'idx_students_nisn');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_leaves_user_status');
            $table->index(['status'], 'idx_leaves_status');
            $table->index(['start_date', 'end_date'], 'idx_leaves_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_user_timestamp');
            $table->dropIndex('idx_attendance_timestamp');
            $table->dropIndex('idx_attendance_status');
        });

        Schema::table('student_attendance', function (Blueprint $table) {
            $table->dropIndex('idx_student_attendance_student_created');
            $table->dropIndex('idx_student_attendance_created');
            $table->dropIndex('idx_student_attendance_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_role_id');
            $table->dropIndex('idx_users_status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_class_room_id');
            $table->dropIndex('idx_students_status');
            $table->dropIndex('idx_students_nisn');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropIndex('idx_leaves_user_status');
            $table->dropIndex('idx_leaves_status');
            $table->dropIndex('idx_leaves_dates');
        });
    }
};
