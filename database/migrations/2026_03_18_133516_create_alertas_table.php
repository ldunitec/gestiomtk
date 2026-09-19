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
       Schema::create('alertas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('equipo_id')->constrained()->cascadeOnDelete();
    $table->string('tipo'); // ej: 'OFFLINE', 'ONLINE'
    $table->text('mensaje');
    $table->boolean('visto')->default(0); // para marcar como leído
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
