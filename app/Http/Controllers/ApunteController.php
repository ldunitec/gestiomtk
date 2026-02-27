<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Apunte;
use App\Models\HistorialApunte;
use App\Models\VisualizacionApunte;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApunteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apuntes = Apunte::with('creador')->latest()->get();

        // return $apuntes;

        return view('admin.apuntes.index', compact('apuntes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('admin.apuntes.create');
    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:100',
            'url' => 'nullable|string|max:255',
            'usuario' => 'nullable|string|max:255',
            'password' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Apunte::create([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'ip' => $request->ip,
            'url' => $request->url,
            'usuario' => $request->usuario,

            // 🔐 ENCRIPTAMOS AQUÍ
            'password' => Crypt::encryptString($request->password),

            'descripcion' => $request->descripcion,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.apuntes.index')
            ->with('success', 'Apunte creado correctamente');
    }


    /**
     * Display the specified resource.
     */
    public function show(Apunte $apunte)
    {
        // Cargar relaciones necesarias, por ejemplo, creador y visualizaciones
        $apunte->load('creador', 'visualizaciones');

        return view('admin.apuntes.show', compact('apunte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apunte $apunte)
    {
        // Cargar relaciones necesarias
        $apunte->load('creador');

        // Retornar la vista de edición
        return view('admin.apuntes.edit', compact('apunte'));
    }


    /**
     * Update the specified resource in storage.
     */




    public function update(Request $request, Apunte $apunte)
    {
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'usuario' => 'required|string|max:100',
            'ip' => 'nullable|string|max:100',
            'url' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'motivo' => 'nullable|string|max:255',
        ]);
        

        $apunte->titulo = $request->titulo;
        $apunte->tipo = $request->tipo;
        $apunte->usuario = $request->usuario;
        $apunte->ip = $request->ip;
        $apunte->url = $request->url;
        $apunte->descripcion = $request->descripcion;

        // 🔐 Si hay nueva contraseña
        if ($request->filled('password')) {

            // Guardar contraseña anterior (ENCRIPTADA como está en BD)
            $passwordAnterior = $apunte->password;


            // Encriptar nueva contraseña
            $passwordNueva = Crypt::encryptString($request->password);

            // Actualizar en apunte
            $apunte->password = $passwordNueva;

            // Registrar historial
            HistorialApunte::create([
                'apunte_id' => $apunte->id,
                'password_anterior' => $passwordAnterior,
                'cambiado_por' => Auth::id(),
                'motivo' => $request->motivo,
            ]);
        }

        $apunte->save();

        return redirect()
            ->route('admin.apuntes.index')
            ->with('success', 'Apunte actualizado correctamente');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apunte = Apunte::findOrFail($id);
        $apunte->delete();

        return redirect()->route('admin.apuntes.index')
            ->with('mensaje', 'Apunte eliminada correctamente')
            ->with('icono', 'success');
    }







    public function verPassword(Request $request, Apunte $apunte)
    {
        $request->validate([
            'password_confirm' => 'required|string',
        ]);

        // Validar contraseña del usuario logueado
        if (
            !Auth::validate([
                'email' => auth()->user()->email,
                'password' => $request->password_confirm
            ])
        ) {
            return response()->json([
                'error' => 'Contraseña incorrecta'
            ], 403);
        }

        // Registrar visualización
        VisualizacionApunte::create([
            'apunte_id' => $apunte->id,
            'user_id' => auth()->id(),
            'ip_usuario' => $request->ip()
        ]);

        // Devolver contraseña desencriptada
        return response()->json([
            'password' => Crypt::decryptString($apunte->password)
        ]);
    }



    public function auditoria()
    {
        $logs = VisualizacionApunte::with('apunte', 'user')
            ->latest()
            ->get();

        return view('apuntes.auditoria', compact('logs'));
    }


    public function historial(Apunte $apunte)
    {
        // Cargamos el historial con usuario que hizo el cambio
        $historial = $apunte->historial()->with('cambiadoPor')->latest()->get();

        return view('admin.apuntes.historial', compact('apunte', 'historial'));
    }


}
