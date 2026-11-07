<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderApproval extends Modelo
{
    protected $table = 'at_work_order_approvals';
    protected $fillable = ['work_order_id', 'approved_by_name', 'approved_by_user_id', 'decision', 'comments', 'signature_path', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function approver(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'approved_by_user_id'); }
}
