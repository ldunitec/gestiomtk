<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('config_asistencias', function (Blueprint $table) {
            $table->id();
            $table->time('entrada_desde');
            $table->time('entrada_hasta');
            $table->time('salida_desde');
            $table->time('salida_hasta');
            $table->time('hora_oficial');
            $table->time('hora_salida');
            $table->integer('minutos_permitidos');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_asistencias');
    }
};
