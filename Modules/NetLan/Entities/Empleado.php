<?php

namespace Modules\NetLan\Entities;

use App\Models\Asistencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = ['nombres','apellidos','dni', 'direccion', 'telefono', 'reloj','fecha_nacimiento','fecha_inicio', 'puesto','foto','fotoDni','fotoLic','estado', 'departamento_id','usuario_id'];

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
