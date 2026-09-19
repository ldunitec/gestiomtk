<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}