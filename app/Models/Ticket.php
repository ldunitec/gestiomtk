<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    protected $fillable = [
        'codigo',
        'cliente',
        'empresa',
        'equipo',
        'problema',
        'prioridad',
        // 'estado',
        'fecha_asignacion',
        'fecha_cierre'
    ];
    public function asignacion()
{
    return $this->hasOne(Asignacion::class);
}

public function evidencias()
{
    return $this->hasMany(Evidencia::class);
}
}

