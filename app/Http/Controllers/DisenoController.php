<?php

namespace App\Http\Controllers;

use App\Models\DisenoCredencial;
use Illuminate\Http\Request;

class DisenoController extends Controller
{

public function index()
{

$disenos=DisenoCredencial::all();

return view('disenos.index',compact('disenos'));

}

public function editor($id)
{

$diseno=DisenoCredencial::findOrFail($id);

return view('disenos.editor',compact('diseno'));

}

public function guardarConfiguracion(Request $request,$id)
{

$diseno=DisenoCredencial::findOrFail($id);

$diseno->configuracion=$request->config;

$diseno->save();

return response()->json(['ok'=>true]);

}

}