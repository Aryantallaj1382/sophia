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
        Schema::table('professors', function (Blueprint $table) {
            $table->string('voov_link')->nullable();
            $table->string('classin_link')->nullable();
            $table->string('zoom_link')->nullable();
            $table->string('teams')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
