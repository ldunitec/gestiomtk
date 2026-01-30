<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativo extends Model
{
    use HasFactory;

    protected  $table ='administrativos';

    protected $fillable = [
        'usuario_id',
        'nombres',
        'apellidos',
        'dni',
        'fecha_nacimiento',
        'telefono',
        'profesion',
        'direccion',

    ];
    public function usuario(){
        return $this->belongsTo(User::class);
    }

    // pasar nombre a mayusculas
    public function setAttribute($dato, $value)
    {
        if ($dato === 'nombres' || $dato === 'apellidos') {
            // Convertir a mayúsculas antes de guardarlo
            $value = strtoupper($value);
        }

        return parent::setAttribute($dato, $value);
    }
    
    // public function setAttribute($key, $value)
    // {
    //     if ($key === 'telefono') {
    //         // Eliminar caracteres no numéricos
    //         $value = preg_replace('/[^0-9]/', '', $value);

    //         // Formatear como +504-xxxx-xxxx
    //         if (strlen($value) == 8) {
    //             $value = '+504 ' . substr($value, 0, 4) . '-' . substr($value, 4, 4);
    //         }
    //     }

    //     return parent::setAttribute($key, $value);
    // }

}
