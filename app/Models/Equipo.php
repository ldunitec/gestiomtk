<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [

        'empleado_id',
        'nombre',
        'serial_number',
        'ip_address',
        'mac_address',
        'puerto_patch',
        'marca_id',
        'modelo_id',
        'antivirus',
        'estado',
        'procesador',
        'ram',
        'almacenamiento',
        'tipo_red',
        'pos_x',
        'pos_y'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }
    // public function panel()
    // {
    //     return $this->belongsTo(Panel::class);
    // }
    // public function port()
    // {
    //     return $this->belongsTo(Port::class);
    // }
    public function connection()
    {
        return $this->hasMany(Connection::class);
    }

}

