<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdministrativoController extends Controller
{
    const sms = 'administrativos';
                 
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

            $administrativos = Administrativo::all();
            return view('admin.administrativos.index', compact('administrativos'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.'.self::sms.'.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $datos = $request->all();
        // return response()->json($datos);

        // Para realizar alacenamiento en varias tablas se gusrdo en el orden de uso, primero el usuario, luego se asigna el roly despues el administrativo

        // validacion 
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'dni' => 'required',
            'fecha_nacimiento' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'profesion' => 'required',
            'rol' => 'required',
        ]);

        // crear usuario 
        $usuario = new User();
        $usuario->name = $request->nombres.' '. $request->apellidos;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->dni);
        $usuario->save();

        // asignar el rol al usuario creado 
        $usuario->assignRole($request->rol);
        try {
            // Tu código de creación de usuario y ficha administrativa...
             // creacion de la ficha del personal administrativo 
        $administrativo = new Administrativo();
        $administrativo->usuario_id = $usuario->id;
        $administrativo->nombres = $request->nombres;
        $administrativo->apellidos = $request->apellidos;
        $administrativo->dni = $request->dni;
        $administrativo->fecha_nacimiento = $request->fecha_nacimiento;
        $administrativo->telefono = $request->telefono;
        $administrativo->direccion = $request->direccion;
        $administrativo->profesion = $request->profesion;
        $administrativo->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un problema al crear el usuario. Por favor, intente nuevamente.'], 500);
        }
       

        return redirect()->route('admin.' . self::sms . '.index')
        ->with('mensaje', self::sms . ' registrado correctamente')
        ->with('icono', 'success');
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Administrativo $administrativo,$id)
    {
         $roles = Role::all();
        $administrativo = Administrativo::findOrFail($id);
      return view('admin.'.self::sms.'.show',compact('administrativo','roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Administrativo $administrativo,$id)
    {
        $roles = Role::all();
        $administrativo = Administrativo::findOrFail($id);
      return view('admin.'.self::sms.'.edit',compact('administrativo','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $administrativo = Administrativo::findOrFail($id);

        $usuario = User::findOrFail($administrativo->usuario_id);
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'dni' => 'required|unique:administrativos,dni,' . $administrativo->id,
            'fecha_nacimiento' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'profesion' => 'required',
            'rol' => 'required',
            'email' => 'required|email|unique:users,email,' . $usuario->id // assuming email is editable

        ]);

        $usuario->name = $request->nombres.' '. $request->apellidos;
        $usuario->email = $request->email;
        $usuario->save();

        
        // asignar el rol al usuario creado 
        $usuario->syncRoles($request->rol);

        $administrativo->nombres = $request->nombres;
        $administrativo->apellidos = $request->apellidos;
        $administrativo->dni = $request->dni;
        $administrativo->fecha_nacimiento = $request->fecha_nacimiento;
        $administrativo->telefono = $request->telefono;
        $administrativo->direccion = $request->direccion;
        $administrativo->profesion = $request->profesion;
        $administrativo->save();


        return redirect()->route('admin.' . self::sms . '.index')
        ->with('mensaje', self::sms . ' actualizado correctamente')
        ->with('icono', 'success');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $administrativo = Administrativo::findOrFail($id);
        $administrativo->delete();
        $administrativo->usuario->delete();

        return redirect()->route('admin.' . self::sms . '.index')
        ->with('mensaje', self::sms . ' eliminado correctamente')
        ->with('icono', 'success');
    }
}
