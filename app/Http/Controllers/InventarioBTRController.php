<?php

namespace App\Http\Controllers;

use App\Models\InventarioBTR;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventarioBTRController extends Controller
{



    public function index()
    {
        $inventarios = InventarioBTR::all();
        $localidades = [
            ['nombre' => 'Plantas Tropicales', 'ip' => '192.168.10.11'],
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

        ];

        return view('admin.inventarioBTR.index', compact('inventarios', 'localidades'));
    }

    public function store(Request $request)
    {
// return $request;
        $request->validate([
            'localidad' => 'required|string|max:255',
            'ip' => 'required|string',
            'fases' => 'required|integer|min:1|max:10',
            'rc' => 'required|integer|min:1|max:10',
            'fibra' => 'required|in:Si,No',
            'ping' => 'required|in:Si,No',
            'camaras' => 'required|integer|min:1|max:10',
            'fuente' => 'nullable|in:Si,No',
            'switchPoE' => 'nullable|in:Si,No',
            'xcom' => 'nullable|in:Si,No',
            'sg116' => 'nullable|in:Si,No',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|image|max:5120', // 5MB max
        ]);

     $rutaImagen = null;

if ($request->hasFile('imagen')) {
    $imagen = $request->file('imagen');

    $localidad = Str::slug($request->localidad); // san-pedro
    $ip = str_replace('.', '-', $request->ip);   // 192-168-1-10
    $fecha = now()->format('Ymd_His');            // 20260126_153022
    $extension = $imagen->getClientOriginalExtension();

    $nombreImagen = "btr_{$localidad}_{$ip}_{$fecha}.{$extension}";

    $rutaImagen = $imagen->storeAs(
        'inventario_btr',
        $nombreImagen,
        'public'
    );
}
// return $rutaImagen;
        InventarioBTR::create([
            'localidad' => $request->localidad,
            'ip' => $request->ip,
            'rc-308' => $request->rc,
            'fases' => $request->fases,
            'fibra' => $request->fibra,
            'ping' => $request->ping,
            'fuente' => $request->fuente,
            'switchPoE' => $request->switchPoE,
            'xcom' => $request->xcom,
            'sg116' => $request->sg116,
            'observaciones' => $request->observaciones,
            'imagen' => $rutaImagen,
        ]);

        return redirect()->back()->with('success', 'Inventario guardado correctamente');
    }
}


