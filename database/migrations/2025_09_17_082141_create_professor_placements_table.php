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
        Schema::create('professor_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('professor_id')->constrained('professors')->onDelete('cascade');
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained('age_groups')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->foreignId('language_level_id')->constrained('language_levels')->onDelete('cascade');
            $table->string('time');
            $table->string('status');
            $table->timestamp('exam_date');
            $table->string('score');
            $table->string('reading');
            $table->string('listening');
            $table->string('writing');
            $table->string('speaking');
            $table->string('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_placements');
    }
};
