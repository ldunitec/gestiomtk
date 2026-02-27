<?php
namespace App\Http\Controllers;

use App\Models\empleado;
use App\Models\Departamento;
use App\Models\Empleado as ModelsEmpleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{

    // public function index(Request $request)
// {
//     if ($request->ajax()) {

    //         $empleados = Empleado::with('departamento')->get();

    //         return datatables()->of($empleados)
//             ->addIndexColumn()
//             ->addColumn('departamento', function ($row) {
//                 return $row->departamento->nombre ?? 'Sin departamento';
//             })
//             ->addColumn('estado_badge', function ($row) {
//                 if ($row->estado == 'Activo') {
//                     return '<span class="badge badge-success">Activo</span>';
//                 } else {
//                     return '<span class="badge badge-danger">Inactivo</span>';
//                 }
//             })
//             ->addColumn('acciones', function ($row) {
//                 return '
//                     <button class="btn btn-sm btn-warning edit" data-id="'.$row->id.'">
//                         <i class="fas fa-edit"></i>
//                     </button>
//                     <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">
//                         <i class="fas fa-trash"></i>
//                     </button>
//                 ';
//             })
//             ->rawColumns(['estado_badge','acciones'])
//             ->make(true);
//     }

    //     return view('admin.empleados.index');
// }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $empleados = Empleado::with('departamento')->get();

            return datatables()->of($empleados)
                ->addIndexColumn()
                ->addColumn('departamento', function ($row) {
                    return $row->departamento->nombre ?? 'Sin departamento';
                })
                ->addColumn('estado_badge', function ($row) {
                    if ($row->estado == 'Activo') {
                        return '<span class="badge badge-success">Activo</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactivo</span>';
                    }
                })
                ->addColumn('acciones', function ($row) {
                    return '
                    <button class="btn btn-sm btn-warning edit" id=btnEdit data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['estado_badge', 'acciones'])
                ->make(true);
        }

        $departamentos = Departamento::all();
        // return $departamentos;
        return view('admin.empleados.index', compact('departamentos'));
    }
    public function create()
    {
        $departamentos = Departamento::all();
        $empleados = Empleado::all();
        return view('admin.empleados.create', compact('empleados', 'departamentos'));
    }

    // public function store(Request $request)
    // {

    //     // $datos = $request->all();
    //     // return response()->json($datos);
    //     $request->validate([
    //         'nombres' => 'required|string|max:255',
    //         'apellidos' => 'required|string|max:255',
    //         'dni' => 'required|string|max:255',
    //         'telefono' => 'required|string|max:255',
    //         'reloj' => 'required|string|max:255',
    //         'fecha_nacimiento' => 'string|max:255',
    //         'departamento_id' => 'required|string|max:255',
    //         'estado' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //     ]);

    //     $usuario = new User();
    //     $usuario->name = $request->nombres . ' ' . $request->apellidos;
    //     $usuario->email = $request->email;
    //     $usuario->password = Hash::make('12345678');
    //     $usuario->force_password_change = true;
    //     $usuario->save();
    //     $usuario->syncRoles('EMPLEADO ');

    //     $empleado = new Empleado();
    //     $empleado->usuario_id = $usuario->id;
    //     $empleado->nombres = $request->nombres;
    //     $empleado->apellidos = $request->apellidos;
    //     $empleado->dni = $request->dni;
    //     $empleado->telefono = $request->telefono;
    //     $empleado->reloj = $request->reloj;
    //     $empleado->fecha_nacimiento = $request->fecha_nacimiento;
    //     $empleado->departamento_id = $request->departamento_id;
    //     $empleado->estado = $request->estado;
    //     $empleado->save();

    //     return redirect()->route('admin.empleados.index')->with('success', 'Empleado creado con éxito.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'email' => 'required|email|unique:users,email',
            'dni' => 'required|unique:empleados,dni',
            'departamento_id' => 'required',
        ]);

        // 1 Crear usuario
        $user = User::create([
            'name' => $request->nombres . ' ' . $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make('12345678')
        ]);

        $user->assignRole('EMPLEADO');

        //  Crear empleado vinculado
        Empleado::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'departamento_id' => $request->departamento_id,
            'reloj' => $request->reloj,
            'estado' => $request->estado,
            'usuario_id' => $user->id, // 🔥 CLAVE AQUÍ
        ]);

        return response()->json(['success' => true]);
    }

 public function update(Request $request, Empleado $empleado)
{
    $empleado->load('usuario'); // cargar usuario

    $request->validate([
        'nombres' => 'required',
        'apellidos' => 'required',
        'departamento_id' => 'required',
    ]);

    // actualizar empleado
    $empleado->update([
        'nombres' => $request->nombres,
        'apellidos' => $request->apellidos,
        'fecha_nacimiento' => $request->fecha_nacimiento,
        'telefono' => $request->telefono,
        'departamento_id' => $request->departamento_id,
        'reloj' => $request->reloj,
        'estado' => $request->estado,
    ]);

    return response()->json(['success' => true]);
}

    public function edit($id)
    {
        $empleado = Empleado::with('usuario')->findOrFail($id);
        $departamentos = Departamento::all();
        return response()->json([
            'empleado' => $empleado,
            'departamentos' => $departamentos
        ]);
    }

    public function show(Empleado $empleado)
    {
        // return view('admin.empleados.show', compact('empleado'));
    }

    //     public function edit(Request $request, $id)
//     {
//         $departamentos = Departamento::all();
//         $empleado = Empleado::find($id);

    //         return view('admin.empleados.edit', compact('departamentos', 'empleado'));
//         // $datos = $id;
//         // return response()->json($empleado);
//     }


    //     public function update(Request $request, $id)
//     {
//         // Buscar el empleado
//         $empleado = Empleado::findOrFail($id);

    //         // Validación
//         $request->validate([
//             'nombres' => 'required|string|max:255',
//             'apellidos' => 'required|string|max:255',
//             'dni' => 'required|string|max:255',
//             'telefono' => 'required|string|max:255',
//             'reloj' => 'required|string|max:255',
//             'fecha_nacimiento' => 'nullable|date',
//             'departamento_id' => 'required|integer|exists:departamentos,id',
//             'estado' => 'nullable|string', // Ajusta según tus estados
//             'email' => 'required|email|unique:users,email,' . $empleado->usuario->id,
//         ]);

    //         // Actualizar usuario relacionado
// // return  $empleado->usuario_id;
//         $usuario = $empleado->usuario;
//         $usuario->name = $request->nombres . ' ' . $request->apellidos;
//         $usuario->email = $request->email;
//         // Si deseas actualizar la contraseña basada en el DNI otra vez, descomenta:
//         // $usuario->password = Hash::make($request->dni);
//         $usuario->save();

    //         // Actualizar empleado
//         $empleado->update($request->only([
//             'nombres',
//             'apellidos',
//             'dni',
//             'telefono',
//             'reloj',
//             'fecha_nacimiento',
//             'departamento_id',
//             'estado'
//         ]));

    //         return redirect()->route('admin.empleados.index')
//             ->with('mensaje', 'Empleado actualizado correctamente')
//             ->with('icono', 'success');
//     }


    public function destroy(Empleado $id)
    {
        $id->delete();
        return redirect()->route('admin.empleados.index')->with('success', 'Empleado eliminado.');
    }

    public function data()
    {
        $empleados = Empleado::all();

        return response()->json([
            'data' => $empleados->toArray()
        ]);
    }
}
