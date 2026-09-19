<?php

namespace Modules\NetLan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->belongsTo(switchMik::class);
    }
    public function equipo()
    {
        return $this->belongsTo(Equipo::class,'equipos_id');
    }
}