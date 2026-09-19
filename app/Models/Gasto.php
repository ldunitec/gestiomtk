<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = [
        'liquidacion_id',
        'fecha',
        'tipo',
        'ciudad',
        'monto'
    ];

    public function liquidacion()
    {
        return $this->belongsTo(Liquidacion::class);
    }
}
