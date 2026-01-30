<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoRed extends Model
{
    protected $table = 'equipos_red';

    protected $fillable = [
        'nombre','tipo','ip','mac','x','y'
    ];

    public function conexionesOrigen(){
        return $this->hasMany(ConexionRed::class,'origen_id');
    }

    public function conexionesDestino(){
        return $this->hasMany(ConexionRed::class,'destino_id');
    }
}

