<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderGpsLog extends Modelo
{
    protected $table = 'at_work_order_gps_logs';
    protected $fillable = ['work_order_id', 'technician_id', 'event', 'latitude', 'longitude', 'accuracy_m', 'recorded_at', 'source'];
    protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'accuracy_m' => 'decimal:2', 'recorded_at' => 'datetime'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function technician(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'technician_id'); }
}
