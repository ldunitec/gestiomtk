<?php

namespace Modules\Mikrotik\Entities;

class Router extends Modelo
{
    protected $table = 'mt_routers';
    protected $hidden = ['clave_api'];
    protected $casts = ['clave_api' => 'encrypted', 'tls' => 'boolean', 'puerto' => 'integer',
        'preparado_at' => 'datetime', 'verificado_at' => 'datetime'];

    public function fichas() { return $this->hasMany(Ficha::class, 'router_id'); }
    public function marcador(): string { return 'gmtk-'.str_replace('-', '', $this->uuid); }
    public function perfil(): string { return $this->marcador().'-visitantes'; }
    public function tarea(): string { return $this->marcador().'-expirar'; }
}
