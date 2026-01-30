<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
          $this->attributes['nombre'] = strtoupper($value);
      }

      public function marca(){

        return $this->belongsTo(Marca::class);
    
        }
      public function equipo(){

        return $this->belongsTo(Equipo::class);
    
        }
}
