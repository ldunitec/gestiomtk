<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
public function index()
{
  $tickets = Ticket::with(['cliente','departamento','agente'])
    ->latest()
    ->get();
// return $tickets;

    return view('admin.tickets.index', compact('tickets'));
}

public function create()
{
    $clientes = Cliente::all();
    $departamentos = Departamento::all();

    return view('admin.tickets.create', compact('clientes','departamentos'));
}
public function store(Request $request)
{
    $ultimo = Ticket::latest()->first();
    $numero = 'TK-'.date('Y').'-'.str_pad(($ultimo->id ?? 0) + 1,4,'0',STR_PAD_LEFT);

    Ticket::create([
        'numero_ticket' => $numero,
        'cliente_id' => $request->cliente_id,
        'departamento_id' => $request->departamento_id,
        'asunto' => $request->asunto,
        'descripcion' => $request->descripcion,
        'prioridad' => $request->prioridad,
    ]);

    return redirect()->route('tickets.index')
        ->with('success','Ticket creado correctamente');
}
public function show(Ticket $ticket)
{
    $ticket->load('respuestas.usuario','cliente','agente');
    return $ticket;

    return view('admin.tickets.show', compact('ticket'));
}



}
