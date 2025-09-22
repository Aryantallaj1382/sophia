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
        Schema::table('exams', function (Blueprint $table) {
            $table->string('type')->default('mock'); // mock, final, placement
            $table->unsignedBigInteger('age_group_id')->nullable(); // برای آزمون تعیین سطح
            $table->unsignedBigInteger('language_level_id')->nullable();
            $table->unsignedBigInteger('skill_id')->nullable();

            // اگر می‌خواهید کلید خارجی:
            $table->foreign('age_group_id')->references('id')->on('age_groups')->nullOnDelete();
            $table->foreign('language_level_id')->references('id')->on('language_levels')->nullOnDelete();
            $table->foreign('skill_id')->references('id')->on('skills')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            //
        });
    }
};
