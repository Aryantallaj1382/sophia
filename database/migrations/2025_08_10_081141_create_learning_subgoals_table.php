<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('learning_subgoals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('learning_goals')->cascadeOnDelete();
            $table->string('title');
            $table->string('sub');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('learning_subgoals');
    }
};
