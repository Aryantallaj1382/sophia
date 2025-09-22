<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_age_groups_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('age_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // مثلا کودکان، نوجوانان، بزرگسالان
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('age_groups');
    }
};
