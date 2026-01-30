<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConexionRed;
use App\Models\Equipo;
use App\Models\EquipoRed;
use Illuminate\Http\Request;

class DiagramaRedController extends Controller
{
    public function index(){
        return view('admin.diagramas.red',[
            'equipos' => Equipo::all()
        ]);
        // return  Equipo::all();
    }

    public function guardarPosicion(Request $request){
        Equipo::where('id',$request->id)->update([
            'x'=>$request->x,
            'y'=>$request->y
        ]);
        return response()->json(['ok'=>true]);
    }

    public function storeEquipo(Request $request){
        return EquipoRed::create($request->all());
    }

    public function storeConexion(Request $request){
        return ConexionRed::create($request->all());
    }
}

