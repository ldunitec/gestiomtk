<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
     use HasFactory;
 protected $fillable = ['titulo', 'fecha_inicio', 'fecha_fin', 'color', 'usuario_id', 'tipo' ,'estado', 'todoist_id','assigned_to'];


public function scopeActivos($query)
{
    return $query->where('estado', 1);
}
}