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
        Schema::create('slas', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->integer('tiempo_respuesta_horas');
    $table->integer('tiempo_resolucion_horas');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
