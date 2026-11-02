<?php

namespace Modules\Gym\Entities;

class Acceso extends GymModel
{
    protected $table = 'gym_accesos';
    protected $guarded = ['id'];
    protected $casts = ['permitido' => 'boolean', 'momento' => 'datetime'];
    public $timestamps = false;
    public function socio() { return $this->belongsTo(Socio::class, 'socio_id'); }
}
