<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{

public function solicitud()
{
    $solicitudes = Solicitud::where('estado', 'pendiente')->get();
    return view('admin.viaticos.solicitud', compact('solicitudes'));
}
public function store(Request $request)
{
    $total = collect([
        $request->alimentacion,
        $request->hospedaje,
        $request->transporte,
        $request->combustible,
        $request->peajes,
        $request->extra
    ])->sum();

    Solicitud::create([
        'ciudad' => $request->ciudad,
        'fecha_solicitud' => $request->fecha_solicitud,
        'fecha_ida' => $request->fecha_ida,
        'fecha_regreso' => $request->fecha_regreso,
        'alimentacion' => $request->alimentacion,
        'hospedaje' => $request->hospedaje,
        'transporte' => $request->transporte,
        'combustible' => $request->combustible,
        'peajes' => $request->peajes,
        'extra' => $request->extra,
        'monto_total' => $total,
    ]);

    return response()->json(['ok' => true]);
}

public function liquidacion(Request $request)
{
    $solicitud = null;

    if ($request->solicitud_id) {
        $solicitud = Solicitud::find($request->solicitud_id);
    }

    return view('admin.liquidaciones.liquidacion', compact('solicitud'));
}
}
