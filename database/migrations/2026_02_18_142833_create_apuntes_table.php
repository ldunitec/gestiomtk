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
        Schema::create('apuntes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['lan', 'web', 'servidor', 'hosting', 'dominio', 'otro'])->default('lan');
            $table->string('ip')->nullable();
            $table->string('url')->nullable();
            $table->string('usuario')->nullable();
            $table->text('password'); // Encriptado
            $table->text('descripcion')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apuntes');
    }
};
