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
        Schema::create('exam_part_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // نام یکتا مثل Listening, Reading
            $table->timestamps();
        });

        Schema::create('exam_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade'); // هر بخش مربوط به یک آزمون
            $table->foreignId('exam_part_type_id')->constrained('exam_part_types')->onDelete('cascade'); // نوع بخش
            $table->integer('number');
            $table->string('title')->nullable();
            $table->text('text')->nullable(); // متن اصلی
            $table->text('passenger')->nullable(); // متن passage
            $table->string('passenger_title')->nullable();
            $table->string('question_title')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type');
    }
};
