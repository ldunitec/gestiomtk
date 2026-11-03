<?php

namespace Modules\Mikrotik\Entities;

class Ficha extends Modelo
{
    protected $table = 'mt_fichas';
    protected $hidden = ['clave'];
    protected $casts = ['clave' => 'encrypted', 'vence_at' => 'datetime', 'sincronizada_at' => 'datetime', 'revocada_at' => 'datetime'];
    public function router() { return $this->belongsTo(Router::class, 'router_id'); }
    public function lote() { return $this->belongsTo(Lote::class, 'lote_id'); }

    public function estadoVisible(): string
    {
        if ($this->estado === 'revocacion_pendiente') { return 'Revocación pendiente'; }
        if ($this->revocada_at) { return 'Revocada'; }
        if ($this->vence_at->isPast()) { return 'Vencida'; }
        return ['pendiente' => 'Pendiente', 'activa' => 'Activa', 'error' => 'Error de publicación', 'vencida' => 'Vencida'][$this->estado] ?? $this->estado;
    }

    public function imprimible(): bool
    {
        return $this->estado === 'activa' && !$this->revocada_at && $this->vence_at->isFuture();
    }

    public function qrUrl(): string
    {
        return $this->router->portal_url.'?'.http_build_query(['username' => $this->usuario], '', '&', PHP_QUERY_RFC3986);
    }
}
