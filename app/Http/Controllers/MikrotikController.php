<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikController extends Controller
{
    private function client()
    {
        return new Client([
            'host' => env('MIKROTIK_HOST'),
            'user' => env('MIKROTIK_USER'),
            'pass' => env('MIKROTIK_PASS'),
            'port' => (int) env('MIKROTIK_PORT', 8728),
        ]);
    }

    // 📋 LISTAR CLIENTES
    public function index()
    {
        $client = $this->client();

        $query = new Query('/ip/dhcp-server/lease/print');
        $leases = $client->query($query)->read();

        // Filtrar solo los del DHCP dhcp-MTK
        $leases = array_filter($leases, function ($l) {
            return isset($l['server']) && $l['server'] === 'dhcp-MTK';
        });

        return view('admin.mikrotik.clientes', compact('leases'));
    }


    // 🌐 AUTORIZAR
    public function autorizar(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'mac' => 'required',
            'comment' => 'nullable'
        ]);

        $client = $this->client();

        $query = new Query('/ip/dhcp-server/lease/add');
        $query->equal('address', $request->ip)
            ->equal('mac-address', $request->mac)
            ->equal('comment', $request->comment)
            ->equal('disabled', 'no');
        // ->equal('server', 'dhcp-MTK')
        // ->equal('add-arp', 'yes');

        $client->query($query)->read();

        return back()->with('success', 'Cliente autorizado correctamente');
    }

    // 🚫 CORTAR INTERNET
  public function deshabilitar(Request $request)
{
    $client = $this->client();

    // Deshabilitar DHCP Lease
    $queryDhcp = new Query('/ip/dhcp-server/lease/set');
    $queryDhcp->equal('.id', $request->id)
              ->equal('disabled', 'yes');
    $client->query($queryDhcp)->read();

    // Deshabilitar ARP
    $queryArp = new Query('/ip/arp/set');
    $queryArp->equal('.id', $request->id)
             ->equal('disabled', 'yes');
    $client->query($queryArp)->read();



        return back();
    }

    // ✅ RESTAURAR INTERNET
    public function habilitar(Request $request)
    {
        $client = $this->client();

        $query = new Query('/ip/dhcp-server/lease/set');
        $query->equal('.id', $request->id)
            ->equal('disabled', 'no');

        $client->query($query)->read();

        return back();
    }

    public function toggleLease(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'disabled' => 'required|boolean'
        ]);

        try {
            $client = new Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USER'),
                'pass' => env('MIKROTIK_PASS'),
                'port' => (int) env('MIKROTIK_PORT', 8728),
            ]);

            $query = new Query('/ip/dhcp-server/lease/set');
            $query->equal('.id', $request->id)
                ->equal('disabled', $request->disabled ? 'yes' : 'no');

            $client->query($query)->read();

            return response()->json([
                'message' => $request->disabled
                    ? '🚫 Internet cortado'
                    : '✅ Internet habilitado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Error: ' . $e->getMessage()
            ], 500);
        }
    }

// host online 
//     public function conectados()
// {
//     $client = $this->client();

//     // Equipos conectados al bridge
//     $query = new Query('/interface/ether2-LAN/host/print');
//     $hosts = $client->query($query)->read();

//     return view('admin.mikrotik.conectados', compact('hosts'));
// }
public function conectados()
{
    $client = $this->client();
$query = new Query('/ip/dhcp-server/lease/print');
$hosts = $client->query($query)->read();

// return $hosts;
    // Rango permitido
    $ipInicio = ip2long('192.168.1.2');
    $ipFin    = ip2long('192.168.1.254');

    // Filtrar solo IPs dentro del rango
    // $hosts = array_filter($leases, function ($h) use ($ipInicio, $ipFin) {

    //     if (!isset($h['address'])) {
    //         return false; // sin IP
    //     }

    //     $ip = ip2long($h['address']);

    //     return $ip >= $ipInicio && $ip <= $ipFin;
    // });

    return view('admin.mikrotik.conectados', compact('hosts'));
}


}
