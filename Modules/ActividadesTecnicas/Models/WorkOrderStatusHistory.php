<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderStatusHistory extends Modelo
{
    protected $table = 'at_work_order_status_history';
    protected $fillable = ['work_order_id', 'from_status', 'to_status', 'changed_by', 'notes', 'changed_at'];
    protected $casts = ['changed_at' => 'datetime'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function user(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'changed_by'); }
}
