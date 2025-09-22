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
        Schema::create('exam_question', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->foreignId('exam_part_id')->nullable()->constrained('exam_parts')->onDelete('cascade');
            $table->foreignId('exams_id')->nullable()->constrained('exams')->onDelete('cascade');
            $table->enum('question_type', ['blank','test']);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('question')->nullable();
            $table->string('user_answer')->nullable();
            $table->timestamps();
        });
        Schema::create('exam_question_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_question_id')->nullable();
            $table->foreign('exam_question_id')->references('id')->on('exam_question')->onDelete('cascade');
            $table->string('path')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('exam_variant', function (Blueprint $table) {
            $table->id();
            $table->string('text')->nullable();
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question');
    }
};
