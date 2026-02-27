<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viatico extends Model
{
    protected $fillable = [
        'origen',
        'destino',
        'cliente',
        'detalle_visita',
        'vehiculo',
        'km',
        'ida_vuelta',
        'tarifa_km',
        'total'
    ];
}

