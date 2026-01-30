<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
    ];
      // Mutator para convertir a mayúsculas antes de guardar
      public function setNombreAttribute($value)
      {
          $this->attributes['nombre'] = strtoupper($value);
      }

      public function modelos(){

        return $this->hasMany(Modelo::class);
        }
}
