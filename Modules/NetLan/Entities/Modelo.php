<?php

namespace Modules\NetLan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = [
        'marca_id',
        'nombre',
    ];
      // Mutator para convertir a mayúsculas antes de guardar
      public function setNombreAttribute($value)
      {
        $this->attributes['nombre'] = $value ? strtoupper($value) : null;
      }

      public function marca(){

        return $this->belongsTo(Marca::class);
    
        }
      public function equipo(){

        return $this->belongsTo(Equipo::class);
    
        }
}
