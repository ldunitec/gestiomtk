<?php

namespace App\Http\Controllers;

use App\Imports\CreyImport;
use App\Models\Crey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class CreyController extends Controller
{

public function index()
{
    $crey = Crey::count();
    $creys = Crey::all();

    // Contar todos los alumnos por curso
      $cursos = DB::table('creys')
            ->select(
                DB::raw("CONCAT(seccion,' ',modalidad) as curso"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy( 'modalidad','seccion')
            ->orderBy('modalidad')
            ->get();

            // dd($cursos);
    return view('admin.alumnos.crey', compact('crey','creys','cursos'));
}
    


    public function importar(Request $request)
    {



        $request->validate([
            'archivos' => 'required'
        ]);

        $archivos = $request->file('archivos');

        foreach ($archivos as $archivo) {

            Excel::import(new CreyImport, $archivo);

        }

        return back()->with('success','Archivos importados correctamente');

    }

     public function eliminarTodos()
    {
        try {
            Crey::truncate(); // elimina todo y reinicia ID
            return response()->json(['success' => 'Todos los registros fueron eliminados.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al eliminar los registros.']);
        }
    }



public function imprimirPorCurso()
{
    $alumnos = Crey::all()->groupBy(function ($item) {
        return $item->curso . '-' . $item->seccion;
    });
// dd($alumnos);
    foreach ($alumnos as $grupo => $items) {

        [$curso, $seccion] = explode('-', $grupo);

        $pdf = Pdf::loadView('admin.alumnos.crey_pdf', [
            'alumnos' => $items,
            'curso' => $curso,
            'seccion' => $seccion,

        ]);

        // Guardar cada PDF
        $pdf->save(public_path("pdf/{$curso}_{$seccion}.pdf"));
        // return $pdf->download("{$curso}_{$seccion}.pdf");
    }

    return "PDFs generados correctamente";
}

}