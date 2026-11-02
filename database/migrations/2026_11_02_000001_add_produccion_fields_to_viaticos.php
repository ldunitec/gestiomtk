<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('viaticos', function (Blueprint $table) {
            $table->string('origen')->nullable();
            $table->unsignedBigInteger('ciudad_id')->nullable()->index();
            $table->decimal('mano_obra', 10, 2)->default(0);
        });

        // En algunas instalaciones noches se agregó manualmente.
        if (!Schema::hasColumn('viaticos', 'noches')) {
            Schema::table('viaticos', function (Blueprint $table) {
                $table->unsignedInteger('noches')->default(0);
            });
        }
        // extra se utiliza en el sistema pero no está en la migración original.
        if (!Schema::hasColumn('viaticos', 'extra')) {
            Schema::table('viaticos', function (Blueprint $table) {
                $table->decimal('extra', 10, 2)->default(0);
            });
        }
        // MariaDB/MySQL: conservar decimales del catálogo sin requerir doctrine/dbal.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE viaticos MODIFY distancia_km DECIMAL(10, 2) NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('viaticos', function (Blueprint $table) {
            $table->dropIndex(['ciudad_id']);
            $table->dropColumn(['origen', 'ciudad_id', 'mano_obra']);
        });
        // Conservar noches, extra y la precisión de distancia: podrían contener datos.
    }
};
