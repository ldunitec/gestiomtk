<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('equipos_red', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->enum('tipo',['router','switch','ap','pc']);
    $table->string('ip')->nullable();
    $table->string('mac')->nullable();
    $table->string('x')->default('50px');
    $table->string('y')->default('50px');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos_red');
    }
};
