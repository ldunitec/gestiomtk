<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;
    protected $fillable = [
        'port_id','equipos_id','nombre','sw','comentario','estado'
    ];
    public function port()
    {
        return $this->belongsTo(Port::class);
    }
    public function swPort()
    {
        return $this->belongsTo(SwitchMIK::class);
    }
    public function equipo()
    {
        return $this->belongsTo(Equipo::class,'equipos_id');
    }
}

