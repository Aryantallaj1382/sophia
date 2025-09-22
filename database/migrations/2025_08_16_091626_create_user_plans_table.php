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
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // کاربر
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade'); // پلن خریداری‌شده

            $table->date('started_at');   // تاریخ شروع
            $table->date('expires_at');   // تاریخ انقضا (مبنای استفاده واقعی کاربر)
            $table->boolean('is_active')->default(true); // وضعیت پلن

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};
