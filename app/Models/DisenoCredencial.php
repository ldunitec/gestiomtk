<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisenoCredencial extends Model
{

protected $fillable=[
'nombre',
'fondo_frente',
'fondo_reverso',
'configuracion'
];

}