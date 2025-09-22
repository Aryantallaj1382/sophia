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
        Schema::create('report_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_professor_time_slot')
                ->nullable()
                ->constrained('private_professor_time_slot')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('private_class_reservations')
                ->nullOnDelete();

            $table->enum('absence', ['absence', 'presence', 'delay']);
            $table->string('absence_time', 255)->nullable();
            $table->boolean('exam')->default(0);
            $table->integer('writing')->nullable();
            $table->integer('speaking')->nullable();
            $table->integer('reading')->nullable();
            $table->integer('listening')->nullable();
            $table->integer('vocabulary')->nullable();
            $table->integer('final_score')->nullable();
            $table->integer('grammar')->nullable();
            $table->enum('student_status', ['passed', 'rejected'])->nullable();
            $table->json('exam_solutions')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('solutions')->nullable();
            $table->json('score')->nullable();
            $table->timestamps();


            $table->json('skills')->nullable();
            $table->json('exam_part')->nullable();
            $table->string('exam_name', 255)->nullable();

            $table->collation = 'utf8mb4_unicode_ci';
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_registration');
    }
};
