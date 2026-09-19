<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;
    protected $fillable = [
       
        'numero',
        'nombre'
        
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
