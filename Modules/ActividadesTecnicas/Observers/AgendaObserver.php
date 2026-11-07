<?php

namespace Modules\ActividadesTecnicas\Observers;

use Illuminate\Support\Facades\Schema;
use Modules\Produccion\Entities\Agenda;
use Modules\ActividadesTecnicas\Services\AgendaIntegration;

class AgendaObserver
{
    public function updated(Agenda $agenda): void
    {
        if (!$agenda->wasChanged(['titulo','fecha_inicio','estado'])) { return; }
        if (!Schema::hasColumn('at_work_orders', 'agenda_id')) { return; }
        app(AgendaIntegration::class)->fromAgenda($agenda);
    }
}
