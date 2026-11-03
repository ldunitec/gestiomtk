<?php

namespace Modules\Mikrotik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Modules\Mikrotik\Entities\Router;
use Modules\Mikrotik\Services\FichasService;

class ConciliarFichas extends Command
{
    protected $signature = 'mikrotik:conciliar';
    protected $description = 'Confirma bloqueos y revocaciones pendientes de fichas de visitantes';

    public function handle(FichasService $service): int
    {
        if (!Schema::hasTable('mt_routers')) { return self::SUCCESS; }
        $fallos = 0;
        foreach (Router::lazyById(50) as $r) {
            try { $fallos += $service->conciliar($r->id); }
            catch (\Throwable $e) {
                $fallos++;
                $this->warn('No se pudo conciliar el router #'.$r->id.'. Revise conectividad y configuración.');
            }
        }
        $this->info('Fichas pendientes de confirmación: '.$fallos);
        return $fallos ? self::FAILURE : self::SUCCESS;
    }
}
