<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoreo extends Model
{
 protected $fillable = [
        'equipo_id',
        'estado',
        'latencia',
    ];
}
