<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderAssignment extends Modelo
{
    protected $table = 'at_work_order_assignments';
    protected $fillable = ['work_order_id', 'technician_id', 'assigned_by', 'assigned_at', 'accepted_at', 'notes'];
    protected $casts = ['assigned_at' => 'datetime', 'accepted_at' => 'datetime'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function technician(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'technician_id'); }
    public function assigner(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'assigned_by'); }
}
