<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\SwitchMIK;
use App\Models\SwPort;
use Illuminate\Http\Request;

class SwitchMIKController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $switchs = SwitchMIK::all();
        return view('admin.switchs.index', compact('switchs'));
    }

public function crear_puerto($id)
{
    $sw_rout = SwitchMIK::find($id);

    if ($sw_rout) {
        for ($i = 1; $i <= $sw_rout->total_ports; $i++) {
            SwPort::create([
                'switch_m_i_k_s_id' => $sw_rout->id,  // ✅ Usar solo el ID y sin espacios
                'port_number' => $i,                  // ✅ Número de puerto
            ]);
        }
    }
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
        //  return response()->json($datos);
        $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'total_ports' => 'required',

        ]);
         SwitchMIK::create(attributes: $request->all());

        return redirect()->route('admin.switchs.index')
            ->with('mensaje', 'Switch creado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(SwitchMIK $switchMIK)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SwitchMIK $switchMIK)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'total_ports' => 'required',

        ]);

        $switchs = SwitchMIK::findOrFail($id); // Encuentra la patch_panel por ID
        $switchs->update($request->all()); // Actualiza los datos de la patch_panel

        return redirect()->route('admin.switchs.index')
            ->with('mensaje', 'Switch actualizado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
               $switchs = SwitchMIK::findOrFail($id); // Encuentra la switchs por ID
                $switchs->delete();
                  return redirect()->route('admin.switch.index')
            ->with('mensaje', 'switch eliminado correctamente')
            ->with('icono', 'success');
    }
}
