<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $fillable = ['equipo_id','tipo','mensaje','visto'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}