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
      Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->string('numero_ticket')->unique();
    $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
    $table->foreignId('departamento_id')->constrained()->onDelete('cascade');
    $table->foreignId('agente_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('asunto');
    $table->text('descripcion');
    $table->enum('prioridad', ['baja','normal','alta','critica'])->default('normal');
    $table->enum('estado', ['abierto','asignado','en_proceso','cerrado'])->default('abierto');
    $table->timestamp('fecha_vencimiento')->nullable();
    $table->timestamp('fecha_cierre')->nullable();
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
