<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = ['descripcion','contenido'];

    public function historial()
    {
        return $this->hasMany(NotaHistorial::class);
    }
}
