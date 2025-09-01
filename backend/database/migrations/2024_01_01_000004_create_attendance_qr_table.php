<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_qr', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code');
            $table->datetime('valid_until');
            $table->timestamps();
            
            $table->index('valid_until');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_qr');
    }
};