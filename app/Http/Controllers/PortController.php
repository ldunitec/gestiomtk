<?php

namespace App\Http\Controllers;

use App\Models\PatchPanel;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patchs = PatchPanel::all();
        $ports = Port::all();
        return view('admin.ports.index', compact('ports', 'patchs'));
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
        // $datos = $request->all();
        // return response()->json($datos);
        Port::create(attributes: $request->all());

        return redirect()->route('admin.ports.index')
            ->with('mensaje', 'Puerto panel creado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Port $port)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Port $port)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    //    $datos = $request->all();
    //     return response()->json($datos);
    $port=Port::find($id);
     $port->update(attributes: $request->all());

        return redirect()->route('admin.ports.index')
            ->with('mensaje', 'Puerto actualizado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $ports = Port::find($id);
        $ports->delete();

       return redirect()->route('admin.ports.index')
            ->with('mensaje', 'Puerto eliminado correctamente')
            ->with('icono', 'success');
    }
}
