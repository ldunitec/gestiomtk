<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Ticket extends Model
{
    protected $fillable = [
    'numero_ticket',
    'cliente_id',
    'departamento_id',
    'agente_id',
    'asunto',
    'descripcion',
    'prioridad',
    'estado',
    'fecha_vencimiento',
    'fecha_cierre'
];

public function cliente()
{
    return $this->belongsTo(Cliente::class);
}

public function departamento()
{
    return $this->belongsTo(Departamento::class);
}

public function agente()
{
    return $this->belongsTo(User::class, 'agente_id');
}

public function respuestas()
{
    return $this->hasMany(RespuestaTicket::class);
}



}
