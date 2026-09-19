<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $fillable=[
        'categoria_id',
        'descripcion',
        'monto',
        'fecha',
        'metodo_pago'
    ];

     public function categoria(){
        return $this->belongsTo(Categoria::class);
    }
}
