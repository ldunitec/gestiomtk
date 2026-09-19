<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Equipo;
use App\Models\Monitoreo;

class MonitoreoController extends Controller
{
    public function index()
    {
        return view('admin.monitoreo.index');
    }

    public function data()
{
  // public function data()
    // {
    //       // Rango de IPs
    $ipInicio = '192.168.10.1';
    $ipFin    = '192.168.10.254';

    // Obtener equipos dentro del rango
    $equipos = Equipo::whereRaw('INET_ATON(ip_address) BETWEEN ? AND ?', [
        ip2long($ipInicio),
        ip2long($ipFin)
    ])->get();

$data = [];

    foreach ($equipos as $eq) {

        $ultimo = Monitoreo::where('equipo_id',$eq->id)
                    ->latest()
                    ->first();

        $estado = $ultimo ? $ultimo->estado : 0;

        $data[] = [
            'nombre'=>$eq->nombre,
            'ip'=>$eq->ip_address,
            'estado'=>$estado
        ];
    }

    return response()->json(['data'=>$data]);
}

    // public function data()
    // {
    //       // Rango de IPs
    // $ipInicio = '192.168.10.1';
    // $ipFin    = '192.168.10.254';

    // // Obtener equipos dentro del rango
    // $equipos = Equipo::whereRaw('INET_ATON(ip_address) BETWEEN ? AND ?', [
    //     ip2long($ipInicio),
    //     ip2long($ipFin)
    // ])->get();
    
    //     $data = [];

    //     foreach ($equipos as $eq) {

    //         // PING
    //         $ping = exec("ping -n 1 ".$eq->ip_address);
    //         $estado = strpos($ping, "TTL=") !== false ? 1 : 0;

    //         // GUARDAR HISTORIAL
    //         Monitoreo::create([
    //             'equipo_id'=>$eq->id,
    //             'estado'=>$estado
    //         ]);

    //         $data[] = [
    //             'nombre' => $eq->nombre,
    //             'ip' => $eq->ip,
    //             'estado' => $estado
    //         ];
    //     }

    //     return response()->json(['data'=>$data]);
    // }
}