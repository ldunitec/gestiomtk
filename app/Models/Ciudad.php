<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $fillable = [
        'nombre',
        'km_desde_tgu',
        'km_desde_sps'
    ];
}
