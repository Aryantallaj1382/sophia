<?php


// database/migrations/xxxx_xx_xx_create_profesoor_accent_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_professor_accent_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('professor_accent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->foreignId('accent_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('professor_accent');
    }
};

