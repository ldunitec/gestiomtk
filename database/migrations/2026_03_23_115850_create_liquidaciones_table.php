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
       Schema::create('liquidaciones', function (Blueprint $table) {
    $table->id();
    $table->string('ciudad')->nullable();
    $table->date('fecha_entrega');
    $table->decimal('asignado', 10, 2)->default(0);
    $table->decimal('total', 10, 2)->default(0);
    $table->decimal('deposito', 10, 2)->default(0);
    $table->enum('estado', [1, 2])->default(1);
  $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidaciones');
    }
};
