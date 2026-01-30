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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();

            // Identificación básica
            $table->string('nombre')->unique();     // nombre del equipo
            $table->string('serial_number')->nullable();

            // Datos de red
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('puerto_patch')->nullable();

            // Relación con otros modelos (opcional)
            $table->foreignId('empleado_id')->nullable()->constrained()->onDelete('set null'); // responsable
            $table->foreignId('departamento_id')->nullable()->constrained()->onDelete('set null'); // ubicación
            $table->foreignId('marca_id')->nullable()->constrained()->onDelete('set null'); // ubicación
            $table->foreignId('modelo_id')->nullable()->constrained()->onDelete('set null'); // ubicación

            // Especificaciones del equipo
            $table->enum('antivirus', ['Activo', 'Inactivo'])->default('Inactivo');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            //agregar para usar estos equipo en el diagrama de res
            $table->string('x')->default('50px');
            $table->string('y')->default('50px');
            $table->enum('tipo_red', ['router', 'switch', 'ap', 'pc'])->nullable();

            $table->string('procesador')->nullable();
            $table->string('ram')->nullable();
            $table->string('almacenamiento')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
