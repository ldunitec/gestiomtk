<?php

namespace Modules\Produccion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CiudadController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['editar' => ['nullable', 'integer', 'exists:ciudades,id']]);
        $ciudades = Ciudad::orderBy('nombre')->get();
        $editando = $request->filled('editar') ? Ciudad::findOrFail($request->input('editar')) : null;

        return view('produccion::admin.viaticos.ciudades', compact('ciudades', 'editando'));
    }

    public function list()
    {
        return response()->json(['data' => Ciudad::orderBy('nombre')->get()]);
    }

    public function store(Request $request)
    {
        // El esquema original exige cliente; un catálogo de rutas no tiene cliente.
        Ciudad::create($this->validated($request) + ['cliente' => '']);

        return redirect()->route('admin.viaticos.ciudades')->with('success', 'Ciudad agregada.');
    }

    public function update(Request $request, $id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->update($this->validated($request, $ciudad->id));

        return redirect()->route('admin.viaticos.ciudades')->with('success', 'Ciudad actualizada. Los viáticos anteriores conservan su distancia.');
    }

    protected function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('ciudades', 'nombre')->ignore($id)],
            'km_desde_tgu' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'km_desde_sps' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
        ]);

        if (($data['km_desde_tgu'] ?? null) === null && ($data['km_desde_sps'] ?? null) === null) {
            throw ValidationException::withMessages(['km_desde_tgu' => 'Indica el kilometraje desde al menos una ciudad de origen.']);
        }

        return $data;
    }
}
