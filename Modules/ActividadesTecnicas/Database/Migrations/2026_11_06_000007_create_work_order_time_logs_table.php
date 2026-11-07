<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_time_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->foreignId('technician_id')->constrained('users')->restrictOnDelete();
    $table->enum('event', ['start', 'pause', 'resume', 'finish']);
    $table->dateTime('started_at')->nullable();
    $table->dateTime('ended_at')->nullable();
    $table->unsignedInteger('duration_seconds')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->index(['work_order_id', 'technician_id', 'started_at'], 'at_000007_1_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_time_logs');
    }
};
