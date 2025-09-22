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
        Schema::table('private_class_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('professor_id');

            // اگر بخوای به users جدول ربط بدی
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_class_reservations', function (Blueprint $table) {
            //
        });
    }
};
