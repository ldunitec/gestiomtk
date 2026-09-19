<?php

namespace App\Http\Controllers;

use App\Models\PatchPanel;
use Illuminate\Http\Request;

class PatchPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patch_panels = PatchPanel::all();
        return view('admin.patch_panels.index', compact('patch_panels'));
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
        //  $datos = $request->all();
        //  return response()->json($datos);
        $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'total_ports' => 'required',

        ]);

        PatchPanel::create(attributes: $request->all());

        return redirect()->route('admin.patch_panels.index')
            ->with('mensaje', 'Patch panel creado correctamente')
            ->with('icono', 'success');


    }

    /**
     * Display the specified resource.
     */
    public function show(PatchPanel $patchPanel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatchPanel $patchPanel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $datos = $request->all();
        // return response()->json($datos);
          $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'total_ports' => 'required',

        ]);

        $patch_panel = PatchPanel::findOrFail($id); // Encuentra la patch_panel por ID
        $patch_panel->update($request->all()); // Actualiza los datos de la patch_panel

        return redirect()->route('admin.patch_panels.index')
            ->with('mensaje', 'patch_panel actualizado correctamente')
            ->with('icono', 'success');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
               $patch_panel = PatchPanel::findOrFail($id); // Encuentra la patch_panel por ID
                $patch_panel->delete();
                  return redirect()->route('admin.patch_panels.index')
            ->with('mensaje', 'patch_panel eliminado correctamente')
            ->with('icono', 'success');
    }
}
