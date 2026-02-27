<?php

namespace App\Http\Controllers;

use App\Models\InventarioBTR;
use App\Http\Controllers\Controller;
use App\Models\DescargaInventarioBTR;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventarioBTRController extends Controller
{



    public function index()
    {
        $inventarios = InventarioBTR::all();
        $localidades = collect([
            ['nombre' => 'Las Palmas', 'ip' => '192.168.10.6'],
            ['nombre' => 'Plantas Tropicales', 'ip' => '192.168.10.11'],
            ['nombre' => 'Mario Mendoza', 'ip' => '192.168.10.16'],
            ['nombre' => 'Hospital Escuela', 'ip' => '192.168.10.21'],
            ['nombre' => 'Lempira Reina', 'ip' => '192.168.10.26'],
            ['nombre' => 'Estadio - La Guaca', 'ip' => '192.168.10.31'],
            ['nombre' => 'San Martin - Ave. Panamá', 'ip' => '192.168.10.36'],
            ['nombre' => 'T Carias - Ave Panamá', 'ip' => '192.168.10.41'],
            ['nombre' => 'San Martin - Las Flores', 'ip' => '192.168.10.46'],
            ['nombre' => 'Más X Menos', 'ip' => '192.168.10.51'],
            ['nombre' => 'ASHOMPLAFA', 'ip' => '192.168.10.56'],
            ['nombre' => 'DELIKATESSEN', 'ip' => '192.168.10.61'],
            ['nombre' => 'Morazán - McDonald', 'ip' => '192.168.10.66'],
            ['nombre' => 'Morazán - Los Castaños', 'ip' => '192.168.10.71'],
            ['nombre' => 'Morazán - Campero', 'ip' => '192.168.10.76'],
            ['nombre' => 'Edificio Rojo', 'ip' => '192.168.10.81'],
            ['nombre' => 'San Martin - T Carias', 'ip' => '192.168.10.86'],
            ['nombre' => 'Suyapa - San Martin', 'ip' => '192.168.10.91'],
            ['nombre' => 'El Barco', 'ip' => '192.168.10.96'],
            ['nombre' => 'Kennedy 2da Entrada', 'ip' => '192.168.10.101'],
            ['nombre' => 'Kennedy Guaymuras', 'ip' => '192.168.10.106'],
            ['nombre' => 'Kennedy 5ta Entrada', 'ip' => '192.168.10.111'],
            ['nombre' => 'Jacaleapa', 'ip' => '192.168.10.116'],
            ['nombre' => 'INFOP', 'ip' => '192.168.10.121'],
            ['nombre' => 'El Hogar', 'ip' => '192.168.10.126'],
            ['nombre' => 'Gálvez - Las Flores', 'ip' => '192.168.10.131'],
            ['nombre' => 'Gálvez - Ave. Panamá', 'ip' => '192.168.10.136'],
            ['nombre' => 'San Felipe', 'ip' => '192.168.10.141'],
            ['nombre' => 'Ave. La Paz - Diunsa', 'ip' => '192.168.10.146'],
            ['nombre' => 'Nashville', 'ip' => '192.168.10.151'],
            ['nombre' => 'Despensa - Próceres', 'ip' => '192.168.10.156'],
            ['nombre' => 'Marina Mercante', 'ip' => '192.168.10.161'],
            ['nombre' => 'Rotonda BANHCAFE', 'ip' => '192.168.10.166'],
            ['nombre' => 'Rotonda DIUNSA', 'ip' => '192.168.10.171'],
            ['nombre' => 'Guanacaste', 'ip' => '192.168.10.176'],
            ['nombre' => 'Arbolito', 'ip' => '192.168.10.181'],
            ['nombre' => 'Chinda Diaz', 'ip' => '192.168.10.186'],
        ])->sortBy('nombre')->values()->all();



        return view('admin.inventarioBTR.index', compact('inventarios', 'localidades'));
    }
    // public function store(Request $request)
// {
//     return $request;
//     $request->validate([
//         'localidad' => 'required|string|max:255',
//         'ip' => 'required|string',
//         'fases' => 'required|integer|min:1|max:10',
//         'rc' => 'required|integer|min:1|max:10',
//         'fibra' => 'required|in:Si,No',
//         'ping' => 'required|in:Si,No',
//         'camaras' => 'required|integer|min:1|max:10',
//         'fuente' => 'nullable|in:Si,No',
//         'switchPoE' => 'nullable|in:Si,No',
//         'xcom' => 'nullable|in:Si,No',
//         'sg116' => 'nullable|in:Si,No',
//         'observaciones' => 'nullable|string',
//         'imagen' => 'nullable|image|max:5120',
//         // 'lienzo_json' => 'nullable'
//     ]);

    //     $rutaImagen = null;

    //     if ($request->hasFile('imagen')) {
//         $imagen = $request->file('imagen');

    //         $localidad = Str::slug($request->localidad);
//         $ip = str_replace('.', '-', $request->ip);
//         $fecha = now()->format('Ymd_His');
//         $extension = $imagen->getClientOriginalExtension();

    //         $nombreImagen = "btr_{$localidad}_{$ip}_{$fecha}.{$extension}";

    //         $rutaImagen = $imagen->storeAs(
//             'inventario_btr',
//             $nombreImagen,
//             'public'
//         );
//     }


    //     dd ($rutaImagen);
//     InventarioBTR::create([
//         'localidad' => $request->localidad,
//         'ip' => $request->ip,
//         'rc' => $request->rc,
//         'fases' => $request->fases,
//         'fibra' => $request->fibra,
//         'ping' => $request->ping,
//         'camaras' => $request->camaras,
//         'fuente' => $request->fuente,
//         'switchPoE' => $request->switchPoE,
//         'xcom' => $request->xcom,
//         'sg116' => $request->sg116,
//         'observaciones' => $request->observaciones,
//         'imagen' => $rutaImagen,
//         // 'lienzo_json' => $request->lienzo_json,
//     ]);

    //     return redirect()->back()
//         ->with('mensaje', 'Registro guardado exitosamente')
//         ->with('icono', 'success');
// }




    public function store(Request $request)
    {
        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');

            $nombre = 'btr_' . Str::slug($request->localidad) . '_' .
                str_replace('.', '-', $request->ip) . '_' .
                now()->format('Ymd_His') . '.' .
                $imagen->getClientOriginalExtension();

            $rutaImagen = $imagen->storeAs('inventario_btr', $nombre, 'public');
        }

        InventarioBTR::create([
            'localidad' => $request->localidad,
            'ip' => $request->ip,
            'rc' => $request->rc,
            'fases' => $request->fases,
            'fibra' => $request->fibra,
            'ping' => $request->ping,
            'camaras' => $request->camaras,
            'fuente' => $request->fuente,
            'switchPoE' => $request->switchPoE,
            'xcom' => $request->xcom,
            'sg116' => $request->sg116,
            'observaciones' => $request->observaciones,
            'imagen' => $rutaImagen,
        ]);

        return redirect()->back()->with('mensaje', 'Configuración guardada correctamente')
            ->with('icono', 'success');
    }


    public function update(Request $request, $id)
    {
        $registro = InventarioBTR::findOrFail($id);
        $rutaImagen = $registro->imagen;

        if ($request->hasFile('imagen')) {

            if ($registro->imagen && Storage::disk('public')->exists($registro->imagen)) {
                Storage::disk('public')->delete($registro->imagen);
            }

            $imagen = $request->file('imagen');

            $nombre = 'btr_' . Str::slug($request->localidad) . '_' .
                str_replace('.', '-', $request->ip) . '_' .
                now()->format('Ymd_His') . '.' .
                $imagen->getClientOriginalExtension();

            $rutaImagen = $imagen->storeAs('inventario_btr', $nombre, 'public');
        }

        $registro->update([
            'localidad' => $request->localidad,
            'ip' => $request->ip,
            'rc' => $request->rc,
            'fases' => $request->fases,
            'fibra' => $request->fibra,
            'ping' => $request->ping,
            'camaras' => $request->camaras,
            'fuente' => $request->fuente,
            'switchPoE' => $request->switchPoE,
            'xcom' => $request->xcom,
            'sg116' => $request->sg116,
            'observaciones' => $request->observaciones,
            'imagen' => $rutaImagen,
        ]);


        return redirect()->back()->with('mensaje', 'Configuración actualizada correctamente')
            ->with('icono', 'success');
    }


    public function pdf($id)
    {
        $registro = InventarioBTR::findOrFail($id);

        $pdf = Pdf::loadView('admin.inventarioBTR.pdf', compact('registro'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('inventario_' . $registro->id . '.pdf');
    }

    public function pdfTodos()
    {
        $registros = InventarioBTR::all();

        $pdf = Pdf::loadView('admin.inventarioBTR.pdf_todos', compact('registros'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('inventario_completo.pdf');
    }

    public function mapasBTR()
    {
                $localidades = collect([
            // ['nombre' => 'Plantas Tropicales', 'ip' => '192.168.10.11'],
            ['nombre' => 'Plantas Tropicales', 'ip' => '192.168.10.11'],
            ['nombre' => 'Mario Mendoza', 'ip' => '192.168.10.16'],
            ['nombre' => 'Hospital Escuela', 'ip' => '192.168.10.21'],
            ['nombre' => 'Lempira Reina', 'ip' => '192.168.10.26'],
            ['nombre' => 'Estadio - La Guaca', 'ip' => '192.168.10.31'],
            ['nombre' => 'San Martin - Ave. Panamá', 'ip' => '192.168.10.36'],
            ['nombre' => 'T Carias - Ave Panamá', 'ip' => '192.168.10.41'],
            ['nombre' => 'San Martin - Las Flores', 'ip' => '192.168.10.46'],
            ['nombre' => 'Más X Menos', 'ip' => '192.168.10.51'],
            ['nombre' => 'ASHOMPLAFA', 'ip' => '192.168.10.56'],
            ['nombre' => 'DELIKATESSEN', 'ip' => '192.168.10.61'],
            ['nombre' => 'Morazán - McDonald', 'ip' => '192.168.10.66'],
            ['nombre' => 'Morazán - Los Castaños', 'ip' => '192.168.10.71'],
            ['nombre' => 'Morazán - Campero', 'ip' => '192.168.10.76'],
            ['nombre' => 'Edificio Rojo', 'ip' => '192.168.10.81'],
            ['nombre' => 'San Martin - T Carias', 'ip' => '192.168.10.86'],
            ['nombre' => 'Suyapa - San Martin', 'ip' => '192.168.10.91'],
            ['nombre' => 'El Barco', 'ip' => '192.168.10.96'],
            ['nombre' => 'Kennedy 2da Entrada', 'ip' => '192.168.10.101'],
            ['nombre' => 'Kennedy Guaymuras', 'ip' => '192.168.10.106'],
            ['nombre' => 'Kennedy 5ta Entrada', 'ip' => '192.168.10.111'],
            ['nombre' => 'Jacaleapa', 'ip' => '192.168.10.116'],
            ['nombre' => 'INFOP', 'ip' => '192.168.10.121'],
            ['nombre' => 'El Hogar', 'ip' => '192.168.10.126'],
            ['nombre' => 'Gálvez - Las Flores', 'ip' => '192.168.10.131'],
            ['nombre' => 'Gálvez - Ave. Panamá', 'ip' => '192.168.10.136'],
            ['nombre' => 'San Felipe', 'ip' => '192.168.10.141'],
            ['nombre' => 'Ave. La Paz - Diunsa', 'ip' => '192.168.10.146'],
            ['nombre' => 'Nashville', 'ip' => '192.168.10.151'],
            ['nombre' => 'Despensa - Próceres', 'ip' => '192.168.10.156'],
            ['nombre' => 'Marina Mercante', 'ip' => '192.168.10.161'],
            ['nombre' => 'Rotonda BANHCAFE', 'ip' => '192.168.10.166'],
            ['nombre' => 'Rotonda DIUNSA', 'ip' => '192.168.10.171'],
            ['nombre' => 'Guanacaste', 'ip' => '192.168.10.176'],
            ['nombre' => 'Arbolito', 'ip' => '192.168.10.181'],
            ['nombre' => 'Chinda Diaz', 'ip' => '192.168.10.186'],
        ])->sortBy('nombre')->values()->all();


        return view('admin.inventarioBTR.mapas_btr', compact('localidades'));
    }

    //       public function dashboard()
// {
//     $inventarios = InventarioBTR::all();

    //     $totalCamaras = $inventarios->sum('camaras');
//     $totalSwitch  = $inventarios->sum('switchPoE');
//     $totalXcom    = $inventarios->sum('xcom');
//     $totalFuente    = $inventarios->sum('fuente');
//     $totalSg116    = $inventarios->sum('sg116');

    //     return view('admin.inventarioBTR.dashboard', compact(
//         'inventarios',
//         'totalCamaras',
//         'totalSwitch',
//         'totalXcom','totalFuente','totalSg116'
//     ));
// }

    public function dashboard()
    {
        $inventarios = InventarioBTR::all();

        $totales = [
            'camaras' => $inventarios->sum('camaras'),
            'switchPoE' => $inventarios->sum('switchPoE'),
            'xcom' => $inventarios->sum('xcom'),
            'sg116' => $inventarios->sum('sg116'),
            'fuente' => $inventarios->sum('fuente'),
        ];

        return view('admin.inventarioBTR.dashboard', compact('inventarios', 'totales'));
    }


    // public function guardar(Request $request)
    // {
    //     $imagen = $request->imagen;
    //     $nombre = Str::slug($request->nombre); // limpia el nombre

    //     // Separar encabezado base64
    //     $imagen = str_replace('data:image/png;base64,', '', $imagen);
    //     $imagen = str_replace('data:image/jpeg;base64,', '', $imagen);
    //     $imagen = str_replace(' ', '+', $imagen);

    //     $imagenDecodificada = base64_decode($imagen);

    //     $nombreArchivo = $nombre . '.png';

    //     Storage::disk('public')->put('mapas/' . $nombreArchivo, $imagenDecodificada);

    //     return response()->json([
    //         'ruta' => 'mapas/' . $nombreArchivo
    //     ]);
    // }




public function guardar(Request $request)
{
    $request->validate([
        'imagen' => 'required',
        'localidad' => 'required',
        'ip' => 'required'
    ]);

    // Preparar contenido base64
    $image = $request->imagen;
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    // Nombre del archivo como pediste
    $extension = 'png'; // ya que html2canvas genera PNG
    $nombre = 'btr_' . Str::slug($request->localidad) . '_' .
                str_replace('.', '-', $request->ip) . '_' .
                now()->format('Ymd_His') . '.' . $extension;

    // Guardar directamente en storage/app/public/inventario_btr
    $rutaImagen = Storage::disk('public')->put('inventario_btr/'.$nombre, $imageData);

    return response()->json([
        'success' => true,
        'ruta' => 'inventario_btr/'.$nombre
    ]);
}





}


