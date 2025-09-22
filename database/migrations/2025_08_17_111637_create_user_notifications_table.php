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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()           // به users(id)
                ->cascadeOnDelete();      // با حذف کاربر، نوتیف‌ها هم حذف شوند
            $table->string('message', 500); // متن نوتیف
            $table->boolean('is_seen')->nullable(); // اختیاری: خوانده‌شدن
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
