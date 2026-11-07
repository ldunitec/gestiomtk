<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderMaterial extends Modelo
{
    protected $table = 'at_work_order_materials';
    protected $fillable = ['work_order_id', 'technician_id', 'material_name', 'sku', 'quantity', 'unit', 'unit_cost', 'notes'];
    protected $casts = ['quantity' => 'decimal:3', 'unit_cost' => 'decimal:2'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function technician(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'technician_id'); }
}
