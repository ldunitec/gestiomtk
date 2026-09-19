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
        'tipo',
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
    public function monitoreos()
    {
        return $this->hasMany(\App\Models\Monitoreo::class);
    }


public function ultimoMonitoreo()
{
    return $this->hasOne(\App\Models\Monitoreo::class)->latestOfMany();
}



    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {

            foreach ($model->getDirty() as $campo => $nuevo) {

                Auditoria::create([
                    'equipo_id' => $model->id,
                    'campo' => $campo,
                    'valor_anterior' => $model->getOriginal($campo),
                    'valor_nuevo' => $nuevo,
                    'user_id' => auth()->id()
                ]);
            }
        });
    }

}

