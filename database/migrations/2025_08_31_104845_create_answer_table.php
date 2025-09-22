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
            $table->foreignId('exam_question_id')->constrained('exam_question')->onDelete('cascade'); // سوال مربوطه
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade'); // دانش‌آموز پاسخ‌دهنده
            $table->foreignId('exam_variant_option_id')->nullable()->constrained('exam_variant_option')->onDelete('cascade'); // برای سوالات تستی / چندگزینه‌ای
            $table->text('text_answer')->nullable();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade'); // دانش‌آموز پاسخ‌دهنده
            $table->string('file')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer');
    }
};
