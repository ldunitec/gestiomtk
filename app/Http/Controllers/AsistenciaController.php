<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\ConfigAsistencia;
use App\Models\Empleado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
 

// public function index(Request $request)
// {
//     $config = ConfigAsistencia::first();

// if (!$config) {
//     $config = new ConfigAsistencia();
// }
//     // $empleados = Empleado::all();
//      $empleados = Empleado::orderBy('nombres')->get();
//        $asistencias = [];

//     $query = Asistencia::query();

//     // 🔎 FILTRO POR FECHA
//     if ($request->filled('desde') && $request->filled('hasta')) {
//         $query->whereBetween('fecha', [
//             $request->desde,
//             $request->hasta
//         ]);
//     }


//         $registros = Asistencia::with('empleado')
//             ->select('empleado_id', 'fecha')
//             ->when($request->desde && $request->hasta, function ($q) use ($request) {
//                 $q->whereBetween('fecha', [$request->desde, $request->hasta]);
//             })
//             ->groupBy('empleado_id', 'fecha')
//             ->orderBy('fecha')
//             ->get();

//         $asistencias = [];
//         $ciclo = ConfigAsistencia::first();

//         foreach ($registros as $r) {

//             $nombreEmpleado = $r->empleado
//                 ? $r->empleado->nombres . ' ' . $r->empleado->apellidos
//                 : 'Empleado no registrado';


//             // ENTRADA
//             $entrada = Asistencia::where('empleado_id', $r->empleado_id)
//                 ->where('fecha', $r->fecha)
//                 ->whereBetween('hora', [
//                     $config->entrada_desde,
//                     $config->entrada_hasta
//                 ])
//                 ->min('hora');


//             // SALIDA
//             $salida = Asistencia::where('empleado_id', $r->empleado_id)
//                 ->where('fecha', $r->fecha)
//                 ->whereBetween('hora', [
//                     $config->salida_desde,
//                     $config->salida_hasta
//                 ])
//                 ->max('hora');

//             // ESTADO ENTRADA
//             $limite = Carbon::parse($config->hora_oficial)
//                 ->addMinutes($config->minutos_permitidos);

//             if (!$entrada) {
//                 $estadoEntrada = '❌ Sin entrada';
//             } elseif ($entrada > $limite->format('H:i:s')) {
//                 $estadoEntrada = '⏰ Tarde';
//             } else {
//                 $estadoEntrada = '✅ A tiempo';
//             }



//             // ESTADO SALIDA
//             $horaSalidaOficial = Carbon::parse($config->hora_salida);

//             if (!$salida) {
//                 $estadoSalida = '❌ Sin salida';
//             } elseif ($salida < $horaSalidaOficial->format('H:i:s')) {
//                 $estadoSalida = '⏰ Salida anticipada';
//             } else {
//                 $estadoSalida = '✅ Salida normal';
//             }

//             // HORAS TRABAJADAS
//             $horasTrabajadas = null;
//             if ($entrada && $salida) {
//                 $horasTrabajadas = Carbon::parse($entrada)
//                     ->diff(Carbon::parse($salida))
//                     ->format('%H:%I');
//             }
//             $horaLimite = Carbon::parse($config->hora_oficial)
//                 ->addMinutes($config->minutos_permitidos);


//             //TIEMPO TARDE
//             $tiempoTardeMinutos = 0;

//             if ($entrada) {
//                 $horaEntrada = Carbon::parse($entrada);

//                 if ($horaEntrada->gt($horaLimite)) {
//                     $tiempoTardeMinutos = $horaLimite->diffInMinutes($horaEntrada)
//                         + ($config->minutos_permitidos);
//                 }
//             }


//             $asistencias[] = [
//                 'empleado_id' => $r->empleado_id,
//                 'empleado_nombre' => $nombreEmpleado,
//                 'fecha' => $r->fecha,
//                 'entrada' => $entrada,
//                 'salida' => $salida,
//                 'hora_limite' => $horaLimite->format('H:i'), // 👈 08:06
//                 'tiempo_tarde' => $tiempoTardeMinutos,
//                 'estado_entrada' => $estadoEntrada,
//                 'estado_salida' => $estadoSalida,
//                 'horas_trabajadas' => $horasTrabajadas
//             ];

//         }
//         $asistencias = [];
//         return view('admin.marcaciones.index', compact(
//             'asistencias',
//             'config',
//             'ciclo'
//         ));
//     } //


public function index(Request $request)
{
    $config = ConfigAsistencia::first() ?? new ConfigAsistencia();
    $empleados = Empleado::all();

    $query = Asistencia::with('empleado');

    if ($request->filled('desde') && $request->filled('hasta')) {
        $query->whereBetween('fecha', [$request->desde, $request->hasta]);
    }else {

    // 🔥 Por defecto últimos 30 días
    $query->where('fecha', '>=', now()->subDays(30)->toDateString());
}

    $registros = $query
        ->orderBy('fecha')
        ->orderBy('hora')
        ->get()
        ->groupBy(['empleado_id', 'fecha']);

    $asistencias = [];
      $ciclo = ConfigAsistencia::first();

    foreach ($registros as $empleadoId => $fechas) {

        foreach ($fechas as $fecha => $marcaciones) {

            $empleado = $marcaciones->first()->empleado;

            $nombreEmpleado = $empleado
                ? $empleado->nombres . ' ' . $empleado->apellidos
                : 'Empleado no registrado';

            // ENTRADA (mínima hora dentro del rango)
            $entrada = $marcaciones
                ->whereBetween('hora', [$config->entrada_desde, $config->entrada_hasta])
                ->min('hora');

            // SALIDA (máxima hora dentro del rango)
            $salida = $marcaciones
                ->whereBetween('hora', [$config->salida_desde, $config->salida_hasta])
                ->max('hora');

            // LIMITE ENTRADA
            $horaLimite = Carbon::parse($config->hora_oficial)
                ->addMinutes($config->minutos_permitidos);

            // ESTADO ENTRADA
            if (!$entrada) {
                $estadoEntrada = '❌ Sin entrada';
            } elseif ($entrada > $horaLimite->format('H:i:s')) {
                $estadoEntrada = '⏰ Tarde';
            } else {
                $estadoEntrada = '✅ A tiempo';
            }

            // ESTADO SALIDA
            $horaSalidaOficial = Carbon::parse($config->hora_salida);

            if (!$salida) {
                $estadoSalida = '❌ Sin salida';
            } elseif ($salida < $horaSalidaOficial->format('H:i:s')) {
                $estadoSalida = '⏰ Salida anticipada';
            } else {
                $estadoSalida = '✅ Salida normal';
            }

            // HORAS TRABAJADAS
            $horasTrabajadas = null;
            if ($entrada && $salida) {
                $horasTrabajadas = Carbon::parse($entrada)
                    ->diff(Carbon::parse($salida))
                    ->format('%H:%I');
            }

            // TIEMPO TARDE
            $tiempoTardeMinutos = 0;
            if ($entrada && Carbon::parse($entrada)->gt($horaLimite)) {
                $tiempoTardeMinutos = $horaLimite->diffInMinutes($entrada);
            }

            $asistencias[] = [
                'empleado_id' => $empleadoId,
                'empleado_nombre' => $nombreEmpleado,
                'fecha' => $fecha,
                'entrada' => $entrada,
                'salida' => $salida,
                'hora_limite' => $horaLimite->format('H:i'),
                'tiempo_tarde' => $tiempoTardeMinutos,
                'estado_entrada' => $estadoEntrada,
                'estado_salida' => $estadoSalida,
                'horas_trabajadas' => $horasTrabajadas
            ];
        }
    }

    return view('admin.marcaciones.index', compact(
        'asistencias',
        'empleados',
        'config',
        'ciclo'
    ));
}



    // impiortar
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:txt'
        ]);

        $archivo = file($request->file('archivo')->getRealPath());

        foreach ($archivo as $linea) {

            $linea = trim($linea);
            if (strlen($linea) < 18)
                continue;
            $empleadoRaw = substr($linea, 0, 4);
            // QUITA CEROS A LA IZQUIERDA
            $empleado = intval($empleadoRaw);

            // $empleado = substr($linea, 0, 4);
            $fechaRaw = substr($linea, 4, 8);
            $horaRaw = substr($linea, 12, 6);

            $fecha = Carbon::createFromFormat('Ymd', $fechaRaw)->format('Y-m-d');
            $hora = Carbon::createFromFormat('His', $horaRaw)->format('H:i:s');

            Asistencia::firstOrCreate(
                [
                    'empleado_id' => $empleado,
                    'fecha' => $fecha,
                    'hora' => $hora
                ],
                [
                    'raw' => $linea
                ]
            );

        }

        return back()->with('success', 'Archivo importado correctamente');
    }

    public function configurar(Request $request)
    {
        ConfigAsistencia::updateOrCreate(
            ['id' => 1],
            $request->all()
        );

        return back()->with('success', 'Configuración guardada');
    }











}
