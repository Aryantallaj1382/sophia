<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('likeable_type');
            $table->unsignedBigInteger('likeable_id');
            $table->timestamps();

            $table->index(['likeable_type', 'likeable_id']);
        });
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->string('dislikable_type');
            $table->unsignedBigInteger('dislikable_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['dislikable_type', 'dislikable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
