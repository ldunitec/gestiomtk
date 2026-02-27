<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class DescargaInventarioBTR extends Model
{
    protected $table = 'descargas_inventario_btr';

    protected $fillable = [
        'inventario_btr_id','ip_cliente'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
