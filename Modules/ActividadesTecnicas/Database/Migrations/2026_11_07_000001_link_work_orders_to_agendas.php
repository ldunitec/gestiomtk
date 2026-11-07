<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agendas')) { throw new RuntimeException('La integración requiere la tabla agendas de Producción.'); }
        // Algunas instalaciones conservan estos campos creados fuera de las migraciones antiguas.
        foreach (['assigned_user_id','contacto','telefono','servicio','proyecto','project_id','usuarios','secuencia_ticket'] as $column) {
            if (Schema::hasColumn('agendas', $column)) { continue; }
            Schema::table('agendas', function (Blueprint $t) use ($column) {
                if ($column === 'assigned_user_id') { $t->unsignedBigInteger($column)->nullable(); }
                elseif ($column === 'usuarios') { $t->json($column)->nullable(); }
                elseif ($column === 'servicio') { $t->text($column)->nullable(); }
                else { $t->string($column)->nullable(); }
            });
        }
        Schema::table('at_work_orders', function (Blueprint $t) {
            $t->foreignId('agenda_id')->nullable()->unique()->constrained('agendas')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('at_work_orders', 'agenda_id')) { return; }
        Schema::table('at_work_orders', function (Blueprint $t) {
            $t->dropForeign(['agenda_id']);
            $t->dropUnique(['agenda_id']);
            $t->dropColumn('agenda_id');
        });
        // Conservar las columnas compatibles de agendas y sus datos existentes.
    }
};
