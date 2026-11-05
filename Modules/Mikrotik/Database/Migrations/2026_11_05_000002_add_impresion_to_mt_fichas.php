<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mt_fichas', function (Blueprint $t) {
            $t->string('nombre', 100)->nullable();
            $t->dateTime('impresa_at')->nullable()->index();
            $t->unsignedBigInteger('impresa_por')->nullable();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('mt_fichas', function (Blueprint $t) {
            $t->dropIndex(['impresa_at']);
            $t->dropColumn(['nombre', 'impresa_at', 'impresa_por', 'deleted_at']);
        });
    }
};
