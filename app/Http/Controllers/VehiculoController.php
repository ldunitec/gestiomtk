<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        return view('viaticos.vehiculos.index');
    }

    public function list()
    {
        return response()->json(Vehiculo::all());
    }

    public function store(Request $request)
    {
        Vehiculo::create($request->all());
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, $id)
    {
        Vehiculo::findOrFail($id)->update($request->all());
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        Vehiculo::destroy($id);
        return response()->json(['ok' => true]);
    }
}

