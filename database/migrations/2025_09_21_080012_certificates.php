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
        Schema::table('certificates', function (Blueprint $table) {

            $table->unsignedBigInteger('for_id')->nullable()->after('for'); // اضافه شد
            $table->string('en_name')->nullable();
            $table->string('zh_name')->nullable();
            $table->boolean('in_person')->nullable();
            $table->boolean('electronic')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

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
