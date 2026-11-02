<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viatico extends Model
{
    use HasFactory;

    protected $casts = [
        'distancia_km' => 'decimal:2',
        'mano_obra' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $fillable = [
        'ciudad',
        'ciudad_id',
        'origen',
        'mano_obra',
        'noches',
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




