<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AlumnosImport;
use App\Models\Alumno;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AlumnoController extends Controller
{
//     public function index()
//     {
//         $cursos = Alumno::select('curso')->distinct()->pluck('curso');
//         $secciones = Alumno::select('seccion')->distinct()->pluck('seccion');

//         $alumnos = Alumno::all();
//         $dBilingue = DB::table('alumnos')
//             ->select(
//                 DB::raw("CONCAT(curso,' ',seccion) as grado"),
//                 DB::raw('COUNT(*) as total')
//             )
//             ->groupBy('curso', 'seccion')
//             ->orderBy('curso')
//             ->get();

//         // return $dBilingue;
//         $total = Alumno::count();


//         $conteo = Alumno::where('institucion', 'Jardin, Escuela y Liceo Bilingue Cardenal')
//             ->selectRaw('curso, COUNT(*) as total')
//             ->groupBy('curso')
//             ->pluck('total', 'curso');


//         $conteo2 = Alumno::where('institucion', 'Liceo Cardenal “Oscar Andrés Rodríguez”')
//             ->selectRaw("CONCAT(curso,' ',seccion) as grado, COUNT(*) as total")
//             ->groupBy('curso', 'seccion')
//             ->pluck('total', 'grado');

//         $conteo3 = Alumno::where('institucion', 'Liceo Cardenal “Oscar Andrés Rodríguez”')
//             ->selectRaw("
//         curso,
//         SUM(modalidad='B.T.P. INFORMATICA') as informaticaIsemed,
//         SUM(modalidad='B.C.H.') as bachilleratoIsemed
//     ")
//             ->groupBy('curso')
//             ->get()
//             ->keyBy('curso');

// $conteoIsemed = Alumno::where('institucion', 'Liceo Cardenal "Oscar Andrés Rodríguez" ISEMED')
//     ->selectRaw("CONCAT(curso) as grado, COUNT(*) as total")
//             ->groupBy('curso')
//             ->pluck('total', 'grado');

// $conteoIsemed2 = Alumno::where('institucion', 'Liceo Cardenal "Oscar Andrés Rodríguez" ISEMED')
//     ->selectRaw("
//         curso, 
//         SUM(modalidad='B.T.P. INFORMATICA') as informaticaIsemed,
//         SUM(modalidad='BCH ISEMED') as bachilleratoIsemed
//     ")
//     ->groupBy('curso')
//     ->get()
//     ->keyBy('curso');


//         // dd($conteoIsemed);

//         $kinder = $conteo['Kinder'] ?? 0;
//         $preparatoria = $conteo['Preparatoria'] ?? 0;
//         $primero = $conteo['Primero'] ?? 0;
//         $segundo = $conteo['Segundo'] ?? 0;
//         $tercero = $conteo['Tercero'] ?? 0;
//         $cuarto = $conteo['Cuarto'] ?? 0;
//         $quinto = $conteo['Quinto'] ?? 0;
//         $sexto = $conteo['Sexto'] ?? 0;
//         $septimo = $conteo['Séptimo'] ?? 0;
//         $octavo = $conteo['Octavo'] ?? 0;
//         $noveno = $conteo['Noveno'] ?? 0;
//         $septimo1 = $conteo2['Séptimo 1'] ?? 0;
//         $septimo2 = $conteo2['Séptimo 2'] ?? 0;
//         $octavo1 = $conteo2['Octavo 1'] ?? 0;
//         $octavo2 = $conteo2['Octavo 2'] ?? 0;
//         $noveno1 = $conteo2['Noveno 1'] ?? 0;
//         $noveno2 = $conteo2['Noveno 2'] ?? 0;
//         $decimoEB = $conteo3['Decimo']->ejecutivo ?? 0;
//         $decimoHH = $conteo3['Decimo']->bachillerato ?? 0;
//         $decimoINF = $conteo3['Decimo']->informatica ?? 0;

//         $undecimoEB = $conteo3['Undécimo']->ejecutivo ?? 0;
//         $undecimoHH = $conteo3['Undécimo']->bachillerato ?? 0;
//         $undecimoINF = $conteo3['Undécimo']->informatica ?? 0;

//         $duodecimoEB = $conteo3['Duodécimo']->ejecutivo ?? 0;
//         $duodecimoHH = $conteo3['Duodécimo']->bachillerato ?? 0;
//         $duodecimoINF = $conteo3['Duodécimo']->informatica ?? 0;

//         $septimoIsemed = $conteoIsemed['Séptimo'] ?? 0;
//         $octavoIsemed = $conteoIsemed['Octavo'] ?? 0;
//         $novenoIsemed = $conteoIsemed['Noveno'] ?? 0;
//         $decimoHHIsemed = $conteoIsemed2['Decimo']->bachilleratoIsemed ?? 0;
//         $decimoINFIsemed = $conteoIsemed2['Decimo']->informaticaIsemed ?? 0;
//         $undecimoHHIsemed = $conteoIsemed2['Undécimo']->bachilleratoIsemed ?? 0;
//         $undecimoINFIsemed = $conteoIsemed2['Undécimo']->informaticaIsemed ?? 0;
// //   dd($conteo3);
//         return view('admin.alumnos.importar', compact(
//             'alumnos',
//             'total',
//             'kinder',
//             'preparatoria',
//             'primero',
//             'segundo',
//             'tercero',
//             'cuarto',
//             'quinto',
//             'sexto',
//             'septimo1',
//             'septimo2',
//             'septimo',
//             'octavo',
//             'noveno',
//             'dBilingue',
//             'octavo1',
//             'octavo2',
//             'noveno1',
//             'noveno2',
//             'decimoEB',
//             'decimoHH',
//             'decimoINF',
//             'undecimoEB',
//             'undecimoHH',
//             'undecimoINF',
//             'duodecimoEB',
//             'duodecimoINF',
//             'cursos',
//             'secciones',
//             'septimoIsemed',
//             'octavoIsemed',
//             'novenoIsemed',
//             'decimoHHIsemed',
//             'decimoINFIsemed',
//             'undecimoHHIsemed',
//             'undecimoINFIsemed',

//             // 'pendientes'
//         ));
//     }

   
public function index()
{
    $alumnos = Alumno::all();
    $total = $alumnos->count();
   

    $cursos = $alumnos->pluck('curso')->unique()->values();
    $secciones = $alumnos->pluck('seccion')->unique()->values();

    // 📊 General por curso
    $bilingue = Alumno::where('institucion', 'Jardin, Escuela y Liceo Bilingue Cardenal')
        ->selectRaw('curso, COUNT(*) as total')
        ->groupBy('curso')
        ->pluck('total', 'curso');

    // 📊 Por curso + sección
    $gradosEscuela = Alumno::where('institucion', 'Liceo Cardenal “Oscar Andrés Rodríguez”')
        ->selectRaw("CONCAT(curso,'-',seccion,'//',modalidad) as grado, COUNT(*) as total")
        ->groupBy('curso', 'seccion','modalidad')
        ->pluck('total', 'grado');

    // 📊 Modalidades
    $modalidadesEscuela = Alumno::where('institucion', 'Liceo Cardenal “Oscar Andrés Rodríguez”')
        ->selectRaw("
            curso,
            SUM(modalidad='B.T.P. INFORMATICA') as informatica,
            SUM(modalidad='B.C.H.') as bachillerato,
            SUM(modalidad='B.T.P. ASIST. EJEC. BILINGUE') as ejecutivo
        ")
        ->groupBy('curso')
        ->get()
        ->keyBy('curso');

    // 📊 ISEMED
$isemed = Alumno::where('institucion', 'Liceo Cardenal "Oscar Andrés Rodríguez" ISEMED')
    ->selectRaw("CONCAT(curso,'-',seccion,' // ',modalidad) as grado, COUNT(*) as total")
    ->groupBy('curso', 'seccion', 'modalidad')
    ->orderBy('curso')
    ->pluck('total', 'grado');
        

        // dd($isemed);

    // 📊 Gráfica
    $grafica = Alumno::selectRaw("
            CONCAT(curso,' ',seccion) as grado,
            COUNT(*) as total
        ")
        ->groupBy('curso', 'seccion')
        ->orderBy('curso')
        ->get();

    return view('admin.alumnos.importar', compact(
        'alumnos',
        'total',
        'cursos',
        'secciones',
        'bilingue',
        'gradosEscuela',
        'modalidadesEscuela',
        'isemed',
        'grafica'
    ));
}
public function importar(Request $request)
    {
        $archivos = $request->file('archivos');

        foreach ($archivos as $archivo) {

            Excel::import(new AlumnosImport, $archivo);

        }

        return back()->with('success', 'Archivos importados correctamente');
    }
    public function foto()
    {
        return view('admin.alumnos.foto');
    }

    public function store(Request $request)
    {
        $image = $request->imagen;

        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageName = 'carnet_' . time() . '.jpg';

        Storage::disk('public')->put(
            'carnets/' . $imageName,
            base64_decode($image)
        );

        return back()->with('success', 'Foto guardada correctamente');
    }

    // AlumnoController.php
    public function eliminarTodos()
    {
        try {
            Alumno::truncate(); // elimina todo y reinicia ID
            return response()->json(['success' => 'Todos los registros fueron eliminados.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al eliminar los registros.']);
        }
    }

    public function generarPDF()
    {
        // 🔹 1. Traer datos limpios
        $datos = Alumno::whereNotNull('curso')
            ->whereNotNull('seccion')
            ->where('curso', '!=', '')
            ->where('seccion', '!=', '')
            ->get();

        $foto = Alumno::all();
        // 🔹 2. Agrupar por curso + sección
        $grupos = $datos->groupBy(function ($item) {
            return $item->curso . '-' . $item->seccion . '-' . $item->modalidad;
        });

        // 🔹 3. Carpeta destino
        $folderPath = storage_path('app/public/pdfs');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $archivos = [];

        // 🔹 4. Generar PDFs
        foreach ($grupos as $key => $items) {

            $curso = $items->first()->curso;
            $modalidad = $items->first()->modalidad;
            $seccion = $items->first()->seccion;
            $institucion = $items->first()->institucion;

            // 🔴 Validación extra de seguridad
            if (empty($curso) || empty($seccion)) {
                continue;
            }

            $pdf = Pdf::loadView('admin.alumnos.pdfCursos', [
                'registros' => $items,
                'modalidad' => $modalidad,
                'curso' => $curso,
                'seccion' => $seccion,
                'institucion' => $institucion,
            ])->setPaper('letter', 'portrait');

            // 🔹 5. Nombre limpio
            $nombre = "Curso_" . Str::slug($curso) . "_Seccion_" . Str::slug($seccion) . "_Modalidad_" . Str::slug($modalidad) . ".pdf";

            $ruta = $folderPath . '/' . $nombre;

            $pdf->save($ruta);

            $archivos[] = $ruta;
        }

        // 🔹 6. Respuesta
        return response()->json([
            'mensaje' => 'PDFs generados correctamente',
            'total' => count($archivos),
            'archivos' => $archivos,
            'foto' => $foto
        ]);
    }
 public function destroy($id)
    {
        Alumno::destroy($id);
        return back()->with('success', 'Archivo eliminado');
    }


}