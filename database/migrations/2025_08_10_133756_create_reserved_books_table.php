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
        Schema::create('reserved_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_class_reservation_id')
                ->constrained('private_class_reservations')
                ->onDelete('cascade');
            $table->enum('selection_type', [
                'I will upload File link',
                'I will upload the teaching material myself',
                'I will leave the book selection to the teacher',
                "I will choose a book from the teachers available materials"
            ]);
            $table->string('link')->nullable();
            $table->string('file')->nullable();
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_books');
    }
};
