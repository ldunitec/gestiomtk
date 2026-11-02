<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla usada por ambos modelos CREY es creys, no creysnew.
        if (!Schema::hasTable('creys')) {
            Schema::create('creys', function (Blueprint $table) {
                $table->id();
                $table->string('curso')->nullable();
                $table->string('modalidad')->nullable();
                $table->string('seccion')->nullable();
                $table->string('dni')->unique();
                $table->string('nombres');
                $table->string('apellidos');
                $table->string('observaciones')->nullable();
                $table->timestamps();
            });
        }
        Schema::table('creys', function (Blueprint $table) {
            $table->string('foto_path')->nullable();
            $table->timestamp('impreso_at')->nullable()->index();
            $table->unsignedBigInteger('impreso_por')->nullable();
        });
        Schema::create('impresion_lotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('accion', 30);
            $table->unsignedInteger('incluidos');
            $table->unsignedInteger('faltantes')->default(0);
            $table->unsignedInteger('marcados')->default(0);
            $table->json('alumno_ids');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresion_lotes');
        Schema::table('creys', function (Blueprint $table) {
            $table->dropIndex(['impreso_at']);
            $table->dropColumn(['foto_path', 'impreso_at', 'impreso_por']);
        });
    }
};
