<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        // MariaDB/MySQL instalados: conservar todas las fechas de fichas anteriores.
        // Las instalaciones SQLite nuevas ya crean estas dos columnas como nullable.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE mt_lotes MODIFY vence_at DATETIME NULL');
            DB::statement('ALTER TABLE mt_fichas MODIFY vence_at DATETIME NULL');
        } elseif (DB::getDriverName() === 'sqlite') {
            foreach (['mt_lotes', 'mt_fichas'] as $tabla) {
                foreach (DB::select("PRAGMA table_info($tabla)") as $columna) {
                    if ($columna->name === 'vence_at' && $columna->notnull) {
                        throw new RuntimeException('Actualización de SQLite antiguo no admitida: migre esta instalación a MariaDB antes de continuar.');
                    }
                }
            }
        } else {
            throw new RuntimeException('Esta migración admite MariaDB/MySQL y SQLite nuevo.');
        }
        Schema::table('mt_lotes', function (Blueprint $t) {
            $t->string('inicio_vigencia', 20)->default('emision');
        });
        Schema::table('mt_fichas', function (Blueprint $t) {
            $t->string('inicio_vigencia', 20)->default('emision')->index();
            $t->unsignedTinyInteger('duracion_dias')->nullable();
            $t->dateTime('activada_at')->nullable();
        });
    }

    public function down(): void
    {
        // No perder la distinción permanente/primer ingreso al retroceder código.
        if (DB::table('mt_lotes')->where('inicio_vigencia', '!=', 'emision')->exists()) {
            throw new RuntimeException('Hay fichas con la nueva vigencia. Conserve esta migración para no perder su política de acceso.');
        }
        Schema::table('mt_fichas', function (Blueprint $t) {
            $t->dropIndex(['inicio_vigencia']);
            $t->dropColumn(['inicio_vigencia', 'duracion_dias', 'activada_at']);
        });
        Schema::table('mt_lotes', fn (Blueprint $t) => $t->dropColumn('inicio_vigencia'));
    }
};
