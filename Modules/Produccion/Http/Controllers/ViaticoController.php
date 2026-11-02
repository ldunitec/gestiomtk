<?php

namespace Modules\Produccion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use App\Models\Viatico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Produccion\Services\ViaticoCalculator;

class ViaticoController extends Controller
{
    public function index()
    {
        return view('produccion::admin.viaticos.index');
    }

    public function create()
    {
        $ciudades = Ciudad::orderBy('nombre')->get();

        return view('produccion::admin.viaticos.create', compact('ciudades'));
    }

    public function store(Request $request, ViaticoCalculator $calculator)
    {
        $data = $request->validate([
            'origen' => ['required', Rule::in(['Tegucigalpa', 'San Pedro Sula'])],
            'ciudad_id' => ['required', 'integer', 'exists:ciudades,id'],
            'precio_combustible' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'precio_peaje' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'precio_hotel' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'precio_alimentacion' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'extra' => ['required', 'integer', 'min:0', 'max:1000000'],
            'mano_obra' => ['required', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'dias' => ['required', 'integer', 'min:1', 'max:365'],
            'noches' => ['required', 'integer', 'min:0', 'max:365'],
            'peajes' => ['required', 'integer', 'min:0', 'max:1000'],
            'cantidad_tecnicos' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $ciudad = Ciudad::findOrFail($data['ciudad_id']);
        $columna = $data['origen'] === 'Tegucigalpa' ? 'km_desde_tgu' : 'km_desde_sps';
        $distancia = $ciudad->{$columna};

        // Cero es válido (viaje local); null significa que falta configurar la ruta.
        if ($distancia === null) {
            throw ValidationException::withMessages([
                'ciudad_id' => 'Configura los kilómetros desde '.$data['origen'].' para esta ciudad.',
            ]);
        }

        // Nombre, origen y distancia quedan guardados como fotografía del viaje.
        // No se confía en kilómetros enviados desde el navegador.
        $data['ciudad'] = $ciudad->nombre;
        $data['distancia_km'] = (float) $distancia;
        $gastos = $calculator->calculate($data);
        if ($gastos['total'] > 99999999.99) {
            throw ValidationException::withMessages(['mano_obra' => 'El total excede el límite permitido de L 99,999,999.99.']);
        }
        $viatico = Viatico::create(array_merge($data, $gastos));

        return redirect()->route('admin.viaticos.show', $viatico->id)
            ->with('success', 'Viático guardado correctamente.');
    }

    public function show($id)
    {
        $viatico = Viatico::findOrFail($id);

        return view('produccion::admin.viaticos.show', compact('viatico'));
    }

    public function pdf($id)
    {
        $viatico = Viatico::findOrFail($id);

        return Pdf::loadView('produccion::admin.viaticos.pdf', compact('viatico'))
            ->setPaper('letter', 'portrait')
            ->stream('viatico_'.$viatico->id.'.pdf');
    }

    public function list()
    {
        return response()->json(['data' => Viatico::orderByDesc('id')->get()]);
    }
}
