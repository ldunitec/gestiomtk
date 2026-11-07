<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Modelo
{
    protected $table = 'at_service_types';
    protected $fillable = ['name', 'description', 'default_duration_minutes', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'default_duration_minutes' => 'integer'];
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class, 'service_type_id'); }
}
