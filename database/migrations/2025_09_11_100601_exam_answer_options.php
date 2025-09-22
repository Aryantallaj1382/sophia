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
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_question_id')->constrained('exam_question')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_variant_id')->nullable()->constrained('exam_variant')->onDelete('cascade'); // برای ورینت‌ها
            $table->text('text_answer')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_answer_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_answer_id')->constrained('exam_answers')->onDelete('cascade');
            $table->foreignId('exam_variant_id')->constrained('exam_variant')->onDelete('cascade');
            $table->foreignId('exam_variant_option_id')->constrained('exam_variant_option')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
