<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $casts = ['km_desde_tgu' => 'decimal:2', 'km_desde_sps' => 'decimal:2'];

    protected $table = 'ciudades';
    protected $fillable = [
        'nombre',
        'cliente',
        'km_desde_tgu',
        'km_desde_sps'
    ];
}

