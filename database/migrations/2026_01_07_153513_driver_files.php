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
     Schema::create('driver_files', function (Blueprint $table) {
    $table->id();
    $table->foreignId('model_id')->constrained()->cascadeOnDelete();
    $table->string('type'); // windows | mac | manual
    $table->string('file_path'); // local
    $table->string('drive_path')->nullable(); // google drive
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
