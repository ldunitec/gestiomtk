<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderEvidence extends Modelo
{
    protected $table = 'at_work_order_evidences';
    protected $fillable = ['work_order_id', 'uploaded_by', 'kind', 'disk', 'path', 'original_name', 'mime_type', 'size_bytes', 'caption', 'captured_at', 'latitude', 'longitude'];
    protected $casts = ['captured_at' => 'datetime', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'size_bytes' => 'integer'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'uploaded_by'); }
}
