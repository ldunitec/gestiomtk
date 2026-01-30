<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcaciones extends Model
{
    protected $fillable = [
        'empleado_id',
        'fecha',
        'hora',
        'raw'
    ];
}

