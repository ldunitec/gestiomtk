<?php

namespace Modules\Gym\Entities;

class Socio extends GymModel
{
    protected $table = 'gym_socios';
    protected $guarded = ['id'];
    protected $casts = [];
    public function membresias() { return $this->hasMany(Membresia::class, 'socio_id'); }
    public function getNombreAttribute(): string { return $this->nombres.' '.$this->apellidos; }
}
