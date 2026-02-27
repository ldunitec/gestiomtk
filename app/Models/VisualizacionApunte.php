<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualizacionApunte extends Model
{
   
    protected $fillable = [
       'apunte_id',
       'user_id',
       'ip_usuario'
    ];
}
