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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['Tarjeta', 'Prestamo', 'Suscripcion', 'Reembolso']);

            $table->string('name'); // Ej: Tarjeta Banrural
            $table->decimal('original_amount', 10, 2);
            $table->decimal('current_balance', 10, 2);

            $table->integer('total_installments')->nullable();
            $table->integer('paid_installments')->default(0);

            $table->date('due_date')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('frequency', ['Mensual', 'Anual'])->nullable();
            $table->decimal('recurring_amount', 10, 2)->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
