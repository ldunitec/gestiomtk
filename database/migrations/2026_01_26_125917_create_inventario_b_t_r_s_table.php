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
        Schema::create('inventario_b_t_r_s', function (Blueprint $table) {
            $table->id();
            $table->string('localidad');
            $table->string('ip');
            $table->string('fases');
            $table->string('rc');
            $table->enum('fibra', ['Si', 'No'])->default('No');
            $table->string('camaras');
            $table->enum('ping', ['Si', 'No'])->default('No');
            $table->integer('fuente')->default(0);
            $table->integer('fuente', )->default(0);
            $table->integer('switchPoE', )->default(0);
            $table->integer('xcom', )->default(0);
            $table->integer('sg116', )->default(0);
            $table->string('observaciones', 255)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->longText('lienzo_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_b_t_r_s');
    }
};
