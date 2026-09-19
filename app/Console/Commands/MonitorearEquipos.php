<?php

namespace App\Console\Commands;

use FreeDSx\Snmp\SnmpClient;
use Illuminate\Console\Command;
use App\Models\Equipo;
use App\Models\Monitoreo;
use App\Models\Alerta;

class MonitorearEquipos extends Command
{
    // Nombre del comando
    protected $signature = 'monitorear:equipos';

    protected $description = 'Monitorea los equipos';

    public function handle()
    {
        // Rango de IPs
        $ipInicio = '192.168.10.1';
        $ipFin = '192.168.10.254';

        // Obtener equipos dentro del rango
        $equipos = Equipo::whereRaw('INET_ATON(ip_address) BETWEEN ? AND ?', [
            ip2long($ipInicio),
            ip2long($ipFin)
        ])->get();

        // Detectar sistema operativo
        $sistema = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'windows' : 'linux';

        foreach ($equipos as $eq) {

            // Comando ping según SO
            $comando = $sistema === 'windows'
                ? "ping -n 1 {$eq->ip_address}"
                : "ping -c 1 {$eq->ip_address}";

            // Ejecutar ping
            $ping = shell_exec($comando) ?? '';
            // Detectar respuesta (TTL)
            $estado = (strpos($ping, 'TTL=') !== false || strpos($ping, 'ttl=') !== false) ? 1 : 0;


            // snmp 
            $cpu = null;
            $ram = null;

 // 📡 SNMP SOLO SI ESTÁ ONLINE
        if ($estado == 1) {

            try {

                $snmp = new SnmpClient([
                    'host' => $eq->ip,
                    'community' => 'public', // cambia si usas otro
                    'version' => 2
                ]);

                // CPU
                $cpu = (int) $snmp->getValue('1.3.6.1.4.1.14988.1.1.3.10.0');

                // RAM
                $ramTotal = (int) $snmp->getValue('1.3.6.1.4.1.14988.1.1.3.8.0');
                $ramUsed  = (int) $snmp->getValue('1.3.6.1.4.1.14988.1.1.3.9.0');

                if ($ramTotal > 0) {
                    $ram = round(($ramUsed / $ramTotal) * 100);
                }

            } catch (\Exception $e) {
                // si falla SNMP, no rompe el sistema
                $cpu = null;
                $ram = null;
            }
        }

            // Guardar monitoreo
            Monitoreo::create([
                'equipo_id' => $eq->id,
                'estado' => $estado,
                'cpu' => $cpu,
                'ram' => $ram
            ]);

            // 🔴 ALERTA OFFLINE (solo una vez)
            if ($estado == 0 && $eq->alertado == 0) {

                // Verifica si ya existe alerta reciente
                $existe = Alerta::where('equipo_id', $eq->id)
                    ->where('tipo', 'OFFLINE')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->exists();

                if (!$existe) {
                    $this->enviarAlertaOffline($eq);
                }

                $eq->update(['alertado' => 1]);
            }
        }

        $this->info('Monitoreo completado: ' . count($equipos) . ' equipos verificados.');
    }

    /**
     * 🔴 Alerta cuando el equipo cae
     */
    private function enviarAlertaOffline($eq)
    {
        $mensaje = "🚨 ALERTA: Equipo caído\n" .
            "Nombre: {$eq->nombre}\n" .
            "IP: {$eq->ip_address}\n" .
            "Fecha: " . now();

        Alerta::create([
            'equipo_id' => $eq->id,
            'tipo' => 'OFFLINE',
            'mensaje' => $mensaje,
            'visto' => 0
        ]);
    }
}