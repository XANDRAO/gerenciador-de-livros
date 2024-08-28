<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('synopsis')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->integer('pages_amount')->nullable();
            $table->string('isbn_number')->unique()->nullable();
            $table->string('image_name')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
