<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_technical_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('action', 100);
    $table->string('subject_type', 180)->nullable();
    $table->unsignedBigInteger('subject_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->dateTime('created_at')->useCurrent();
    $table->index(['subject_type', 'subject_id'], 'at_000013_1_index');
    $table->index(['user_id', 'created_at'], 'at_000013_2_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_technical_audit_logs');
    }
};
