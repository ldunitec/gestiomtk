<?php

namespace Modules\Gym\Entities;

class Membresia extends GymModel
{
    protected $table = 'gym_membresias';
    protected $guarded = ['id'];
    protected $attributes = ['anulada' => false];
    protected $casts = ['anulada' => 'boolean'];
    public function socio() { return $this->belongsTo(Socio::class, 'socio_id'); }
    public function pagos() { return $this->hasMany(Pago::class, 'membresia_id'); }
}
