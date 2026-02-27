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
        Schema::create('viaticos', function (Blueprint $table) {
    $table->id();
    $table->string('cliente');
    $table->string('origen');
    $table->string('destino');
    $table->string('vehiculo');
    $table->decimal('km', 8, 2);
    $table->boolean('ida_vuelta')->default(false);
    $table->decimal('tarifa_km', 8, 2);
    $table->decimal('total', 10, 2);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viaticos');
    }
};
