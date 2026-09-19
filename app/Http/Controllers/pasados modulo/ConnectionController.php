<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Equipo;
use App\Models\PatchPanel;
use App\Models\Port;
use App\Models\SwitchMIK;
use App\Models\SwPort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConnectionController extends Controller
{
   



    public function index()
    {
        $equipos = Equipo::all();
        $ports =  Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'SW%');
        })->with('patch_panel')->get();
        
        $portsws = Port::whereHas('patch_panel', function ($query) {
            $query->where('name', 'not like', 'PP%');
        })->with('patch_panel')->get();

          $swPorts =  SwPort::with('sw_mk')->get();
        //    return response()->json($swRouts);
        $conexiones = Connection::with('equipo', 'port.patch_panel')->get();

        // return response()->json($portsws);
        return view('NetLan::admin.connections.index', compact('equipos', 'ports', 'conexiones', 'portsws','swPorts'));
    }

      public function cambiarEstado(Request $request, $id)
    {
        //   $datos = $request->all();
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
            'estado'=>'required'
          
        ]);


        Connection::create(attributes: $request->all());

        return redirect()->route('admin.connections.index')
            ->with('mensaje', 'Conexion creada correctamente')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
   public function show()
{
     $equipos = Equipo::all();
     $equipos = Equipo::all();
     $ports =  Port::all();
     $swPorts =  SwPort::all();


    $switches = Connection::pluck('sw')->toArray(); // Todos los valores 'sw' registrados
    $conexiones = Connection::with('equipo')->get(); // Si necesitas más detalles en la vista
     $puerto = DB::table('connections')->pluck('nombre')->toArray(); // Ej: ['PP-A-1', 'PP-A-5', 'PP-A-9']
        $sw = DB::table('connections')->pluck('id','sw')->toArray(); //

    return view('admin.connections.show', compact('equipos', 'swPorts', 'ports',  'switches', 'conexiones','puerto','sw'));
}

public function buscarValor($valor)
{
  

      if (Str::startsWith($valor, 'PP')) {
        $resultado = Connection::with('equipo', 'equipo.empleado')
            ->where('nombre', 'like', $valor . '%')
            ->first();
    } else {
        $resultado = Connection::with('equipo', 'equipo.empleado')
            ->where('sw', $valor)
            ->first();
    }
    
    
    // $resultado = Connection::with('equipo','equipo.empleado')->where('nombre', $valor)->first();
    //    return response()->json($resultado);

    if ($resultado) {
        return response()->json($resultado);
    } else {
        return response()->json(['mensaje' => 'No encontrado'], 404);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
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
        $equipos = Equipo::all();
        //    return response()->json($conexiones);
        return view('admin.connections.edit', compact('conexion', 'ports', 'equipos','portsws','swPorts','portName','swSW'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $datos = Connection::find($id);
        // return response()->json($datos);
        $request->validate([
            'port_id' => 'required',
            'equipos_id' => 'required',
            'nombre'=>'required',
            'sw'=>'required',
            'estado'=>'required'
          
        ]);

        $conexion = Connection::findOrFail($id); // Encuentra la conne$conexion por ID
        $conexion->update($request->all()); // Actualiza los datos de la conne$conexion

        return redirect()->route('admin.connections.index')
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

        return redirect()->route('admin.connections.index')
            ->with('mensaje', 'Conexion eliminada correctamente')
            ->with('icono', 'success');
    }
}
