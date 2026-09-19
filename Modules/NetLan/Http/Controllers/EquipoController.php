<?php

namespace Modules\NetLan\Http\Controllers;


use Illuminate\Routing\Controller;

use Modules\NetLan\Entities\Equipo;
use Modules\NetLan\Entities\Marca;
use Modules\NetLan\Entities\Modelo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Modules\NetLan\Entities\Departamento;
use Modules\NetLan\Entities\Empleado;

class EquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $departamentos = Departamento::all();
        $marcas = Marca::all();
        $empleados = Empleado::orderBy('nombres', 'asc')->get();
     $equipos = Equipo::with('empleado', 'marca')->get();
        $activos = Equipo::with('empleado', 'marca')->where('estado','activo')->get();

        $inActivos = Equipo::with('empleado', 'marca')->where('estado','inactivo')->get();
        // dd($inActivos);
        return view('netlan::admin.equipos.index', compact(
            'departamentos',
            'marcas',
            'empleados',
            'inActivos',
            'activos'
        ));

       
   


    }
    public function cambiarEstado(Request $request, $id)
    {
        //   $datos = $request->all();
        //     return response()->json($datos);
        $equipo = Equipo::findOrFail($id);
        $equipo->estado = $equipo->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $equipo->save();

        return response()->json(['message' => 'Estado actualizado correctamente.']);
    }

    public function getModelos($marca_id)
    {
        $getmodelo = Modelo::where('marca_id', $marca_id)->get();
        return response()->json($getmodelo);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $datos = $request->all();
        // return response()->json($datos);


        $request->validate([
            'nombre' => 'required|string|max:255',
            'empleado_id' => 'required',
            'serial_number' => 'nullable|string|max:255',
            'ip_address' => 'required|string|max:55',
            'mac_address' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'marca_id' => 'nullable',
            'modelo_id' => 'nullable',
            'antivirus' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'procesador' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'almacenamiento' => 'nullable|string|max:255',
        ]);

        //         $datos = $request->all();

        // return response()->json($datos);
        Equipo::create([
            'nombre' => $request->nombre,
            'empleado_id' => $request->empleado_id,
            'serial_number' => $request->serial_number,
            'ip_address' => $request->ip_address,
            'mac_address' => $request->mac_address,
            'tipo' => $request->tipo,
            'marca_id' => $request->marca_id,
            'modelo_id' => $request->modelo_id,
            'antivirus' => $request->antivirus,
            'estado' => $request->estado,
            'procesador' => $request->procesador,
            'ram' => $request->ram,
            'almacenamiento' => $request->almacenamiento,
        ]);

        return back()
            ->with('mensaje', 'Equipo creado correctamente')
            ->with('icono', 'success');
    }


    /**
     * Display the specified resource.
     */
    public function show(Equipo $equipo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $equipo = Equipo::find($id);
        $empleados = Empleado::orderBy('nombres', 'asc')->get();
        $marcas = Marca::orderBy('nombre', 'asc')->get();
        // Buscar todos los modelos que pertenezcan a la misma marca del equipo
        $modelos = Modelo::where('marca_id', $equipo->marca_id)->get();



        //   $datos = $request->all();
        // return response()->json($modelos);
        return view('netlan::admin.equipos.edit', compact('empleados', 'equipo', 'marcas', 'modelos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //    $datos = $request->all();
        //    return response()->json($datos);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'empleado_id' => 'required',
            'serial_number' => 'nullable|string|max:255',
            'ip_address' => 'required|string|max:55',
            'mac_address' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'marca_id' => 'nullable',
            'modelo_id' => 'nullable',
            'antivirus' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'procesador' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'almacenamiento' => 'nullable|string|max:255',
        ]);

        $equipo = equipo::find($id);



        // Actualizamos los datos del equipo
        $equipo->nombre = $request->nombre;
        $equipo->empleado_id = $request->empleado_id;
        $equipo->serial_number = $request->serial_number;
        $equipo->ip_address = $request->ip_address;
        $equipo->mac_address = $request->mac_address;
        $equipo->tipo = $request->tipo;
        $equipo->marca_id = $request->marca_id;
        $equipo->modelo_id = $request->modelo_id;
        $equipo->antivirus = $request->antivirus;
        $equipo->estado = $request->estado;
        $equipo->procesador = $request->procesador;
        $equipo->ram = $request->ram;
        $equipo->almacenamiento = $request->almacenamiento;
        $equipo->save();

        return redirect()->route('netlan.admin.equipos.index')
            ->with('mensaje', 'equipo actualizada correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $equipo = Equipo::find($id);
        $equipo->delete();


        return back() ->with('mensaje', 'equipo eliminado correctamente')
            ->with('icono', 'success');
    }
// scan-red

}
