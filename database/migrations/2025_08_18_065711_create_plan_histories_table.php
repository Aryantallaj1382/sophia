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
        Schema::create('plan_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_plan_id'); // پلن خریداری شده
            $table->morphs('usable'); // usable_id و usable_type
            $table->decimal('price', 10, 2); // قیمت
            $table->string('name'); // اسم استفاده
            $table->timestamps();

            $table->foreign('user_plan_id')
                ->references('id')
                ->on('user_plans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_histories');
    }
};
