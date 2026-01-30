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
        Schema::create('template_elements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('template_id')->constrained()->cascadeOnDelete();
    $table->enum('type',['text','photo','qr','barcode']);
    $table->string('field')->nullable(); // name, career, id_number
    $table->integer('x')->default(0);
    $table->integer('y')->default(0);
    $table->integer('font_size')->default(12);
    $table->string('color')->default('#000000');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_elements');
    }
};
