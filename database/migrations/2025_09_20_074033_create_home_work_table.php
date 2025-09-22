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
        Schema::create('report_home_work', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('answer', 255);
            $table->enum('status', ['yes', 'no'])->nullable();
            $table->boolean('doing_homework')->default(0)->nullable();
            $table->foreignId('report_registration_id')->constrained('report_registration')->onDelete('cascade');
            $table->boolean('is_reading')->default(0)->nullable();
            $table->timestamps();

            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_work');
    }
};
