<?php

namespace Modules\ActividadesTecnicas\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\ActividadesTecnicas\Models\WorkOrder;
use Modules\ActividadesTecnicas\Services\AgendaIntegration;

class IntegrarAgenda extends Command
{
    protected $signature = 'actividades-tecnicas:integrar-agenda';
    protected $description = 'Vincular a la agenda las órdenes técnicas anteriores sin duplicar las ya vinculadas';

    public function handle(AgendaIntegration $integration): int
    {
        $n = 0;
        WorkOrder::whereNull('agenda_id')->chunkById(100, function ($orders) use ($integration, &$n) {
            foreach ($orders as $order) {
                DB::transaction(function () use ($order, $integration, &$n) {
                    $order = WorkOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
                    if ($order->agenda_id) { return; }
                    $integration->toAgenda($order); $n++;
                }, 3);
            }
        });
        $this->info($n.' órdenes vinculadas. No se envían tareas a Todoist desde este comando.');
        return self::SUCCESS;
    }
}
