<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_materials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->foreignId('technician_id')->constrained('users')->restrictOnDelete();
    $table->string('material_name', 180);
    $table->string('sku', 80)->nullable();
    $table->decimal('quantity', 12, 3)->default(1);
    $table->string('unit', 30)->default('unidad');
    $table->decimal('unit_cost', 12, 2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->index(['work_order_id', 'material_name'], 'at_000010_1_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_materials');
    }
};
