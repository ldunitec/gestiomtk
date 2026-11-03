<?php

namespace Modules\Mikrotik\Entities;

class Lote extends Modelo
{
    protected $table = 'mt_lotes';
    protected $casts = ['vence_at' => 'datetime'];
    public function fichas() { return $this->hasMany(Ficha::class, 'lote_id'); }
    public function router() { return $this->belongsTo(Router::class, 'router_id'); }
}
