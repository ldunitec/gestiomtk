<?php

namespace App\Http\Controllers;

use App\Models\CardDesign;
use App\Models\Persona;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IdCardController extends Controller
{
  public function index()
    {
        $personas = Persona::all();
        $designs  = CardDesign::all();

        return view('admin.idcards.index', compact('personas','designs'));
    }


    public function create()
    {
        return view('admin.idcards.create');
    }
    public function store(Request $request)
    {


        $request->validate([
            'nombre' => 'required',
            'identificacion' => 'required|unique:personas',
            'foto' => 'image|mimes:jpg,png|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('fotos', 'public');
        }
        // return $path;
        Persona::create([
            'nombre' => $request->nombre,
            'identificacion' => $request->identificacion,
            'cargo' => $request->cargo,
            'foto' => $path
        ]);

        return redirect()->route('admin.idcards.index')
            ->with('success', 'Persona registrada');
    }

     public function preview($personaId, $designId)
    {
        $persona = Persona::findOrFail($personaId);
        $design  = CardDesign::findOrFail($designId);

        return view('admin.idcards.preview', compact('persona','design'));
    }




   public function print($personaId, $designId)
{
    $persona = Persona::findOrFail($personaId);
    $design  = CardDesign::findOrFail($designId);

    // DEBUG TEMPORAL
    // dd($persona, $design);

    $pdf = Pdf::loadView('admin.idcards.print', compact('persona','design'))
        ->setPaper([0, 0, 1013, 638], 'portrait');


    return $pdf->stream('credencial.pdf');
}




public function saveDesign(Request $request)
{
    $frontBg = null;

    if ($request->hasFile('front_bg')) {
        $frontBg = $request->file('front_bg')
            ->store('card_backgrounds', 'public');
    }

    CardDesign::create([
        'nombre'        => $request->nombre,
        'front_layout'  => json_decode($request->front, true),
        'back_layout'   => json_decode($request->back, true),
        'front_bg'      => $frontBg,
    ]);

    return response()->json(['ok' => true]);
}



}

