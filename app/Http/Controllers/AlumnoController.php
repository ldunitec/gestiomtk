<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AlumnosImport;
use App\Models\Alumno;
use Maatwebsite\Excel\Facades\Excel;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::all();
        // return $alumnos;
        return view('admin.alumnos.importar',compact('alumnos'));
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new AlumnosImport, $request->file('archivo'));
     

        return back()->with('success', 'Importación completada');
    }
}