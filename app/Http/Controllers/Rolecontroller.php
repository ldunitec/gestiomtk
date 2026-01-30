<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;

class Rolecontroller extends Controller
{
    const sms = 'roles';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

            $roles = Role::all();
            return view('admin.roles.index', compact('roles'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.'.self::sms.'.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $datos = $request->all();
        // return response()->json($datos);
        $request->validate([
            'name' => 'required|unique:roles',
            'guard_name' => 'web', 
        ]);

         Role::create($request->all());
         return redirect()->route('admin.' . self::sms . '.index')
         ->with('mensaje', self::sms . ' creado correctamente')
         ->with('icono', 'success');
    }

 public function permisos($id)
    {
        $nombresGrupos = [
            'usuarios' => 'Gestión de Usuarios',
            'roles' => 'Gestión de Roles',
            'productos' => 'Inventario de Productos',
            // agrega los que necesites
        ];

        $rol = Role::find($id);
        $permisosSeleccionados = $rol->permissions->pluck('id')->toArray();
        // Agrupar permisos por prefijo del name (antes del punto)
        $permisos = Permission::orderBy('name')->get()->groupBy(function ($permiso) {
            return explode('.', $permiso->name)[1]; // ejemplo: 'usuarios' de 'usuarios.edit'
        });
                // return response()->json($permisosSeleccionados);

        return view('admin.roles.permisos', compact('rol', 'permisos', 'permisosSeleccionados', 'nombresGrupos'));
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $rol = Role::findOrFail($id);
        return view('admin.'.self::sms.'.edit', compact('rol'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:roles',
            'guard_name' => 'web', 
        ]);
        $rol = Role::findOrFail($id);
        $rol->update($request->all());
         return redirect()->route('admin.' . self::sms . '.index')
         ->with('mensaje', self::sms . ' creado correctamente')
         ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rol = Role::findOrFail($id);
        $rol->delete();

        return redirect()->route('admin.' . self::sms . '.index')
        ->with('mensaje', self::sms . ' eliminado correctamente')
        ->with('icono', 'success');
    }

     public function actualizarPermisos(Request $request, $id)
    {
 // $datos = $request->all();
        // return response()->json($datos);

        $rol = Role::findOrFail($id);
 
        // Si no se selecciona ningún permiso, pasar array vacío
        $permisosIds = $request->input('permisos', []);
           // Filtra solo los permisos válidos según el guard del rol
    $permisosValidos = Permission::whereIn('id', $permisosIds)
        ->where('guard_name', $rol->guard_name)
        ->pluck('id')
        ->toArray();
        // Sincroniza los permisos con el rol
        $rol->syncPermissions([$permisosValidos]);
        
        return redirect()->route('admin.roles.index')
            ->with('mensaje', 'Permisos actualizados correctamente.')
            ->with('icono', 'success');
    }


public function generate()
    {
        // Ejecutar ambos comandos
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        \Illuminate\Support\Facades\Artisan::call('permission:from-routes');

        // Opcional: mensaje para la vista
        $output = \Illuminate\Support\Facades\Artisan::output();

        return redirect()->back()->with('success', "✅ Permisos generados y cache reseteado:\n" . $output);
    }
    }


//////////////////////////////////////

// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

// class RoleController extends Controller
// {
//     public function index()
//     {
//         $roles = Role::withCount('permissions')->get();
//         return view('admin.roles.index', compact('roles'));
//     }

//     public function create()
//     {
//         $permissions = Permission::all();
//         return view('admin.roles.create', compact('permissions'));
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|unique:roles,name',
//             'permissions' => 'nullable|array'
//         ]);

//         $role = Role::create(['name' => $request->name]);
//         $role->syncPermissions($request->permissions);

//         return redirect()->route('roles.index')
//             ->with('success', 'Rol creado correctamente');
//     }

//     public function edit(Role $role)
//     {
//         $permissions = Permission::all();
//         $rolePermissions = $role->permissions->pluck('name')->toArray();

//         return view('admin.roles.edit', compact(
//             'role',
//             'permissions',
//             'rolePermissions'
//         ));
//     }

//     public function update(Request $request, Role $role)
//     {
//         $request->validate([
//             'name' => 'required|unique:roles,name,' . $role->id,
//             'permissions' => 'nullable|array'
//         ]);

//         $role->update(['name' => $request->name]);
//         $role->syncPermissions($request->permissions);

//         return redirect()->route('roles.index')
//             ->with('success', 'Rol actualizado');
//     }

//     public function destroy(Role $role)
//     {
//         $role->delete();
//         return response()->json(['success' => true]);
//     }
// }

