<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        return view('admin.alertas.index');
    }

    public function data()
    {
        // Traer últimas alertas con equipo
        $alertas = Alerta::with('equipo')->latest()->get();

        // return datatables()->of($alertas)->toJson();

        return datatables()->of($alertas)
    ->editColumn('created_at', function ($a) {
        return $a->created_at 
            ? $a->created_at->format('d/m/Y H:i:s') 
            : '';
    })
    ->make(true);
    }
}