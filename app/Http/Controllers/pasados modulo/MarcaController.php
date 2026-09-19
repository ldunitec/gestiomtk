<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */public function syncAttendance()
{
    Artisan::call('anviz:attendance');
    return back()->with('success', 'Asistencia descargada correctamente');
}

    public function index()
    {
        dd('CONTROLADOR VIEJO');
       $marcas = Marca::all();
       return view('admin.marcas.index',compact('marcas'));
    // return response()->json($marcas);
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
          dd('CONTROLADOR VIEJO');
        // $datos = $request->all();
        // return response()->json($datos);
        $request->validate([
            'nombre'      => 'required|string|max:50',
        ]);

        Marca::create(attributes: $request->all());

       
        return back()
            ->with('mensaje', 'Marca creada correctamente')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
          dd('CONTROLADOR VIEJO');
        //  $datos = $request->all();
        // return response()->json($datos);
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $marca = Marca::findOrFail($id); // Encuentra la marca por ID
        $marca->update($request->all()); // Actualiza los datos de la marca

        return redirect()->route('admin.marcas.index')
            ->with('mensaje', 'Marca actualizada correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
          dd('CONTROLADOR VIEJO');
        $marca = Marca::findOrFail($id); 
       $marca->delete();

       return redirect()->route('admin.marcas.index')
       ->with('mensaje', 'Marca eliminada correctamente')
       ->with('icono', 'success');
    }
}
