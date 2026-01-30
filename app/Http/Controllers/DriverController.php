<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\DriverFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * LISTADO
     */
    public function index()
    {
        $files = DriverFile::with([
            'deviceModel.brand',
            'deviceModel.category'
        ])->get();

        //   $datos = $request->all();
            // return response()->json($files);

        return view('admin.drivers.index', compact('files'));
    }

    /**
     * FORMULARIO CREAR
     */
    public function create()
    {
        return view('admin.drivers.create', [
            'brands' => Brand::all(),
            'categories' => Category::all(),
            'models' => DeviceModel::with('brand', 'category')->get()
        ]);
    }

    /**
     * GUARDAR ARCHIVOS
     */
    public function store(Request $request)
    {
        $request->validate([
            'model_id' => 'required|exists:models,id',
            'windows'  => 'nullable|file|max:51200',
            'mac'      => 'nullable|file|max:51200',
            'manual'   => 'nullable|file|max:51200',
        ]);

        $types = ['windows', 'mac', 'manual'];

        foreach ($types as $type) {

            if ($request->hasFile($type)) {

                // 📁 Guardar local
                $localPath = $request->file($type)
                    ->store("drivers/{$request->model_id}", 'local');

                // ☁ Respaldar en Google Drive
                // $drivePath = Storage::disk('google')->put(
                //     "drivers/{$request->model_id}",
                //     fopen(storage_path("app/{$localPath}"), 'r')
                // );

                DriverFile::create([
                    'model_id'  => $request->model_id,
                    'type'      => $type,
                    'file_path' => $localPath,
                    // 'drive_path'=> $drivePath
                ]);
            }
        }

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', 'Archivos guardados correctamente');
    }

    /**
     * DESCARGAR ARCHIVO
     */
    public function download(DriverFile $file)
    {
        return Storage::download($file->file_path);
    }

    /**
     * ELIMINAR ARCHIVO
     */
    public function destroy(DriverFile $file)
    {
        // eliminar local
        Storage::delete($file->file_path);

        // eliminar drive si existe
        if ($file->drive_path) {
            Storage::disk('google')->delete($file->drive_path);
        }

        $file->delete();

        return back()->with('success', 'Archivo eliminado');
    }
}

