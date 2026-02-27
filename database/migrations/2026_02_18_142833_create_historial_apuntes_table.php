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
        Schema::create('historial_apuntes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apunte_id')->constrained()->onDelete('cascade');
            $table->text('password_anterior');
            // $table->text('password_nueva');
            $table->foreignId('cambiado_por')->constrained('users');
            $table->text('motivo')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_apuntes');
    }
};
