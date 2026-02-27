<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CardDesign;

class CardDesignController extends Controller
{
    public function index(){ $designs = CardDesign::all(); 
  

   return view('admin.idcards.index', compact('designs')); }
    
public function create()
{
    $design = (object)[
        'id' => null,
        'name' => '',
        'design_json' => [],
        'background' => null,
    ];

    return view('admin.idcards.editor', compact('design'));
}

    public function store(Request $request){

    
        CardDesign::create([
            'name'=>$request->name,
            'layout'=>$request->layout
        ]);
        return redirect()->route('idcards.index')->with('success','Diseño guardado');
    }

public function edit($id)
{
    $design = CardDesign::findOrFail($id);

    // seguridad extra
    if (!$design->design_json) {
        $design->design_json = [];
    }

    return view('admin.idcards.editor', compact('design'));
}
    public function update(Request $request, CardDesign $id){
        $id->update(['name'=>$request->name,'layout'=>$request->layout]);
        return redirect()->route('idcards.index')->with('success','Diseño actualizado');
    }
    public function saveDesign(Request $request)
{
    $data = $request->validate([
        'name' => 'required',
        'design_json' => 'nullable', // puede estar vacío
        'background' => 'nullable|image'
    ]);

    // Si no viene design_json, guardar array vacío
    $data['design_json'] = $data['design_json'] ?? [];

    $design = CardDesign::updateOrCreate(
        ['id' => $request->id],
        $data
    );

    return response()->json($design);
}

}
