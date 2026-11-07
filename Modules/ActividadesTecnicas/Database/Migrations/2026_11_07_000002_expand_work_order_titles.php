<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Coincidir con los 255 caracteres que ya permite la agenda.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE at_work_orders MODIFY title VARCHAR(255) NOT NULL');
        }
        // SQLite no limita la longitud declarada de VARCHAR.
    }

    public function down(): void
    {
        // No reducir la longitud: podría truncar títulos de actividades importadas.
    }
};
