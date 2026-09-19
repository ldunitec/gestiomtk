<?php
namespace Modules\NetLan\Http\Controllers;


use App\Models\User;
    use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\NetLan\Entities\Departamento;
use Modules\NetLan\Entities\Empleado;
use PhpOffice\PhpSpreadsheet\Writer\Pdf as WriterPdf;
use Yajra\DataTables\Facades\DataTables;

class EmpleadoController extends Controller
{





public function index(Request $request)
{
    if ($request->ajax()) {
        $query = Empleado::with(['usuario', 'departamento']);

        // Calcular contadores generales
        $totalCount     = Empleado::count();
        $activosCount   = Empleado::where('estado', 'activo')->count();
        $inactivosCount = Empleado::where('estado', 'inactivo')->count();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('departamento', function ($row) {
                return $row->departamento ? $row->departamento->nombre : null;
            })
            // AGREGAR VALORES EXTRA A LA RESPUESTA JSON:
            ->with([
                'totalCount'     => $totalCount,
                'activosCount'   => $activosCount,
                'inactivosCount' => $inactivosCount,
            ])
            ->make(true);
    }

    $departamentos = Departamento::all();
    return view('netlan::admin.empleados.index', compact('departamentos'));
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
        'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        'fotoDni' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        'fotoLic' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $data = $request->except(['email']); // mejor práctica

    // 📸 Guardar foto
    if ($request->hasFile('foto')) {

        $file = $request->file('foto');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('empleados', $nombre, 'public');

        $data['foto'] = 'empleados/' . $nombre;
    }

     if ($request->hasFile('fotoDni')) {

        $file = $request->file('fotoDni');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('empleados/dni/', $nombre, 'public');

        $data['fotoDni'] = 'empleados/dni/' . $nombre;
    }

     if ($request->hasFile('fotoLic')) {

        $file = $request->file('fotoLic');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('empleados/lic/', $nombre, 'public');

        $data['fotoLic'] = 'empleados/lic/' . $nombre;
    }

    // 🔐 Crear usuario
    $user = User::create([
        'name' => $request->nombres . ' ' . $request->apellidos,
        'email' => $request->email,
        'password' => Hash::make('12345678')
    ]);

    $user->assignRole('EMPLEADO');

    $data['usuario_id'] = $user->id;

    // dd($request->file('foto'));

    // ✅ AQUÍ ESTÁ LO QUE TE FALTA
    Empleado::create($data);

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}



public function update(Request $request, Empleado $empleado)
{
    $empleado->load('usuario');

    $request->validate([
        'nombres' => 'required',
        'apellidos' => 'required',
        'departamento_id' => 'required',
    ]);

    // Inicializamos el array de datos con los valores del request
    $data = $request->only([
        'nombres', 'apellidos', 'fecha_nacimiento', 'telefono', 
        'departamento_id', 'reloj', 'estado', 'direccion', 
        'fecha_inicio', 'puesto'
    ]);

    // 📸 Manejo de FOTO
    if ($request->hasFile('foto')) {
        // 1. Eliminar anterior si existe
        if ($empleado->foto && Storage::disk('public')->exists($empleado->foto)) {
            Storage::disk('public')->delete($empleado->foto);
        }
        // 2. Guardar nueva
        $file = $request->file('foto');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('empleados', $nombre, 'public');
        $data['foto'] = 'empleados/' . $nombre;
    }

    // 🪪 Manejo de FOTO DNI
    if ($request->hasFile('fotoDni')) {
        if ($empleado->fotoDni && Storage::disk('public')->exists($empleado->fotoDni)) {
            Storage::disk('public')->delete($empleado->fotoDni);
        }
        $file = $request->file('fotoDni');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('empleados/dni', $nombre, 'public');
        $data['fotoDni'] = 'empleados/dni/' . $nombre;
    }

    // 🪪 Manejo de FOTO LICENCIA
    if ($request->hasFile('fotoLic')) {
        if ($empleado->fotoLic && Storage::disk('public')->exists($empleado->fotoLic)) {
            Storage::disk('public')->delete($empleado->fotoLic);
        }
        $file = $request->file('fotoLic');
        $nombre = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('empleados/lic', $nombre, 'public');
        $data['fotoLic'] = 'empleados/lic/' . $nombre;
    }

    // Actualizar empleado con el array $data acumulado
    $empleado->update($data);

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



public function destroy(Empleado $empleado) // Cambié $id por $empleado para mayor claridad
{
    // 1. Definir los campos que tienen archivos
    $camposArchivos = ['foto', 'fotoDni', 'fotoLic'];

    // 2. Recorrer y eliminar del disco 'public'
    foreach ($camposArchivos as $campo) {
        if ($empleado->$campo && Storage::disk('public')->exists($empleado->$campo)) {
            Storage::disk('public')->delete($empleado->$campo);
        }
    }

    // 3. Eliminar el registro de la base de datos
    $empleado->delete();

    // 4. Respuesta (Como usas AJAX en el frontend, lo ideal es retornar JSON)
    return response()->json(['success' => true, 'message' => 'Empleado y archivos eliminados.']);
}

    public function data()
    {
        $empleados = Empleado::all();

        return response()->json([
            'data' => $empleados->toArray()
        ]);
    }



    public function ficha($id)
    {
        $empleado = Empleado::findOrFail($id);

        // return $empleado;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('netlan::admin.empleados.ficha', compact('empleado'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('ficha_empleado.pdf');
    }

    /**
     * Obtener métricas y estadísticas de empleados para las tarjetas Dashboard/Index.
     */
    public function estadisticas(): \Illuminate\Http\JsonResponse
    {
        $total = Empleado::count();
        $activos = Empleado::where('estado', 'Activo')->count();
        $inactivos = Empleado::where('estado', '!=', 'Activo')->count();
     
        // Si manejas impresión de carnet/tarjeta
        $pendientes = Empleado::whereNull('foto')->orWhere('foto', '')->count();

        return response()->json([
            'total'      => $total,
            'activos'    => $activos,
            'inactivos'  => $inactivos,
            'pendientes' => $pendientes,
        ]);
    }
}
