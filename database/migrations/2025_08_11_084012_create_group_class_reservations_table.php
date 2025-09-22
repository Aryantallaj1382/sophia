<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_class_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_class_id')
                ->constrained('group_classes')
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

    public function down(): void
    {
        Schema::dropIfExists('group_class_reservations');
    }
};
