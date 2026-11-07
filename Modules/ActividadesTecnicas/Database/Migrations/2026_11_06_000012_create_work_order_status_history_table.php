<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_status_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->string('from_status', 30)->nullable();
    $table->string('to_status', 30);
    $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
    $table->text('notes')->nullable();
    $table->dateTime('changed_at');
    $table->timestamps();
    $table->index(['work_order_id', 'changed_at'], 'at_000012_1_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_status_history');
    }
};
