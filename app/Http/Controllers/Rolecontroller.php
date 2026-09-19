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
        return view('admin.' . self::sms . '.create');
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
    public function edit($id)
    {
        $rol = Role::findOrFail($id);
        return view('admin.' . self::sms . '.edit', compact('rol'));

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




    // public function generate()
// {
//     try {
//         Artisan::call('permission:cache-reset');
//         Artisan::call('permission:from-routes');

    //         $output = Artisan::output();

    //         return back()->with([
//             'success' => 'Permisos generados correctamente',
//             'output'  => $output,
//         ]);
//     } catch (\Throwable $e) {
//         return back()->with('error', $e->getMessage());
//     }
// }



    public function generate()
    {
        Artisan::call('permission:cache-reset');
        Artisan::call('permission:from-routes');

        $permissions = Permission::latest()->pluck('name');

        // Mostrar en logs (como consola)
        foreach ($permissions as $permission) {
            logger()->info("Permiso: {$permission}");
        }

        return back()->with([
            'success' => 'Permisos generados',
            'output' => $permissions->implode("\n"),
        ]);
    }
}
