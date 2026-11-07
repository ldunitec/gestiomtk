<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceLocation extends Modelo
{
    protected $table = 'at_service_locations';
    protected $fillable = ['client_id', 'name', 'address', 'latitude', 'longitude', 'geofence_radius_m', 'contact_name', 'contact_phone', 'notes', 'is_active'];
    protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'is_active' => 'boolean'];
    public function client(): BelongsTo { return $this->belongsTo(ServiceClient::class, 'client_id'); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class, 'location_id'); }
}
