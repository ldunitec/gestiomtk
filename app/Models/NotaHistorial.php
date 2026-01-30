<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaHistorial extends Model
{
    public $timestamps = false;
    protected $fillable = ['nota_id','contenido','editado_en'];
}

