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
       Schema::create('gastos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('liquidacion_id')->constrained('liquidaciones')->onDelete('cascade');
    $table->date('fecha');
    $table->string('tipo');
    $table->string('ciudad');
    $table->decimal('monto', 10, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
