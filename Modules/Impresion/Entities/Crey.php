<?php

namespace Modules\Impresion\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crey extends Model
{
    use HasFactory;

   protected $fillable = [
        'curso',
        'modalidad',
        'seccion',
        'dni',
        'nombres',
        'apellidos',
        'observaciones',
    ];
}
