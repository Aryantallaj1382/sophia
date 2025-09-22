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
        Schema::table('accents', function (Blueprint $table) {
            $table->string('title_ch')->nullable()->after('title');
        });
        Schema::table('learning_goals', function (Blueprint $table) {
            $table->string('title_ch')->nullable()->after('title');
        });

        Schema::table('learning_subgoals', function (Blueprint $table) {
            $table->string('title_ch')->nullable()->after('title');
        });
        Schema::table('age_groups', function (Blueprint $table) {
            $table->string('title_ch')->nullable()->after('title');
        });
        Schema::table('language_levels', function (Blueprint $table) {
            $table->string('title_ch')->nullable()->after('title');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accents', function (Blueprint $table) {
            //
        });
    }
};
