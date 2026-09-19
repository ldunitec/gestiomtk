<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use Illuminate\Http\Request;

class EvidenciaController extends Controller
{
    public function store(Request $request)
    {
        if($request->hasFile('archivo')){

            $file = $request->file('archivo');
            $nombre = time().'_'.$file->getClientOriginalName();

            // $file->move(public_path('evidencias'), $nombre);
$file->storeAs('evidencias', $nombre, 'public');
            Evidencia::create([
                'ticket_id' => $request->ticket_id,
                'archivo' => $nombre,
                'tipo' => $file->getClientOriginalExtension(),
                'descripcion' => $request->descripcion
            ]);
        }

        return response()->json(['success'=>true]);
    }

    public function listar($ticket_id)
    {
        return Evidencia::where('ticket_id',$ticket_id)->get();
    }

    public function destroy(Request $request)
    {
        $evidencia = Evidencia::findOrFail($request->id);

        unlink(public_path('evidencias/'.$evidencia->archivo));

        $evidencia->delete();

        return response()->json(['success'=>true]);
    }
}