<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_platforms_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon')->nullable(); // آدرس یا نام آیکون
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('platforms');
    }
};
