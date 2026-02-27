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
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('km_desde_tgu', 8, 2)->nullable();
            $table->decimal('km_desde_sps', 8, 2)->nullable();
            $table->string('cliente');
            $table->text('detalle_visita')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudads');
    }
};
