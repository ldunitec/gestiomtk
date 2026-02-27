<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = ['nombres','apellidos','dni', 'telefono', 'reloj','fecha_nacimiento','estado', 'departamento_id','usuario_id'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function usuario(){
        return $this->belongsTo(User::class);
    }
    public function equipos(){
        return $this->hasMany(Equipo::class , );
    }
      public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
