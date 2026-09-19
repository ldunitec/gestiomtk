<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Viatico;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ViaticoController extends Controller
{
    // Mostrar formulario
    public function index()
    {
        return view('admin.viaticos.index');
    }
    public function ciudades()
    {
        return view('admin.viaticos.ciudades');
    }
    // Mostrar formulario
    public function create()
    {
        return view('admin.viaticos.create');
    }

    // Guardar en la base de datos
    public function store(Request $request)
    {

//    return response()->json($request);
        $data = $request->validate([
            'precio_combustible' => 'required|numeric',
            'precio_peaje' => 'required|numeric',
            'precio_hotel' => 'required|numeric',
            'precio_alimentacion' => 'required|numeric',
            'extra' => 'required|integer|min:0',
            'ciudad' => 'required|string',
            'distancia_km' => 'required|numeric',
            'dias' => 'required|integer|min:1',
            'noches' => 'required|integer|min:0',
            'peajes' => 'required|numeric|min:0',
            'cantidad_tecnicos' => 'required|integer|min:1',
            
        ]);

        // dd($data);
        // Calcular combustible: (distancia * 2 + 30) / 30
        $data['combustible'] = ceil( ($data['distancia_km'] * 2 + 30) / 30 )*$data['precio_combustible']*1.3;
        $data['alimentacion'] = ceil( $data['cantidad_tecnicos'] *$data['dias']*$data['precio_alimentacion'] );        // Total
        $data['peajes'] = ceil( $data['precio_peaje'] *$data['peajes']);        // Total
        $data['extra'] = ceil( $data['extra']);        // Total
        $data['hospedaje'] = ceil( $data['precio_hotel'] *$data['noches']);        // Total
        $data['total'] = ($data['hospedaje'] + $data['alimentacion'] + $data['peajes'] + $data['combustible']+$data['extra']);
        
        $viatico = Viatico::create($data);
        return redirect()->route('admin.viaticos.show', $viatico->id)
                         ->with('success', 'Viático guardado correctamente.');
    }

    // Mostrar detalle y generar PDF
    public function show($id)
    {
        $viatico = Viatico::findOrFail($id);
        // dd($viatico);
        return view('admin.viaticos.show', compact('viatico'));
    }

    public function pdf($id)
    {
        $viatico = Viatico::findOrFail($id);

        $pdf = Pdf::loadView('admin.viaticos.pdf', compact('viatico'));

        return $pdf->download('viatico_'.$viatico->id.'.pdf');
    }

    public function list()
{
    $viaticos = Viatico::all(); // O Viatico::with('cliente')...

    return response()->json([
        'data' => $viaticos
    ]);
}
    public function ciudadData()
{
    $ciudades = Ciudad::all(); // O Viatico::with('cliente')...

    return response()->json([
        'data' => $ciudades
    ]);
}
}