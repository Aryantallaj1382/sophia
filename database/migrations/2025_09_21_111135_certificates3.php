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
//        Schema::table('certificates', function (Blueprint $table) {
//
//            $table->enum('status' , ['paid' , 'no_paid'])->default('no_paid')->nullable();
//
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
