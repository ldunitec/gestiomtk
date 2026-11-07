<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_gps_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->foreignId('technician_id')->constrained('users')->restrictOnDelete();
    $table->enum('event', ['arrival', 'start', 'pause', 'resume', 'finish', 'departure']);
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->decimal('accuracy_m', 8, 2)->nullable();
    $table->dateTime('recorded_at');
    $table->string('source', 30)->default('mobile_gps');
    $table->timestamps();
    $table->index(['work_order_id', 'recorded_at'], 'at_000008_1_index');
    $table->index(['technician_id', 'recorded_at'], 'at_000008_2_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_gps_logs');
    }
};
