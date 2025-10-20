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
        Schema::table('stories', function (Blueprint $table) {
            $table->boolean('main_page')->default(false);

        });
//        Schema::drop('main_stories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
