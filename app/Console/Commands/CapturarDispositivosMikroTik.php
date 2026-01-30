<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Dispositivo;

class CapturarDispositivosMikroTik extends Command
{
    protected $signature = 'mikrotik:capturar-dispositivos';
    protected $description = 'Captura los dispositivos conectados desde MikroTik';

    public function handle()
    {
        $client = new Client([
            'host' => '192.168.10.1', // IP de tu router MikroTik
            'user' => 'shirei',
            'pass' => 'tankM3g@mega',
            'port' => 8728, // puerto API por defecto
        ]);

        $query = new Query('/ip/dhcp-server/lease/print');
        $dispositivos = $client->query($query)->read();

        foreach ($dispositivos as $item) {
            Dispositivo::updateOrCreate(
                ['mac' => $item['mac-address']],
                [
                    'ip' => $item['address'],
                    'nombre' => $item['host-name'] ?? 'Desconocido',
                    'estado' => $item['status'] ?? 'unknown',
                    'ultima_conexion' => now()
                ]
            );
        }

        $this->info('Dispositivos actualizados correctamente.');
    }
}

