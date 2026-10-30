<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('agendas', function (Blueprint $table) {
    $table->string('project_id')->nullable();
    $table->json('usuarios')->nullable();
    $table->string('contacto')->nullable();
    $table->string('telefono')->nullable();
    $table->string('servicio')->nullable();
    $table->json('todoist_task_id')->nullable()->change();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
};
