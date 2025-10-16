<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->text('body')->nullable();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->string('voice_url')->nullable();
            $table->string('video_url')->nullable();
            $table->enum('admin_status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->timestamps();
            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
