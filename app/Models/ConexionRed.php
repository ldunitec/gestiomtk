<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConexionRed extends Model
{
    protected $table = 'conexiones_red';
    protected $fillable = ['origen_id','destino_id','interfaz'];
}

