<?php

namespace Modules\Produccion\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agenda extends Model
{
      use HasFactory;

protected $fillable = [
    'titulo',
    'fecha_inicio',
    'fecha_fin',
    'tipo',
    'secuencia_ticket',
    'color',
    'usuario_id',
    'assigned_user_id',
    'project_id',
    'usuarios',
    'contacto',
    'telefono',
    'servicio',
    'todoist_task_id',
    'estado'
];

protected $casts = [
    'usuarios' => 'array',
   'todoist_task_id' => 'string',
];
public function scopeActivos($query)
{
    return $query->where('estado', 1);
}
}
