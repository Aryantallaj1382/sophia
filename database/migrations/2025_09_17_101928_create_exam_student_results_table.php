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
        Schema::create('exam_student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_student_id')->constrained('exam_student')->onDelete('cascade');
            $table->string('status');
            $table->timestamp('date');
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
        Schema::dropIfExists('exam_student_results');
    }
};
