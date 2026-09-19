<?php
namespace Modules\Documentacion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Documentacion\Entities\Documentacion;
use Modules\Documentacion\Entities\Seccion;
use Barryvdh\DomPDF\Facade\Pdf;
 use Illuminate\Support\Facades\File;

class DocumentacionController extends Controller
{
    public function index()
    {
        $docs = Documentacion::latest()->get();
        return view('documentacion::admin.documentaciones.index', compact('docs'));
    }

    public function create()
    {
        return view('documentacion::admin.documentaciones.create');
    }

    public function store(Request $request)
    {
        $doc = Documentacion::create($request->all());
        return redirect()->route('documentacion.admin.documentaciones.edit', $doc->id);
    }

    public function edit($id)
    {
        $doc = Documentacion::with('secciones')->findOrFail($id);
        return view('documentacion::admin.documentaciones.edit', compact('doc'));
    }

    public function addSeccion(Request $request)
    {
        Seccion::create($request->all());
        return back();
    }

    public function pdf($id)
    {
        $doc = Documentacion::with('secciones')->findOrFail($id);

        $pdf = Pdf::loadView('documentacion::admin.documentaciones.pdf', [
            'documentacion' => $doc,
            'secciones' => $doc->secciones
        ]);

        return $pdf->stream('documentacion.admin.documentaciones.pdf');
    }
   

public function importar(Request $request)
{
    $tipo = $request->tipo;
    $nombre = $request->nombre;

    switch ($tipo) {
        case 'modelo':
            $path = app_path("Models/$nombre.php");
            break;

        case 'controlador':
            $path = app_path("Http/Controllers/$nombre.php");
            break;

        case 'migracion':
            $files = File::files(database_path('migrations'));
            foreach ($files as $file) {
                if (str_contains($file->getFilename(), $nombre)) {
                    $path = $file->getPathname();
                    break;
                }
            }
            break;
    }

    if (!isset($path) || !File::exists($path)) {
        return response()->json(['error' => 'Archivo no encontrado']);
    }

    return response()->json([
        'contenido' => File::get($path)
    ]);
}
}