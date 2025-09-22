<?php

// database/migrations/xxxx_xx_xx_create_books_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('title_file', ['pdf', 'epub', 'mobi'])->default('pdf'); // فرمت فایل کتاب
            $table->string('author')->nullable();
            $table->string('edition')->nullable();
            $table->string('volume')->nullable();
            $table->json('topics')->nullable(); // موضوعات (چند تا) مثلا ردینگ و رایتینگ
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('file')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('video')->nullable();
            $table->string('video_cover')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};
