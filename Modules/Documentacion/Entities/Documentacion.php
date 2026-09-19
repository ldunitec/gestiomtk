<?php

namespace Modules\Documentacion\Entities;

use Illuminate\Database\Eloquent\Model;

class Documentacion extends Model
{
    protected $fillable = [
        'titulo','descripcion','modulo','version'
    ];

    public function secciones()
    {
        return $this->hasMany(Seccion::class)->orderBy('orden');
    }
     protected $table = 'documentaciones';
}
