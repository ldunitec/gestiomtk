<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'identidad',
        'telefono',
        'email',
        'direccion',
        'ip',
        'mac',
        'plan',
        'estado'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Un cliente puede tener muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Si tienes tabla pagos
    // public function pagos()
    // {
    //     return $this->hasMany(Pag::class);
    // }


}
