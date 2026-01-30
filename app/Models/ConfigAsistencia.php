<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigAsistencia extends Model
{
    protected $fillable = [
        'entrada_desde',
        'entrada_hasta',
        'salida_desde',
        'salida_hasta',
        'hora_oficial',
        'hora_salida',
        'minutos_permitidos'
    ];
}

