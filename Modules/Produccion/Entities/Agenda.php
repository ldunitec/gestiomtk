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
// Las actualizaciones y el historial de la orden vinculada deben confirmarse juntos.
public function save(array $options = [])
{
    return \Illuminate\Support\Facades\DB::transaction(fn () => parent::save($options));
}

public function ordenTecnica()
{
    return $this->hasOne(\Modules\ActividadesTecnicas\Models\WorkOrder::class, 'agenda_id');
}

public function scopeActivos($query)
{
    return $query->where('estado', 1);
}
}

