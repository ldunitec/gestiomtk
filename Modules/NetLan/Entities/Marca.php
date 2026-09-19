<?php

namespace Modules\NetLan\Entities;
use Modules\NetLan\Entities\Modelo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marca extends Model
{ 
    use HasFactory;
    protected $fillable = [
        'nombre',
    ];
      // Mutator para convertir a mayúsculas antes de guardar
      public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $value ? strtoupper($value) : null;
    }

      public function modelos(){

        return $this->hasMany(Modelo::class, 'marca_id');
        }
  
}
