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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_type', [
                'one_to_one',
                'group',
                'webinar',
                'placement_test',
                'mock_test',
                'final_exam',
            ]); // نوع پلن
            $table->string('name'); // اسم پلن
            $table->string('color'); // اسم پلن
            $table->decimal('price', 12, 2); // قیمت نهایی پلن
            $table->integer('class_count'); // تعداد کلاس مجاز
            $table->string('original_price')->nullable(); // قیمت بدون تخفیف
            $table->string('discount_amount')->default(0); // مبلغ تخفیف
            $table->string('days'); // تاریخ انقضا پلن

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
