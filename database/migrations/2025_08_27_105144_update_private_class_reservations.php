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
            $table->string('cancel_reason')->nullable()->after('sessions_count');
            $table->string('cancel_file')->nullable()->after('cancel_reason');

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
