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
        Schema::create('switch_m_i_k_s', function (Blueprint $table) {
             $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->integer('total_ports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switch_m_i_k_s');
    }
};
