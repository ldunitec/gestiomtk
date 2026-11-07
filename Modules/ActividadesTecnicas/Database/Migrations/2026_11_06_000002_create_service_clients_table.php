<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_service_clients', function (Blueprint $table) {
    $table->id();
    $table->string('name', 180);
    $table->string('tax_id', 40)->nullable()->index();
    $table->string('contact_name', 150)->nullable();
    $table->string('phone', 30)->nullable();
    $table->string('email')->nullable();
    $table->text('address')->nullable();
    $table->boolean('is_active')->default(true);
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_service_clients');
    }
};
