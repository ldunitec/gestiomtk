<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceClient extends Modelo
{
    protected $table = 'at_service_clients';
    protected $fillable = ['name', 'tax_id', 'contact_name', 'phone', 'email', 'address', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];
    public function locations(): HasMany { return $this->hasMany(ServiceLocation::class, 'client_id'); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class, 'client_id'); }
}
