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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('subject');
            $table->string('department'); // در SQL شما varbinary بود ولی در لاراول string کفایت می‌کنه
            $table->enum('status', ['open', 'answered', 'pending', 'closed'])
                ->default('pending');

            $table->timestamps();
        });
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message')->nullable();
            $table->boolean('is_support_reply')->default(false);
            $table->string('file')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
