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
        Schema::create('group_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professors')->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained('age_groups');
            $table->foreignId('language_level_id')->constrained();
            $table->foreignId('subject_id')->constrained('learning_subgoals');
            $table->foreignId('language_id')->constrained('languages');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('min_capacity');
            $table->unsignedInteger('max_capacity');
            $table->unsignedInteger('sessions_count');
            $table->unsignedInteger('hourly')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image')->nullable();
            $table->string('class_link')->nullable();
            $table->enum('admin_status', ['pending', 'progress', 'filling', 'not_filled', 'approved'])->default('pending');
            $table->bigInteger('total_price')->nullable();
            $table->bigInteger('new_total_price')->nullable();
            $table->bigInteger('total_percentage')->nullable();
            $table->unsignedBigInteger('view')->default(0);
            $table->timestamps();
        });

        Schema::create('group_class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_class_id')->constrained('group_classes')->onDelete('cascade');
            $table->string('day'); // "شنبه", "یکشنبه", ...
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_classes');
    }
};
