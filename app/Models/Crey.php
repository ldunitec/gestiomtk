<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crey extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso',
        'seccion',
        'modalidad',
        'dni',
        'nombres',
        'apellidos',
        'observaciones'
    ];
}