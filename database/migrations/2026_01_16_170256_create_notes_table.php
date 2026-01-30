<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


 return new class extends Migration {

    public function up()
    {
       // database/migrations/xxxx_xx_xx_create_notes_table.php
Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
    $table->string('titulo')->nullable();
    $table->text('nota');
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
};
           


