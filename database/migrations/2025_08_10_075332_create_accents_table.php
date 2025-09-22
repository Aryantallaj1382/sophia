<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_accents_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('accents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('accents');
    }
};

