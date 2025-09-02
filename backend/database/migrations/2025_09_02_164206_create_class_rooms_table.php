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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kelas (contoh: 7A, 8B, 9C)
            $table->string('level'); // Tingkat (contoh: 7, 8, 9)
            $table->string('description')->nullable(); // Deskripsi kelas
            $table->unsignedBigInteger('walikelas_id')->nullable(); // ID guru yang menjadi walikelas
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('walikelas_id')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint untuk nama kelas
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
