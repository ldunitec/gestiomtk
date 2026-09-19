<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        dd('modelo viejo');
        $modelos = Modelo::all();
        $marcas = Marca::all();
        return view('admin.modelos.index', compact('modelos', 'marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //    $datos = $request->all();
        //    return response()->json($datos);
        // validacion 
        $request->validate([
            'marca_id' => 'required',
            'nombre' => 'required',

        ]);

        Modelo::create($request->all());

        return back()
            ->with('mensaje', 'modelo creado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Modelo $modelo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'marca_id' => 'required',
            'nombre' => 'required',
        ]);
        $modelo = Modelo::findOrFail($id);
        $modelo->update($request->all());

        return redirect()->route('admin.modelos.index')
            ->with('mensaje', 'modelo actualizado correctamente')
            ->with('icono', 'success');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

    dd('modelo viejo');
        $modelo = Modelo::findOrFail($id);
        $modelo->delete();
        return redirect()->route('admin.modelos.index')
            ->with('mensaje', 'modelo eliminado correctamente')
            ->with('icono', 'success');
    }
}
