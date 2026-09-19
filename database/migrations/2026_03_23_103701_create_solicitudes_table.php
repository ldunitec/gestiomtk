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
     Schema::create('solicitudes', function (Blueprint $table) {
    $table->id();
    $table->string('ciudad');
    $table->date('fecha_solicitud');

    $table->decimal('alimentacion', 10, 2)->default(0);
    $table->decimal('hospedaje', 10, 2)->default(0);
    $table->decimal('transporte', 10, 2)->default(0);
    $table->decimal('combustible', 10, 2)->default(0);
    $table->decimal('peajes', 10, 2)->default(0);
    $table->decimal('extra', 10, 2)->default(0);

    $table->decimal('monto_total', 10, 2);

    $table->date('fecha_ida');
    $table->date('fecha_regreso');

    $table->enum('estado', ['pendiente', 'liquidado'])->default('pendiente');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
