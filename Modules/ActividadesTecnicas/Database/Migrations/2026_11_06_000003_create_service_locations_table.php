<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('at_service_locations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('at_service_clients')->cascadeOnDelete();
    $table->string('name', 150);
    $table->text('address');
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->unsignedInteger('geofence_radius_m')->default(150);
    $table->string('contact_name', 150)->nullable();
    $table->string('contact_phone', 30)->nullable();
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->index(['latitude', 'longitude'], 'at_000003_1_index');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('at_service_locations');
    }
};
