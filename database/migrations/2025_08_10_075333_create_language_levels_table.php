<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_language_levels_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('language_levels', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // مثلا A1, B1, C1
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('language_levels');
    }
};
