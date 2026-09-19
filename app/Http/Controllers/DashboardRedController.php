<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Monitoreo;

class DashboardRedController extends Controller
{
    public function index()
    {



        return view('admin.monitoreo.dashboard-red');
    }

    public function data()
    {

        $ipInicio = '192.168.10.1';
        $ipFin = '192.168.10.254';

        // Obtener equipos dentro del rango
        $equipos = Equipo::whereRaw('INET_ATON(ip_address) BETWEEN ? AND ?', [
            ip2long($ipInicio),
            ip2long($ipFin)
        ])->get();

        // $equipos = Equipo::orderBy('estado','desc')->get();

        $data = [];

        foreach ($equipos as $eq) {

            $ultimo = Monitoreo::where('equipo_id', $eq->id)
                ->latest()
                ->first();
               

            $estado = $ultimo ? $ultimo->estado : 0;
            $fecha = $ultimo ? $ultimo->created_at : null;
            $fechaformat = optional($fecha)->format('d/m/Y H:i:s');

            $data[] = [
                'nombre' => $eq->nombre,
                'ip' => $eq->ip_address,
                'estado' => $estado,
                'fecha' => $fechaformat
            ];
        }

        return response()->json($data);
    }
}
