<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSynopsisAndDescriptionToBooksTable extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->text('synopsis')->nullable(); // Adiciona a coluna 'synopsis'
            $table->text('description')->nullable(); // Adiciona a coluna 'description'
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['synopsis', 'description']); // Remove as colunas 'synopsis' e 'description'
        });
    }
}

