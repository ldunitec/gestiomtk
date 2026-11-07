<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_evidences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
    $table->enum('kind', ['before', 'during', 'after', 'document', 'other'])->default('other');
    $table->string('disk', 40)->default('public');
    $table->string('path', 500);
    $table->string('original_name')->nullable();
    $table->string('mime_type', 120)->nullable();
    $table->unsignedBigInteger('size_bytes')->nullable();
    $table->string('caption', 500)->nullable();
    $table->dateTime('captured_at')->nullable();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->timestamps();
    $table->index(['work_order_id', 'kind'], 'at_000009_1_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_evidences');
    }
};
