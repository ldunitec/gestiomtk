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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('tipo')->nullable()->default('Sin categoría');
            $table->string('secuencia_ticket')->nullable()->unique();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->string('color')->nullable();
            $table->string('todoist_task_id')->nullable();
            $table->string('todoist_id')->nullable()->after('id');
            $table->boolean('completado')->default(false);
            $table->integer('estado')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down()
{
    Schema::table('agendas', function (Blueprint $table) {
        $table->dropColumn('assigned_user_id');
    });
}
};

