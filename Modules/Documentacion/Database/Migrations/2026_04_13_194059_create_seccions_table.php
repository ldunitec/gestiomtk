<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('secciones_documentacion', function (Blueprint $table) {
    $table->id();
    $table->foreignId('documentacion_id')->constrained()->onDelete('cascade');
    $table->string('tipo'); // modelo, controlador, etc
    $table->string('titulo');
    $table->longText('contenido')->nullable();
    $table->integer('orden')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seccions');
    }
};
