<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'curso',
        'jornada',
        'seccion',
        'modalidad',
        'dni',
        'nombres',
        'apellidos',
        'observacion'
    ];
}


