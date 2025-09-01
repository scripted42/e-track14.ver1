<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['checkin', 'checkout']);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy', 5, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedBigInteger('qr_token_id')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'cuti', 'dinas_luar'])->default('hadir');
            $table->datetime('timestamp');
            $table->date('date_only')->storedAs('DATE(timestamp)');
            $table->boolean('synced')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'date_only']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance');
    }
};