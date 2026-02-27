<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apunte extends Model
{
 protected $fillable = [
    'titulo',
    'tipo',
    'ip',
    'url',
    'usuario',
    'password',
    'descripcion',
    'created_by'
];

public function historial()
{
    return $this->hasMany(HistorialApunte::class);
}

public function creador()
{
    return $this->belongsTo(User::class, 'created_by');
}
public function ultimaActualizacionPassword()
{
    return $this->historial()->latest()->first();
}


}
