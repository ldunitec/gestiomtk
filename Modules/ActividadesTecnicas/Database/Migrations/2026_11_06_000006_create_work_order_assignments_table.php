<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->foreignId('technician_id')->constrained('users')->restrictOnDelete();
    $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
    $table->dateTime('assigned_at');
    $table->dateTime('accepted_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->unique(['work_order_id', 'technician_id'], 'at_000006_1_index');
    $table->index(['technician_id', 'assigned_at'], 'at_000006_2_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_assignments');
    }
};
