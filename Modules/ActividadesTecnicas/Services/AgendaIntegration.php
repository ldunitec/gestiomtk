<?php

namespace Modules\ActividadesTecnicas\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Illuminate\Validation\ValidationException;
use Modules\Produccion\Entities\Agenda;
use Modules\ActividadesTecnicas\Models\{WorkOrder, TechnicalAuditLog};

class AgendaIntegration
{
    public function toAgenda(WorkOrder $order, array $options = []): Agenda
    {
        $agenda = $order->agenda_id ? Agenda::whereKey($order->agenda_id)->firstOrFail() : new Agenda;
        $agenda->titulo = $order->title;
        $agenda->fecha_inicio = $order->scheduled_at?->copy()->timezone(config('app.timezone'))->toDateString();
        $agenda->estado = in_array($order->status, ['completed','approved','cancelled'], true) ? 2 : 1;
        if (!$agenda->exists) {
            $agenda->usuario_id = $order->created_by;
            $agenda->tipo = 'Visita';
            $agenda->servicio = mb_substr((string) $order->description, 0, 255);
        }
        if (array_key_exists('agenda_tipo', $options)) { $agenda->tipo = $options['agenda_tipo']; }
        if (array_key_exists('agenda_responsable', $options)) {
            $agenda->assigned_user_id = $options['agenda_responsable'] === 'todos' ? null : $options['agenda_responsable'];
        }
        $agenda->color = ['Interno'=>'#3788d8','Taller'=>'#28a745','Visita'=>'#ffc107','Llamada'=>'#6c757d'][$agenda->tipo] ?? '#6c757d';
        $agenda->saveQuietly();
        if (!$order->agenda_id) { $order->forceFill(['agenda_id'=>$agenda->id])->save(); }
        $order->setRelation('agenda', $agenda);
        return $agenda;
    }

    public function fromAgenda(Agenda $agenda): void
    {
        $order = WorkOrder::where('agenda_id', $agenda->id)->lockForUpdate()->first();
        if (!$order) { return; }
        if (Auth::check()) { Gate::authorize('actividades-tecnicas.gestionar'); }
        if ($order->status === 'approved' && (int)$agenda->estado !== 2) { throw ValidationException::withMessages(['estado'=>'Una orden aprobada no se puede reabrir desde la agenda.']); }
        if ($order->status === 'cancelled') { throw ValidationException::withMessages(['estado'=>'La orden técnica está cancelada y no se puede reabrir desde la agenda.']); }
        if (mb_strlen($agenda->titulo) > 180) { throw ValidationException::withMessages(['title'=>'Las órdenes técnicas admiten títulos de hasta 180 caracteres.']); }
        $old = $order->only(['title','scheduled_at','status']);
        $order->title = $agenda->titulo;
        if ($agenda->wasChanged('fecha_inicio')) {
            $order->scheduled_at = $this->schedule($agenda, $order);
        }
        $previousStatus = $order->status;
        if ((int) $agenda->estado === 2 && !in_array($order->status, ['completed','approved'], true)) {
            $order->status = 'completed'; $order->closed_at = CarbonImmutable::now('UTC');
        } elseif ((int) $agenda->estado === 1 && $order->status === 'completed') {
            $order->status = 'new'; $order->closed_at = null;
        }
        if (!$order->isDirty()) { return; }
        $order->save();
        $actor = Auth::id() ?? $agenda->usuario_id;
        if ($previousStatus !== $order->status) {
            $order->statusHistory()->create(['from_status'=>$previousStatus,'to_status'=>$order->status,
                'changed_by'=>$actor,'changed_at'=>CarbonImmutable::now('UTC'),'notes'=>'Estado actualizado desde Agenda de Producción']);
        }
        TechnicalAuditLog::create(['user_id'=>$actor,'action'=>'sincronizar_agenda','subject_type'=>WorkOrder::class,
            'subject_id'=>$order->id,'old_values'=>$old,'new_values'=>$order->only(array_keys($old)),'created_at'=>CarbonImmutable::now('UTC')]);
    }

    private function schedule(Agenda $agenda, ?WorkOrder $order = null): ?CarbonImmutable
    {
        if (!$agenda->fecha_inicio) { return null; }
        $time = $order?->scheduled_at?->copy()->timezone(config('app.timezone'))->format('H:i:s') ?? '00:00:00';
        $date = CarbonImmutable::parse($agenda->fecha_inicio, config('app.timezone'))->toDateString();
        return CarbonImmutable::parse($date.' '.$time, config('app.timezone'))->utc();
    }

    public function convert(Agenda $agenda, int $actor): WorkOrder
    {
        return DB::transaction(function () use ($agenda, $actor) {
            $agenda = Agenda::whereKey($agenda->id)->lockForUpdate()->firstOrFail();
            $order = WorkOrder::where('agenda_id', $agenda->id)->first();
            if ($order) { return $order; }
            if (mb_strlen($agenda->titulo) > 180) { throw ValidationException::withMessages(['title'=>'Reduzca el título de la actividad a 180 caracteres para convertirla en orden técnica.']); }
            $order = app(WorkOrderService::class)->create(['title'=>$agenda->titulo,'description'=>$agenda->servicio,
                'priority'=>'normal','scheduled_at'=>$this->schedule($agenda)], $actor, $agenda);
            return $order;
        }, 3);
    }
}
