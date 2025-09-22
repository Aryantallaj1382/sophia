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
        Schema::table('private_professor_time_slot', function (Blueprint $table) {
            $table->unsignedInteger('session_number')->nullable()->after('id');

            $table->enum('status', [
                'Cancelled',
                'Absent',
                'Finished',
                'Today',
                'Upcoming'
            ])->default('Upcoming')->after('session_number');
            $table->date('date')->after('status');
            $table->time('time')->after('date');

            $table->enum('cancel_by', ['student', 'professor'])->nullable()->after('time');
            $table->timestamp('cancel_date')->nullable()->after('cancel_by');
            $table->string('cancel_reason')->nullable()->after('cancel_date');
            $table->string('cancel_reason_file')->nullable()->after('cancel_reason');

            $table->string('refund')->nullable()->after('cancel_reason_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_professor_time_slot', function (Blueprint $table) {
            //
        });
    }
};
