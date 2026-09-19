<?php

namespace Modules\Documentacion\Entities;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones_documentacion';

    protected $fillable = [
        'documentacion_id','tipo','titulo','contenido','orden'
    ];

    public function documentacion()
    {
        return $this->belongsTo(Documentacion::class);
    }
}
