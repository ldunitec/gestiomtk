<?php

namespace Modules\Impresion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Impresion\Entities\Crey;
use Modules\Impresion\Imports\ImpresionesImport;


class CreyController extends Controller
{


    /**
     * Display a listing of the resource.
     */
 // cámbialo por tu modelo real

public function index()
{
    // Conteo de pendientes por curso y sección
   $estadisticas = Crey::select(
        'curso',
        'seccion',
        'modalidad',   
             DB::raw("
            CASE 
                WHEN curso = 'SEPTIMO' THEN 1
                WHEN curso = 'OCTAVO' THEN 2
                WHEN curso = 'NOVENO' THEN 3
                WHEN curso = 'DECIMO' THEN 4
                WHEN curso = 'UNDECIMO' THEN 5
                WHEN curso = 'DUODECIMO' THEN 6
            END as orden_curso
        "),
        DB::raw("SUM(CASE WHEN observaciones = 'Pendiente' THEN 1 ELSE 0 END) as pendientes"),
        DB::raw("SUM(CASE WHEN observaciones != 'Pendiente' THEN 1 ELSE 0 END) as completos")
    )
    ->groupBy('curso', 'seccion','modalidad')
    ->orderBy('orden_curso')
    ->orderBy('seccion')
    ->get();
      
    $totales = Crey::select(
        DB::raw("SUM(CASE WHEN observaciones = 'Pendiente' THEN 1 ELSE 0 END) as total_pendientes"),
        DB::raw("SUM(CASE WHEN observaciones != 'Pendiente' THEN 1 ELSE 0 END) as total_completos")
    )
    ->first();

    // Tus registros normales (si los usas en otra tabla)
    $items = Crey::all();


        $creydatos = Crey::all();
        return view('impresion::admin.creys.index', compact('items', 'estadisticas','creydatos','totales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Crey $creydata)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crey $creydata)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
{

// dd($request);
    $item = Crey::findOrFail($id);

    $request->validate([
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dni' => 'required|string|max:20',
        'curso' => 'required|string|max:50',
        'seccion' => 'required|string|max:50',
        'modalidad' => 'required|string|max:50',
    ]);

    $item->update($request->all());

    return back()->with('success', 'Registro actualizado correctamente');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crey $crey)
    {
        Crey::findOrFail($crey->id)->delete();
     return redirect()->back()->with('success', 'Registro eliminado correctamente');
    }

    public function importar(Request $request)
    {

    // dd(class_exists(\Maatwebsite\Excel\Facades\Excel::class));
        // Validar que el archivo sea un crey
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('import_file');
            
            Excel::import(new ImpresionesImport, $file);

            return redirect()->back()->with('success', '¡Datos importados con éxito!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error en la importación: ' . $e->getMessage());
        }
    }
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // /**
    //  * Display a listing of the resource.
    //  * @return Renderable
    //  */
    // public function index()
    // {
    //     return view('impresion::index');
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  * @return Renderable
    //  */
    // public function create()
    // {
    //     return view('impresion::create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  * @param Request $request
    //  * @return Renderable
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Show the specified resource.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function show($id)
    // {
    //     return view('impresion::show');
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function edit($id)
    // {
    //     return view('impresion::edit');
    // }

    // /**
    //  * Update the specified resource in storage.
    //  * @param Request $request
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function destroy($id)
    // {
    //     //
    // }

