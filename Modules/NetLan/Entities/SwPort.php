<?php

namespace Modules\NetLan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SwPort extends Model
{
    use HasFactory;
     protected $fillable = [
        'switch_m_i_k_s_id','port_number',
    ];
   
    public function sw_mk()
    {
        return $this->belongsTo(switchMik::class,'switch_m_i_k_s_id');
    }
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
    public function connection()
    {
        return $this->hasOne(Connection::class);
    }
}

