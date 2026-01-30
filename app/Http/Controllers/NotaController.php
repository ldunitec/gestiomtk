<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function index()
    {
        $notas = Nota::latest()->get();
        return view('admin.notas.index', compact('notas'));
    }

    public function store(Request $request)
    {
        return $request;
        Nota::create($request->all());
        return back()->with('success','Nota creada');
    }

    public function edit(Nota $nota)
    {
        return response()->json($nota);
    }

    public function update(Request $request, Nota $nota)
    {
        // guardar historial
        $nota->historial()->create([
            'contenido' => $nota->contenido,
            'editado_en' => now()
        ]);

        $nota->update($request->all());
        return response()->json(['ok'=>true]);
    }

    public function show(Nota $nota)
    {
        // return view('admin.notas.print', compact('nota'));
    }
}

