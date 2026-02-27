<?php

namespace App\Http\Controllers;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Ususario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->get();
        $roles = Role::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    // con checkbox 
    // public function asignarRol(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required',
    //         'roles'   => 'array'
    //     ]);

    //     $usuario = User::findOrFail($request->user_id);

    //     // Sincroniza roles (reemplaza los anteriores)
    //     $usuario->syncRoles($request->roles);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Roles asignados correctamente'
    //     ]);
    // }

    // con radio 
    public function asignarRol(Request $request)
{
    $request->validate([
        'user_id' => 'required',
        'rol'     => 'required'
    ]);

    $usuario = User::findOrFail($request->user_id);

    // Asignar un solo rol
    $usuario->syncRoles([$request->rol]);

    return response()->json([
        'success' => true,
        'message' => 'Rol asignado correctamente'
    ]);
}

}
