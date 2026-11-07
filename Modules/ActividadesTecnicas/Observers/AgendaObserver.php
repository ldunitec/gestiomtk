<?php

namespace Modules\ActividadesTecnicas\Observers;

use Illuminate\Support\Facades\Schema;
use Modules\Produccion\Entities\Agenda;
use Modules\ActividadesTecnicas\Services\AgendaIntegration;

class AgendaObserver
{
    public function created(Agenda $agenda): void
    {
        if (!Schema::hasColumn('at_work_orders', 'agenda_id')) { return; }
        // Agenda::save() ya engloba la actividad, la orden y su historial en una transacción.
        // Las órdenes que crean agenda usan saveQuietly(), evitando el ciclo inverso.
        app(AgendaIntegration::class)->convert($agenda, (int) $agenda->usuario_id);
    }

    public function updated(Agenda $agenda): void
    {
        if (!$agenda->wasChanged(['titulo','fecha_inicio','estado'])) { return; }
        if (!Schema::hasColumn('at_work_orders', 'agenda_id')) { return; }
        app(AgendaIntegration::class)->fromAgenda($agenda);
    }
}

