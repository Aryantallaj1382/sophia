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
        Schema::create('exam_student', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['not_started', 'in_progress', 'completed'])
                ->default('not_started');

            $table->decimal('score', 5, 2)->nullable(); // نمره دانشجو
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('finished_at')->nullable(); // زمان پایان
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']); // یک دانشجو فقط یک رکورد برای هر آزمون
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_student');
    }
};
