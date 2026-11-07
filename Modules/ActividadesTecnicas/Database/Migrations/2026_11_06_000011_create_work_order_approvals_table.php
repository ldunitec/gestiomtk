<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_work_order_approvals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_order_id')->constrained('at_work_orders')->cascadeOnDelete();
    $table->string('approved_by_name', 150);
    $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->enum('decision', ['approved', 'rejected'])->default('approved');
    $table->text('comments')->nullable();
    $table->string('signature_path', 500)->nullable();
    $table->dateTime('approved_at');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_work_order_approvals');
    }
};
