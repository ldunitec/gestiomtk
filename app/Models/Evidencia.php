<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $fillable = [
        'ticket_id',
        'archivo',
        'tipo',
        'descripcion'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}