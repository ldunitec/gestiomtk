<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mt_routers', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->string('nombre', 100);
            $t->string('host', 253);
            $t->unsignedSmallInteger('puerto')->default(8729);
            $t->boolean('tls')->default(true);
            $t->string('usuario_api', 100);
            $t->text('clave_api');
            $t->string('servidor_hotspot', 100);
            $t->string('portal_url', 255);
            $t->string('ssid', 100);
            $t->string('velocidad', 40)->default('2M/5M');
            $t->dateTime('preparado_at')->nullable();
            $t->dateTime('verificado_at')->nullable();
            $t->string('version', 60)->nullable();
            $t->timestamps();
        });
        Schema::create('mt_lotes', function (Blueprint $t) {
            $t->id();
            $t->uuid('solicitud_uuid')->unique();
            $t->foreignId('router_id')->constrained('mt_routers')->restrictOnDelete();
            $t->unsignedTinyInteger('dias');
            $t->unsignedTinyInteger('cantidad');
            $t->string('visitante', 100)->nullable();
            $t->unsignedBigInteger('creado_por');
            $t->dateTime('vence_at')->nullable()->index();
            $t->timestamps();
        });
        Schema::create('mt_fichas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('lote_id')->constrained('mt_lotes')->restrictOnDelete();
            $t->foreignId('router_id')->constrained('mt_routers')->restrictOnDelete();
            $t->string('usuario', 32)->unique();
            $t->text('clave');
            $t->string('estado', 30)->default('pendiente')->index();
            $t->dateTime('vence_at')->nullable()->index();
            $t->dateTime('sincronizada_at')->nullable();
            $t->dateTime('revocada_at')->nullable();
            $t->string('error', 180)->nullable();
            $t->timestamps();
        });
        Schema::create('mt_eventos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('router_id')->constrained('mt_routers')->restrictOnDelete();
            $t->unsignedBigInteger('ficha_id')->nullable();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('accion', 40);
            $t->boolean('correcto');
            $t->dateTime('created_at')->index();
        });
    }

    public function down(): void
    {
        foreach (['mt_eventos', 'mt_fichas', 'mt_lotes', 'mt_routers'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

