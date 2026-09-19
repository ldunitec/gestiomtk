<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viaticos', function (Blueprint $table) {
            $table->id();
            $table->string('ciudad');
            $table->integer('distancia_km');
            $table->decimal('hospedaje', 10, 2);
            $table->decimal('alimentacion', 10, 2);
            $table->decimal('peajes', 10, 2);
            $table->decimal('combustible', 10, 2);
            $table->integer('cantidad_tecnicos')->default(1);
            $table->integer('dias')->default(1);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viaticos');
    }
};