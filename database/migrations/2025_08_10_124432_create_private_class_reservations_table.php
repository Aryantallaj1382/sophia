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
        Schema::create('private_class_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_level_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->foreignId('subgoal_id')->constrained('learning_subgoals')->onDelete('cascade');
            $table->string('discount_code')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->enum('class_type', ['trial', 'placement', 'sessional'])->default('sessional'); // نوع کلاس
            $table->unsignedInteger('sessions_count')->default(1); // تعداد جلسات

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_class_reservations');
    }
};
