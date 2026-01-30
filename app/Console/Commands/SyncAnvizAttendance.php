<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnvizService;
use App\Models\Asistencia;

class SyncAnvizAttendance extends Command
{
    protected $signature = 'anviz:attendance';
    protected $description = 'Descargar asistencia desde Anviz VF30';

    public function handle()
    {
        $service = new \App\Servicios\AnvizService();

        $page = 1;
        $pageSize = 100;

        do {
            $data = $service->getAttendance($page, $pageSize);

            foreach ($data['rows'] ?? [] as $row) {
                Asistencia::firstOrCreate(
                    [
                        'empleado_id' => $row['userId'],
                        'fecha_hora'  => $row['time'],
                    ],
                    [
                        'tipo'        => $this->mapType($row['type'] ?? 0),
                        'dispositivo' => config('services.anviz.device', 'VF30'),
                    ]
                );
            }

            $total = $data['total'] ?? 0;
            $page++;

        } while (($page - 1) * $pageSize < $total);

        $this->info('✔ Asistencia sincronizada correctamente');
    }

    private function mapType($type): string
    {
        return match ($type) {
            1 => 'salida',
            2 => 'almuerzo',
            default => 'entrada',
        };
    }
}
