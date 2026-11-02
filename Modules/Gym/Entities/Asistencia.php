<?php

namespace Modules\Gym\Entities;

class Asistencia extends GymModel
{
    protected $table = 'gym_asistencias';
    protected $guarded = ['id'];
    protected $casts = ['entrada' => 'datetime', 'salida' => 'datetime'];
    public function socio() { return $this->belongsTo(Socio::class, 'socio_id'); }
}
