<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Modelo
{
    protected $table = 'at_work_orders';
    protected $fillable = [
        'order_number', 'client_id', 'location_id', 'service_type_id', 'title',
        'description', 'priority', 'status', 'scheduled_at', 'due_at',
        'created_by', 'closed_at', 'completion_notes',
    ];

    protected $casts = ['scheduled_at' => 'datetime', 'due_at' => 'datetime', 'closed_at' => 'datetime'];

    public function statusLabel(): string
    {
        return ['new'=>'Nueva','assigned'=>'Asignada','accepted'=>'Aceptada','en_route'=>'En camino',
            'in_progress'=>'En ejecución','paused'=>'En pausa','completed'=>'Completada','under_review'=>'En revisión',
            'approved'=>'Aprobada','not_completed'=>'No completada','cancelled'=>'Cancelada'][$this->status] ?? $this->status;
    }

    public function priorityLabel(): string
    {
        return ['low'=>'Baja','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente'][$this->priority] ?? $this->priority;
    }

    public function scheduledLocal(string $format = 'd/m/Y H:i'): ?string
    {
        return $this->scheduled_at?->copy()->timezone(config('app.timezone', 'America/Tegucigalpa'))->format($format);
    }

    public function agenda(): BelongsTo { return $this->belongsTo(\Modules\Produccion\Entities\Agenda::class, 'agenda_id'); }
    public function client(): BelongsTo { return $this->belongsTo(ServiceClient::class, 'client_id'); }
    public function location(): BelongsTo { return $this->belongsTo(ServiceLocation::class, 'location_id'); }
    public function serviceType(): BelongsTo { return $this->belongsTo(ServiceType::class, 'service_type_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
    public function assignments(): HasMany { return $this->hasMany(WorkOrderAssignment::class); }
    public function timeLogs(): HasMany { return $this->hasMany(WorkOrderTimeLog::class); }
    public function gpsLogs(): HasMany { return $this->hasMany(WorkOrderGpsLog::class); }
    public function evidences(): HasMany { return $this->hasMany(WorkOrderEvidence::class); }
    public function materials(): HasMany { return $this->hasMany(WorkOrderMaterial::class); }
    public function approvals(): HasMany { return $this->hasMany(WorkOrderApproval::class); }
    public function statusHistory(): HasMany { return $this->hasMany(WorkOrderStatusHistory::class); }
}

