<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number', 30)->unique();
    $table->foreignId('client_id')->nullable()->constrained('at_service_clients')->nullOnDelete();
    $table->foreignId('location_id')->nullable()->constrained('at_service_locations')->nullOnDelete();
    $table->foreignId('service_type_id')->nullable()->constrained('at_service_types')->nullOnDelete();
    $table->string('title', 255);
    $table->longText('description')->nullable();
    $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
    $table->enum('status', ['new', 'assigned', 'accepted', 'en_route', 'in_progress', 'paused', 'completed', 'under_review', 'approved', 'not_completed', 'cancelled'])->default('new');
    $table->dateTime('scheduled_at')->nullable();
    $table->dateTime('due_at')->nullable();
    $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
    $table->dateTime('closed_at')->nullable();
    $table->longText('completion_notes')->nullable();
    $table->timestamps();
    $table->index(['status', 'scheduled_at'], 'at_000005_1_index');
    $table->index(['priority', 'due_at'], 'at_000005_2_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_orders');
    }
};

