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
        Schema::create('amortizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deuda_id');
            $table->integer('cuota');
            $table->date('fecha_pago');
            $table->decimal('capital', 10, 2);
            $table->decimal('interes', 10, 2);
            $table->decimal('saldo', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amortizacions');
    }
};
