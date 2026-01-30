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
       Schema::create('conexiones_red', function (Blueprint $table) {
    $table->id();
    $table->foreignId('origen_id')->constrained('equipos_red')->cascadeOnDelete();
    $table->foreignId('destino_id')->constrained('equipos_red')->cascadeOnDelete();
    $table->string('interfaz')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conexiones_red');
    }
};
