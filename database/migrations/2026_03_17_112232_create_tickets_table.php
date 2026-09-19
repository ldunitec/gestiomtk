<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->unique();

            $table->string('cliente');
            $table->string('empresa'); // quien te subcontrata

            $table->string('equipo')->nullable();
            $table->text('problema');

            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');

            $table->enum('estado', [
                'pendiente',
                'asignado',
                'en_proceso',
                'finalizado',
                'cancelado'
            ])->default('pendiente');

            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->integer('sla_horas')->default(24);
            $table->text('solucion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
