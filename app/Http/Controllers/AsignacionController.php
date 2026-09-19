<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function asignar(Request $request)
    {
        $ticket = Ticket::findOrFail($request->ticket_id);

        Asignacion::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'user_id' => $request->user_id,
                'fecha_asignacion' => now()
            ]
        );

        // Cambiar estado automáticamente
        $ticket->estado = 'asignado';
        $ticket->fecha_asignacion = now();
        $ticket->save();

        return response()->json(['success'=>true]);
    }

    public function iniciar(Request $request)
    {
        $asignacion = Asignacion::where('ticket_id',$request->ticket_id)->first();

        $asignacion->fecha_inicio = now();
        $asignacion->save();

        $asignacion->ticket->update([
            'estado'=>'en_proceso'
        ]);

        return response()->json(['success'=>true]);
    }

    public function finalizar(Request $request)
    {
        $asignacion = Asignacion::where('ticket_id',$request->ticket_id)->first();

        $asignacion->fecha_fin = now();
        $asignacion->save();

        $asignacion->ticket->update([
            'estado'=>'finalizado',
            'fecha_cierre'=>now()
        ]);

        return response()->json(['success'=>true]);
    }
}