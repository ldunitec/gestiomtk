<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viatico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ciudad',
        'distancia_km',
        'hospedaje',
        'alimentacion',
        'peajes',
        'combustible',
        'cantidad_tecnicos',
        'dias',
        'extra',
        'total',
    ];
}



