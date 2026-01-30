<?php

namespace App\Http\Controllers;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = File::latest()->get();
        return view('admin.files.index', compact('files'));
// $tipos = File::where('brand', 'evolis')
//     ->select('descrip')
//     ->distinct()
//     ->get();


// return response()->json($driversEvolis);



    }

    public function create()
    {
        return view('admin.files.create');
    }

    public function store(Request $request)
    {
        // $request->validate([
        //  'file' => 'file|max:307200'
        // ]);

        // $datos = $request;
        // return response()->json($datos);

        // $uploadedFile = $request->file('file');
        // $path = $uploadedFile->store('files');

        // return $request;
// _______________________________________________________
        File::create([
            // 'name'     => $uploadedFile->getClientOriginalName(),
            'name'    => $request->name,
            'brand_id'    => $request->brand_id,
            'category' => $request->category,
            'model'    => $request->model,
            'type'   => strtolower(trim($request->descrip)),
            'link_drive' => $request->link_drive,
            // 'path'     => $path,
            // 'type'     => $uploadedFile->extension(),
        ]);

     return redirect()->back()->with('success', 'Registro guardado correctamente');

 }

   // app/Http/Controllers/FileController.php

    public function download(File $file)
    {
        // Si usas storage local
        return response()->download(storage_path('app/'.$file->link_drive));
    }



    // public function destroy(File $file)
    // {
    //     Storage::delete($file->path);
    //     $file->delete();

    //     return back()->with('success', 'Archivo eliminado');
    // }

    public function destroy($id)
{
    $file = File::findOrFail($id);
return response()->json($file);
    // if ($file->path && Storage::exists($file->path)) {
    //     Storage::delete($file->path);
    // }

    // $file->delete();

    // return back()->with('success', 'Archivo eliminado correctamente');
}

}
