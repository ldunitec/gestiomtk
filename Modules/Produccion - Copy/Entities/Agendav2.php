<?php

namespace Modules\Produccion\Entities;

use Illuminate\Database\Eloquent\Model;

class Agendav2 extends Model
{
    protected $table = 'agendas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'estado',
        'todoist_id'
    ];
}
