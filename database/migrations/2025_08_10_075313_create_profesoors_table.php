<?php

// database/migrations/xxxx_xx_xx_create_profesoors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // یک به یک با یوزر
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_native')->default(false);
            $table->decimal('value', 8, 2)->nullable(); // مثلا قیمت یا امتیاز
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('sample_video')->nullable();
            $table->string('sample_video_cover')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('id_card')->nullable(); // مدرک شناسایی (فایل یا شماره)
            $table->date('birth_date')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('professors');
    }
};

