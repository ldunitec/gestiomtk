<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderTimeLog extends Modelo
{
    protected $table = 'at_work_order_time_logs';
    protected $fillable = ['work_order_id', 'technician_id', 'event', 'started_at', 'ended_at', 'duration_seconds', 'notes'];
    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime', 'duration_seconds' => 'integer'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function technician(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'technician_id'); }
}
