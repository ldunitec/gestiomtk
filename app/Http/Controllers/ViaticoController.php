<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Vehiculo;
use App\Models\Viatico;
use Illuminate\Http\Request;

class ViaticoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */
   public function index()
    {
        $viaticos = Viatico::all();
      

 return view('admin.viaticos.index', compact('viaticos'));
     }

public function list()
{
    $viaticos = Viatico::orderBy('created_at', 'desc')->get();

    return response()->json([
        'data' => $viaticos
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    $ciudad   = Ciudad::findOrFail($request->ciudad_id);
    $vehiculo = Vehiculo::findOrFail($request->vehiculo_id);

    $km = $request->origen === 'Tegucigalpa'
        ? $ciudad->km_desde_tgu
        : $ciudad->km_desde_sps;

    if ($request->ida_vuelta) {
        $km *= 2;
    }

    $total = $km * $vehiculo->tarifa_km;

    $viatico = Viatico::create([
        'origen'         => $request->origen,
        'destino'        => $ciudad->nombre,
        'cliente'        => $request->cliente,
        'detalle_visita' => $request->detalle_visita,
        'vehiculo'       => $vehiculo->tipo,
        'km'             => $km,
        'ida_vuelta'     => $request->ida_vuelta,
        'tarifa_km'      => $vehiculo->tarifa_km,
        'total'          => $total,
    ]);

    return response()->json([
        'ok'     => true,
        'id'     => $viatico->id,
        'km'     => $km,
        'total'  => $total
    ]);
}


    /**
     * Display the specified resource.
     */
    public function show(Viatico $viatico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Viatico $viatico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Viatico $viatico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Viatico $viatico)
    {
        //
    }
}
