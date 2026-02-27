<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioBTR extends Model
{
    protected $fillable = [
        'localidad','ip','fases','rc','fibra','camaras','ping',
        'fuente','switchPoE','xcom','sg116',
        'observaciones','imagen',
        
    ];

    protected $casts = [
    'lienzo_json' => 'array',
    'camaras'     => 'integer',
    'switchPoE'   => 'integer',
    'xcom'        => 'integer',
    'sg116'       => 'integer',
    'fases'       => 'integer',
    'fuente'       => 'integer',
    'rc'          => 'integer',
];


    public function descargas()
    {
        return $this->hasMany(DescargaInventarioBTR::class);
    }



}

