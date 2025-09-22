<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('book_type', [
                "Students Book",
                "Teachers Book",
                "Workbook"
            ])->nullable()->after('edition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_classes');
    }
};

