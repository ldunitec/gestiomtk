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
       Schema::table('equipos', function (Blueprint $table) {
    $table->enum('tipo_red',['router','switch','ap','pc'])->nullable();
    $table->string('pos_x')->default('50px');
    $table->string('pos_y')->default('50px');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            //
        });
    }
};
