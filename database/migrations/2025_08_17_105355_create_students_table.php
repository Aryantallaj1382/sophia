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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // یک به یک با یوزر
            $table->string('first_name'); // نام
            $table->string('last_name'); // نام خانوادگی
            $table->string('email')->unique(); // ایمیل (یکتا)
            $table->string('phone')->nullable();
            $table->string('we_chat')->nullable();
            $table->date('birth_date'); // تاریخ تولد
            $table->enum('gender', ['male', 'female']); // جنسیت
            $table->enum('level', ['prea1', 'a1', 'a2', 'b1', 'b2', 'c1', 'c2']); // جنسیت
            $table->timestamps(); // زمان‌های ایجاد و به‌روزرسانی
        });
        Schema::create('student_learning_subgoal', function (Blueprint $table) {
            $table->id(); // کلید اصلی
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete(); // کلید خارجی به جدول دانش‌آموز
            $table->foreignId('learning_subgoal_id')->constrained('learning_subgoals')->cascadeOnDelete(); // کلید خارجی به جدول زیرهدف‌های یادگیری
            $table->timestamps(); // زمان‌های ایجاد و به‌روزرسانی
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
