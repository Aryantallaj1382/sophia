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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id(); // کلید اصلی
            $table->string('title'); // عنوان وبلاگ
            $table->longText('content'); // متن اصلی
            $table->json('tags'); // تگ‌ها به صورت JSON
            $table->string('image'); // تگ‌ها به صورت JSON
            $table->string('reading_time'); // زمان خواندن
            $table->unsignedBigInteger('views')->default(0); // تعداد بازدید
            $table->enum('type', ['blog', 'news']); // نوع (وبلاگ یا خبر)
            $table->enum('category', [
                'one_to_one',
                'group',
                'webinar',
                'placement_test',
                'mock_test',
                'final_exam'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
