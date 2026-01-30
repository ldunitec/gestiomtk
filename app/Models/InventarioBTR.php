<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioBTR extends Model
{
    use HasFactory;
     protected $fillable = [
        'localidad', 'ip', 'fases','rc', 'fibra','camara', 'ping',
        'fuente', 'switchPoE', 'xcom', 'sg116',
        'observaciones','imagen'
    ];
}
