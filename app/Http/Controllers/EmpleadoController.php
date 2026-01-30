<?php
namespace App\Http\Controllers;

use App\Models\empleado;
use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        
        $departamentos = Departamento::all();
        $empleados = Empleado::with('departamento')->get();
        
       
        return view('admin.empleados.index', compact('empleados','departamentos'));
    }

    public function create()
    {
        $empleados = Empleado::all();
        return view('admin.empleados.create', compact('empleados'));
    }

    public function store(Request $request)
    {

        // $datos = $request->all();
        // return response()->json($datos);
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'reloj' => 'required|string|max:255',
            'fecha_nacimiento' => 'string|max:255',
            'departamento_id' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $usuario = new User();
        $usuario->name = $request->nombres.' '.$request->apellidos;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->dni);
        $usuario->save();  

        $empleado = new Empleado();
        $empleado->usuario_id = $usuario->id;
        $empleado->nombres = $request->nombres;
        $empleado->apellidos = $request->apellidos;
        $empleado->dni = $request->dni;
        $empleado->telefono = $request->telefono;
        $empleado->reloj = $request->reloj;
        $empleado->fecha_nacimiento = $request->fecha_nacimiento;
        $empleado->departamento_id = $request->departamento_id;
        $empleado->estado = $request->estado;
        $empleado->save();

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado creado con éxito.');
    }

    public function show(Empleado $empleado)
    {
        // return view('admin.empleados.show', compact('empleado'));
    }

    public function edit(Request $request,$id)
    {
        $departamentos = Departamento::all();
        $empleado = Empleado::find($id);
        
        return view('admin.empleados.edit', compact( 'departamentos','empleado'));
        // $datos = $id;
        // return response()->json($empleado);
    }

  
      public function update(Request $request, $id)
{
    // Buscar el empleado
    $empleado = Empleado::findOrFail($id);

    // Validación
    $request->validate([
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dni' => 'required|string|max:255',
        'telefono' => 'required|string|max:255',
        'reloj' => 'required|string|max:255',
        'fecha_nacimiento' => 'nullable|date',
        'departamento_id' => 'required|integer|exists:departamentos,id',
        'estado' => 'nullable|string', // Ajusta según tus estados
        'email' => 'required|email|unique:users,email,' .$empleado->usuario->id,
    ]);

    // Actualizar usuario relacionado
// return  $empleado->usuario_id;
    $usuario = $empleado->usuario;
    $usuario->name = $request->nombres . ' ' . $request->apellidos;
    $usuario->email = $request->email;
    // Si deseas actualizar la contraseña basada en el DNI otra vez, descomenta:
    // $usuario->password = Hash::make($request->dni);
    $usuario->save();

    // Actualizar empleado
    $empleado->update($request->only([
        'nombres', 
        'apellidos', 
        'dni', 
        'telefono', 
        'reloj', 
        'fecha_nacimiento', 
        'departamento_id', 
        'estado'
    ]));

    return redirect()->route('admin.empleados.index')
        ->with('mensaje', 'Empleado actualizado correctamente')
        ->with('icono', 'success');
}


    public function destroy(Empleado $id)
    {
        $id->delete();
        return redirect()->route('admin.empleados.index')->with('success', 'Empleado eliminado.');
    }
}
