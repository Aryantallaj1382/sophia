<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('professor_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professors')->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->integer('min_blocks')->default(1);
            $table->enum('status', ['open', 'reserved', 'inactive'])->default('inactive');
            $table->timestamps();

            $table->index('professor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('professor_time_slots');
    }
};
