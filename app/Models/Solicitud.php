<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $fillable = [
        'monto',
        'descripcion',
        'estado'
    ];

    public function liquidaciones()
    {
        return $this->hasMany(Liquidacion::class);
    }

    public function liquidacion()
{
    return $this->hasOne(Liquidacion::class);
}
}
