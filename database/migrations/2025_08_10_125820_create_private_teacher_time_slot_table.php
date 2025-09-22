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
        Schema::create('private_professor_time_slot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_class_reservation_id');
            $table->unsignedBigInteger('professor_time_slot_id');
            $table->timestamps();

            // ایندکس‌ها و کلیدهای خارجی
            $table->foreign('private_class_reservation_id')
                ->references('id')
                ->on('private_class_reservations')
                ->onDelete('cascade');

            $table->foreign('professor_time_slot_id')
                ->references('id')
                ->on('professor_time_slots')
                ->onDelete('cascade');

            $table->index('private_class_reservation_id', 'private_professor_time_slot_reservation_idx');
            $table->index('professor_time_slot_id', 'private_professor_time_slot_slot_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_teacher_time_slot');
    }
};
