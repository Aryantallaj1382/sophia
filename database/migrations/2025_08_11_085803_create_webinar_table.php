<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new  class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professors')->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained('age_groups');
            $table->foreignId('language_level_id')->constrained();
            $table->foreignId('subject_id')->constrained('learning_subgoals');
            $table->foreignId('language_id')->constrained('languages');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null')->after('platform_id');
            $table->unsignedInteger('min_capacity');
            $table->unsignedInteger('max_capacity');
            $table->date('date')->nullable();
            $table->time('time');
            $table->string('image')->nullable();
            $table->string('class_link')->nullable();
            $table->enum('admin_status', ['pending', 'progress', 'filling', 'not_filled', 'approved'])->default('pending');
            $table->unsignedBigInteger('view')->default(0);
            $table->timestamps();
        });

        Schema::create('webinar_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')
                ->constrained('webinars')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('discount_code')->nullable();
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled', 'completed'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webinar');
    }
};
