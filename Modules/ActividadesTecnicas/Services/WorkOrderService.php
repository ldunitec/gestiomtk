<?php

namespace Modules\ActividadesTecnicas\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\ActividadesTecnicas\Models\{WorkOrder, TechnicalAuditLog};

class WorkOrderService
{
    private function audit(WorkOrder $order, int $actor, string $action, array $old, array $new): void
    {
        TechnicalAuditLog::create(['user_id'=>$actor, 'action'=>$action, 'subject_type'=>WorkOrder::class,
            'subject_id'=>$order->id, 'old_values'=>$old, 'new_values'=>$new, 'created_at'=>CarbonImmutable::now('UTC')]);
    }

    public function create(array $data, int $actor): WorkOrder
    {
        return DB::transaction(function () use ($data, $actor) {
            // El ID autoincremental evita duplicados entre solicitudes simultáneas.
            $order = WorkOrder::create(array_merge($data, ['order_number'=>'OT-'.Str::ulid(), 'status'=>'new', 'created_by'=>$actor]));
            $order->update(['order_number'=>'OT-'.now()->format('Ymd').'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
            $order->statusHistory()->create(['from_status'=>null,'to_status'=>'new','changed_by'=>$actor,
                'changed_at'=>CarbonImmutable::now('UTC'),'notes'=>'Orden creada']);
            $this->audit($order, $actor, 'crear_orden', [], $order->only(['order_number','title','priority','status','scheduled_at']));
            return $order;
        });
    }

    public function update(WorkOrder $order, array $data, int $actor): WorkOrder
    {
        return DB::transaction(function () use ($order, $data, $actor) {
            $order = WorkOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === 'cancelled') { throw ValidationException::withMessages(['order'=>'Una orden cancelada no se puede editar.']); }
            $old = $order->only(array_keys($data));
            $order->fill($data);
            if ($order->isDirty()) {
                $order->save();
                $this->audit($order, $actor, 'editar_orden', $old, $order->only(array_keys($data)));
            }
            return $order;
        });
    }

    public function cancel(WorkOrder $order, int $actor): WorkOrder
    {
        return DB::transaction(function () use ($order, $actor) {
            $order = WorkOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === 'cancelled') { return $order; }
            if ($order->status !== 'new') { throw ValidationException::withMessages(['order'=>'Solo se pueden cancelar órdenes nuevas desde esta pantalla.']); }
            $order->update(['status'=>'cancelled','closed_at'=>CarbonImmutable::now('UTC')]);
            $order->statusHistory()->create(['from_status'=>'new','to_status'=>'cancelled','changed_by'=>$actor,
                'changed_at'=>CarbonImmutable::now('UTC'),'notes'=>'Orden cancelada']);
            $this->audit($order, $actor, 'cancelar_orden', ['status'=>'new'], ['status'=>'cancelled']);
            return $order;
        });
    }
}
