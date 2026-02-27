<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
     Schema::create('hotspot_sessions', function (Blueprint $table) {
    $table->id();
    $table->string('user');
    $table->string('ip');
    $table->string('mac');
    $table->timestamp('login_at');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_sessions');
    }
};
