<?php

namespace Modules\NetLan\Http\Controllers;

use Illuminate\Routing\Controller;


use Modules\NetLan\Entities\Connection;
use Modules\NetLan\Entities\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\NetLan\Entities\Port;
use Modules\NetLan\Entities\SwPort;

class ConnectionController extends Controller
{
   



    public function index()
    {
        $equipos = Equipo::where('estado', 'Activo')->with('empleado.departamento')->orderBy('nombre','asc')->get();
        // return response()->json($equipos);
        $ports =  Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'SW%');
        })->with('patch_panel')->get();
        
        $portsws = Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'PP%');
        })->with('patch_panel')->get();

          $swPorts =  SwPort::with('sw_mk')->get();
        //    return response()->json($swRouts);
        $conexiones = Connection::with('equipo', 'port.patch_panel')->get();

        // return response()->json($equipos);
        return view('netlan::admin.connections.index', compact('equipos', 'ports', 'conexiones', 'portsws','swPorts'));
    }

      public function cambiarEstado(Request $request, $id)
    {
          $datos = $request->all();
        //     return response()->json($datos);
        $conexion = Connection::findOrFail($id);
        $conexion->estado = $conexion->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $conexion->save();

        return response()->json(['message' => 'Estado actualizado correctamente.']);
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
  $request->validate([
            'port_id' => 'required',
            'equipos_id' => 'required',
            'nombre'=>'required',
            'sw'=>'required',
          
        ]);


        Connection::create(attributes: $request->all());

    return redirect()->route('netlan.admin.connections.index')
    ->with('mensaje', 'Conexion creada correctamente')
    ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
public function show()
{
    $equipos = Equipo::with([
        'empleado.departamento'
    ])->get();

    $ports = Port::with('patch_panel')->get();

    $swPorts = SwPort::with('sw_mk')->get();

    /*
    |--------------------------------------------------------------------------
    | Conexiones del mapa de red
    |--------------------------------------------------------------------------
    | Cargamos toda la información necesaria para mostrar:
    | - Puerto Cisco
    | - Patch Panel
    | - Equipo
    | - Empleado
    | - Departamento
    | - IP
    | - MAC
    | - Estado
    | - Comentario
    */
    $conexiones = Connection::with([
        'equipo.empleado.departamento',
        'port.patch_panel'
    ])->get();

    /*
    |--------------------------------------------------------------------------
    | Puertos Cisco utilizados
    |--------------------------------------------------------------------------
    */
    $switches = $conexiones
        ->pluck('sw')
        ->filter()
        ->values()
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | Patch Panels utilizados
    |--------------------------------------------------------------------------
    */
    $puerto = $conexiones
        ->pluck('nombre')
        ->filter()
        ->values()
        ->toArray();

    return view(
        'netlan::admin.connections.show',
        compact(
            'equipos',
            'swPorts',
            'ports',
            'switches',
            'conexiones',
            'puerto'
        )
    );
}

public function buscarValor($valor)
{
    if (Str::startsWith($valor, 'PP')) {

        $resultados = Connection::with([
            'equipo.empleado.departamento',
            'port.patch_panel'
        ])
        ->where('nombre', 'like', $valor . '%')
        ->get();
// 
    } else {

        $resultados = Connection::with([
            'equipo.empleado.departamento',
            'port.patch_panel'
        ])
        ->where('sw', $valor)
        ->get();
    }

    if ($resultados->isNotEmpty()) {

        return response()->json([
            'cantidad' => $resultados->count(),
            'datos' => $resultados
        ]);

    }

 return response()->json([
    'mensaje' => 'Puerto libre',
    'cantidad' => 0,
    'datos' => []
]);
}


    /**
     * Show the form for editing the specified resource.
     */

public function asignarPuerto(Request $request)
{
    $request->validate([
        'connection_id' => 'required|integer|exists:connections,id',
        'sw' => [
            'required',
            'string',
            'regex:/^SW-A-(?:[1-9]|[1-4][0-9]|5[0-2])$/',
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Buscar la conexión existente
    |--------------------------------------------------------------------------
    */
    $conexion = Connection::findOrFail($request->connection_id);

    /*
    |--------------------------------------------------------------------------
    | Verificar que el puerto Cisco no esté ocupado
    |--------------------------------------------------------------------------
    */
    $ocupado = Connection::where('sw', $request->sw)
        ->where('id', '!=', $conexion->id)
        ->exists();

    if ($ocupado) {

        return response()->json([
            'success' => false,
            'mensaje' => 'El puerto ' . $request->sw . ' ya tiene una conexión registrada.'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Asignar puerto
    |--------------------------------------------------------------------------
    */
    $conexion->sw = $request->sw;
    $conexion->save();

    return response()->json([
        'success' => true,
        'mensaje' => 'Puerto asignado correctamente.',
        'conexion_id' => $conexion->id,
        'sw' => $conexion->sw
    ]);
}


    public function edit($id)
    {
        // $datos= Connection::find($id);
        $conexion = Connection::find($id);
        // return response()->json($conexion);
         $ports =  Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'SW%');
        })->with('patch_panel')->get();
        
        $portsws = Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'PP%');
        })->with('patch_panel')->get();

         
          $swPorts =  SwPort::with('sw_mk')->get();
            //    return response()->json($swRouts);
          $portName =  $conexion->nombre;
          $swSW =  $conexion->sw;
        

        $conexiones = Connection::with('equipo', 'port.patch_panel')->get();
       $equipos = Equipo::where('estado', 'Activo')->orderBy('nombre','asc')->get();
        //    return response()->json($equipos);
        return view('netlan::admin.connections.edit', compact('conexion', 'ports', 'equipos','portsws','swPorts','portName','swSW'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $datos = Connection::find($id);
        $request->validate([
            'port_id' => 'required',
            'nombre'=>'required',
            'sw'=>'required',
            'equipos_id' => 'required',
            // 'estado'=>'required'
            
            ]);
            
            $conexion = Connection::findOrFail($id); // Encuentra la conne$conexion por ID
            // return response()->json($request);
        $conexion->update($request->all()); // Actualiza los datos de la conne$conexion

   return redirect()->route('netlan.admin.connections.index')
            ->with('mensaje', 'Conexion actualizada correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $conexion = Connection::findOrFail($id);
        $conexion->delete();

        return redirect()->back()
            ->with('mensaje', 'Conexion eliminada correctamente')
            ->with('icono', 'success');
    }
}
