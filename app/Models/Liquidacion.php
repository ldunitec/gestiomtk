<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $fillable = [
        'ciudad',
        'fecha_entrega',
        'asignado',
        'total',
        'deposito',
        'estado',
        'solicitud_id' 
    ];

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }
   
    // 👇 relación nueva
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    protected $table = 'liquidaciones';
}
