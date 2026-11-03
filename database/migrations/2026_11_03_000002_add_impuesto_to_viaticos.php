<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('viaticos', function (Blueprint $table) {
            // Null distingue registros anteriores: no se recalculan sus importes.
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('impuesto_porcentaje', 5, 2)->nullable();
            $table->decimal('impuesto', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('viaticos', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'impuesto_porcentaje', 'impuesto']);
        });
    }
};
