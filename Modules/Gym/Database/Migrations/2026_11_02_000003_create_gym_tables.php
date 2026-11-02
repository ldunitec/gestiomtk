<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_socios', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 40)->unique();
            $t->string('nombres', 100);
            $t->string('apellidos', 100);
            $t->string('documento', 50)->nullable();
            $t->string('telefono', 40)->nullable();
            $t->string('email')->nullable();
            $t->string('estado', 20)->default('activo')->index();
            $t->timestamps();
        });
        Schema::create('gym_planes', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 100);
            $t->unsignedInteger('dias');
            $t->unsignedInteger('precio_centavos');
            $t->unsignedInteger('limite_visitas')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('gym_membresias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('socio_id')->constrained('gym_socios')->restrictOnDelete();
            $t->foreignId('plan_id')->constrained('gym_planes')->restrictOnDelete();
            $t->string('plan_nombre', 100);
            $t->date('inicio');
            $t->date('fin');
            $t->unsignedInteger('precio_centavos');
            $t->unsignedInteger('pagado_centavos')->default(0);
            $t->unsignedInteger('limite_visitas')->nullable();
            $t->boolean('anulada')->default(false);
            $t->unsignedBigInteger('creado_por')->nullable();
            $t->timestamps();
            $t->index(['socio_id', 'inicio', 'fin']);
        });
        Schema::create('gym_pagos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('membresia_id')->constrained('gym_membresias')->restrictOnDelete();
            $t->unsignedInteger('centavos');
            $t->string('referencia', 150)->nullable();
            $t->unsignedBigInteger('creado_por')->nullable();
            $t->timestamps();
        });
        Schema::create('gym_asistencias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('socio_id')->constrained('gym_socios')->restrictOnDelete();
            $t->foreignId('membresia_id')->constrained('gym_membresias')->restrictOnDelete();
            $t->dateTime('entrada')->index();
            $t->dateTime('salida')->nullable();
            $t->string('estado', 20)->default('abierta');
            $t->timestamps();
        });
        Schema::create('gym_accesos', function (Blueprint $t) {
            $t->id();
            $t->uuid('solicitud_id')->unique();
            $t->string('codigo', 40);
            $t->unsignedBigInteger('socio_id')->nullable()->index();
            $t->unsignedBigInteger('asistencia_id')->nullable();
            $t->unsignedBigInteger('operador_id');
            $t->string('movimiento', 10);
            $t->boolean('permitido');
            $t->string('motivo', 100);
            $t->dateTime('momento')->index();
        });
    }

    public function down(): void
    {
        foreach (['gym_accesos', 'gym_asistencias', 'gym_pagos', 'gym_membresias', 'gym_planes', 'gym_socios'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
