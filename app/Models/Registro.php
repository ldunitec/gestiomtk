<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $fillable = ['user_id', 'fecha_hora', 'evento'];
}
